<?php
/**
 * Citizen sign-up: prove the email address with a one-time code first, then
 * collect personal details. Used by auth/register.php.
 *
 * The code is a 6-digit number, stored only as a hash, valid for 10 minutes, and
 * killed after 5 wrong guesses. Requests for codes are rate-limited per email and
 * per device. Creating the account re-checks the verification in the database, so
 * skipping a step in the browser can't skip the check.
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/mail.php';
require_once __DIR__ . '/mailer.php';
require_once __DIR__ . '/deferred.php';
require_once __DIR__ . '/password-rules.php';

const OTP_VALID_MINUTES = 10;
const OTP_MAX_WRONG_GUESSES = 5;
const OTP_RESEND_SECONDS = 60;
const OTP_MAX_PER_EMAIL_PER_HOUR = 5;
const OTP_MAX_PER_IP_PER_HOUR = 10;
/** After the code is accepted, the person has this long to finish the details form. */
const REGISTRATION_WINDOW_MINUTES = 30;

function normalizeEmail(string $email): string
{
    return strtolower(trim($email));
}

/** Seconds until another code may be requested for this email (0 = now). */
function secondsUntilResend(string $email): int
{
    $stmt = getDbConnection()->prepare(
        'SELECT TIMESTAMPDIFF(SECOND, created_at, NOW()) FROM email_verifications WHERE email = :email ORDER BY id DESC LIMIT 1'
    );
    $stmt->execute(['email' => $email]);
    $age = $stmt->fetchColumn();

    return $age === false ? 0 : max(0, OTP_RESEND_SECONDS - (int) $age);
}

/**
 * Creates a fresh code for the email and queues it for sending.
 *
 * @return array{success: bool, error: string}
 */
function requestEmailCode(string $email, string $ip): array
{
    $email = normalizeEmail($email);
    $pdo = getDbConnection();

    if ($email === '' || strlen($email) > 150 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['success' => false, 'error' => 'Please enter a valid email address.'];
    }
    if (!MAIL_CONFIGURED) {
        return ['success' => false, 'error' => 'Email is not set up on this server, so accounts cannot be verified. Please contact the municipal office.'];
    }

    $exists = $pdo->prepare('SELECT 1 FROM users WHERE email = :email LIMIT 1');
    $exists->execute(['email' => $email]);
    if ($exists->fetchColumn()) {
        return ['success' => false, 'error' => 'An account with that email already exists. Try signing in instead.'];
    }

    $wait = secondsUntilResend($email);
    if ($wait > 0) {
        return ['success' => false, 'error' => "A code was just sent. You can ask for another in $wait seconds."];
    }

    $perEmail = $pdo->prepare('SELECT COUNT(*) FROM email_verifications WHERE email = :email AND created_at > NOW() - INTERVAL 1 HOUR');
    $perEmail->execute(['email' => $email]);
    $perIp = $pdo->prepare('SELECT COUNT(*) FROM email_verifications WHERE ip_address = :ip AND created_at > NOW() - INTERVAL 1 HOUR');
    $perIp->execute(['ip' => $ip]);
    if ((int) $perEmail->fetchColumn() >= OTP_MAX_PER_EMAIL_PER_HOUR || (int) $perIp->fetchColumn() >= OTP_MAX_PER_IP_PER_HOUR) {
        return ['success' => false, 'error' => 'Too many codes were requested. Please try again in an hour.'];
    }

    // Tidy up: anything older than a day is useless.
    $pdo->exec('DELETE FROM email_verifications WHERE created_at < NOW() - INTERVAL 1 DAY');

    $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    $pdo->prepare(
        'INSERT INTO email_verifications (email, code_hash, expires_at, ip_address)
         VALUES (:email, :hash, NOW() + INTERVAL ' . OTP_VALID_MINUTES . ' MINUTE, :ip)'
    )->execute(['email' => $email, 'hash' => password_hash($code, PASSWORD_DEFAULT), 'ip' => $ip]);

    queueAppointmentEmail($email, 'Your LGU Norzagaray verification code', renderVerificationEmailHtml($code));

    return ['success' => true, 'error' => ''];
}

/**
 * Checks a code the person typed.
 *
 * @return array{success: bool, error: string}
 */
function verifyEmailCode(string $email, string $code): array
{
    $email = normalizeEmail($email);
    $code = preg_replace('/\s+/', '', $code);
    $pdo = getDbConnection();

    if (!preg_match('/^\d{6}$/', $code)) {
        return ['success' => false, 'error' => 'Enter the 6-digit code from the email.'];
    }

    $stmt = $pdo->prepare(
        'SELECT * FROM email_verifications WHERE email = :email AND verified_at IS NULL AND expires_at > NOW() ORDER BY id DESC LIMIT 1'
    );
    $stmt->execute(['email' => $email]);
    $row = $stmt->fetch();

    if (!$row) {
        return ['success' => false, 'error' => 'That code has expired. Please ask for a new one.'];
    }
    if ((int) $row['attempts'] >= OTP_MAX_WRONG_GUESSES) {
        return ['success' => false, 'error' => 'Too many wrong tries. Please ask for a new code.'];
    }

    if (!password_verify($code, $row['code_hash'])) {
        $pdo->prepare('UPDATE email_verifications SET attempts = attempts + 1 WHERE id = :id')->execute(['id' => $row['id']]);
        $left = OTP_MAX_WRONG_GUESSES - (int) $row['attempts'] - 1;

        return ['success' => false, 'error' => $left > 0
            ? "That code is not right. You have $left " . ($left === 1 ? 'try' : 'tries') . ' left.'
            : 'Too many wrong tries. Please ask for a new code.'];
    }

    $pdo->prepare('UPDATE email_verifications SET verified_at = NOW() WHERE id = :id')->execute(['id' => $row['id']]);

    return ['success' => true, 'error' => ''];
}

/** Was this email's code accepted recently enough to let the person finish signing up? */
function isEmailVerified(string $email): bool
{
    $stmt = getDbConnection()->prepare(
        'SELECT 1 FROM email_verifications WHERE email = :email AND verified_at > NOW() - INTERVAL ' . REGISTRATION_WINDOW_MINUTES . ' MINUTE LIMIT 1'
    );
    $stmt->execute(['email' => normalizeEmail($email)]);

    return (bool) $stmt->fetchColumn();
}

/** 09XXXXXXXXX, or +639XXXXXXXXX / 639XXXXXXXXX (spaces and dashes allowed); returned as 09XXXXXXXXX. */
function normalizePhilippineMobile(string $mobile): ?string
{
    $digits = preg_replace('/[\s\-()]/', '', $mobile);
    if (preg_match('/^09\d{9}$/', $digits)) {
        return $digits;
    }
    if (preg_match('/^\+?639(\d{9})$/', $digits, $m)) {
        return '09' . $m[1];
    }

    return null;
}

/**
 * Validates the details form.
 *
 * @return array{errors: string[], data: array<string, string>}
 */
function validateRegistrationDetails(array $input, string $email): array
{
    $clean = fn(string $key) => trim(preg_replace('/\s+/', ' ', (string) ($input[$key] ?? '')));
    $first = $clean('first_name');
    $middle = $clean('middle_name');
    $last = $clean('last_name');
    $suffix = $clean('suffix');
    $password = (string) ($input['password'] ?? '');
    $confirm = (string) ($input['confirm_password'] ?? '');
    $mobile = normalizePhilippineMobile((string) ($input['mobile'] ?? ''));

    // Letters (including accented ones), spaces, . ' - and digits are allowed (a few real names
    // contain a number), but a name must start with a letter and contain at least $minLetters
    // letters, so "1" or "11" are refused. A middle name may be just an initial.
    $nameOk = function (string $v, int $minLetters = 2): bool {
        return (bool) preg_match("/^[\\p{L}][\\p{L}\\d .'\\-]{0,59}$/u", $v) && preg_match_all('/\\p{L}/u', $v) >= $minLetters;
    };

    $errors = [];
    if (!$nameOk($first)) {
        $errors[] = 'Please enter a valid first name.';
    }
    if ($middle !== '' && !$nameOk($middle, 1)) {
        $errors[] = 'Please enter a valid middle name.';
    }
    if (!$nameOk($last)) {
        $errors[] = 'Please enter a valid last name.';
    }
    if ($suffix !== '' && !preg_match('/^[A-Za-z0-9.]{1,10}$/', $suffix)) {
        $errors[] = 'Please enter a valid suffix, such as Jr., III or 2nd.';
    }
    if ($mobile === null) {
        $errors[] = 'Please enter a valid mobile number, like 09123456789.';
    }
    if (($problem = passwordProblem($password)) !== null) {
        $errors[] = $problem;
    } elseif ($password !== $confirm) {
        $errors[] = 'Passwords do not match.';
    }
    if (empty($input['privacy'])) {
        $errors[] = 'Please agree to the Privacy Notice to create an account.';
    }

    $fullName = trim(implode(' ', array_filter([$first, $middle, $last, $suffix], fn($v) => $v !== '')));

    return [
        'errors' => $errors,
        'data' => ['first' => $first, 'middle' => $middle, 'last' => $last, 'suffix' => $suffix, 'mobile' => (string) $mobile, 'full_name' => $fullName, 'password' => $password],
    ];
}

/**
 * Creates the citizen account for a verified email.
 *
 * @return array{success: bool, error: string}
 */
function createCitizenAccount(string $email, array $data): array
{
    $email = normalizeEmail($email);
    $pdo = getDbConnection();

    if (!isEmailVerified($email)) {
        return ['success' => false, 'error' => 'Your email verification expired. Please start again.'];
    }

    try {
        $pdo->prepare(
            "INSERT INTO users (full_name, first_name, middle_name, last_name, suffix, mobile, email, password, privacy_consent_at, role)
             VALUES (:full_name, :first, :middle, :last, :suffix, :mobile, :email, :password, NOW(), 'user')"
        )->execute([
            'full_name' => $data['full_name'],
            'first' => $data['first'],
            'middle' => $data['middle'] === '' ? null : $data['middle'],
            'last' => $data['last'],
            'suffix' => $data['suffix'] === '' ? null : $data['suffix'],
            'mobile' => $data['mobile'],
            'email' => $email,
            'password' => password_hash($data['password'], PASSWORD_DEFAULT),
        ]);
    } catch (PDOException $e) {
        if ($e->getCode() === '23000') {
            return ['success' => false, 'error' => 'An account with that email already exists. Try signing in instead.'];
        }
        throw $e;
    }

    // The codes have done their job.
    $pdo->prepare('DELETE FROM email_verifications WHERE email = :email')->execute(['email' => $email]);

    return ['success' => true, 'error' => ''];
}

function renderVerificationEmailHtml(string $code): string
{
    return '
        <div style="font-family: \'Segoe UI\', Arial, sans-serif; max-width: 480px; margin: 0 auto;">
          <div style="background:#2563eb;color:#ffffff;padding:16px 20px;border-radius:8px 8px 0 0;font-weight:700;">LGU Norzagaray Portal</div>
          <div style="border:1px solid #e2e8f0;border-top:none;padding:24px 20px;border-radius:0 0 8px 8px;">
            <h2 style="margin:0 0 10px;color:#0f172a;font-size:18px;">Verify your email</h2>
            <p style="color:#334155;font-size:14px;margin:0 0 16px;">Use this code to continue creating your citizen account:</p>
            <p style="font-size:32px;letter-spacing:8px;font-weight:700;color:#1d4ed8;margin:0 0 16px;text-align:center;">' . htmlspecialchars($code) . '</p>
            <p style="color:#334155;font-size:13px;margin:0 0 6px;">It expires in ' . OTP_VALID_MINUTES . ' minutes. Never share it with anyone, and the LGU will never ask you for it.</p>
            <p style="color:#94a3b8;font-size:12px;margin:14px 0 0;">If you did not try to create an account, you can ignore this email.</p>
          </div>
        </div>
    ';
}
