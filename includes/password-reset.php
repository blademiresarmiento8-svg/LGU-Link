<?php
/**
 * "Forgot your password?": the person proves they own the account's email with a 6-digit code, then picks a new
 * password. Works for every kind of account (citizen, office, Municipal Administrator, system administrator).
 * Used by auth/forgot-password.php.
 *
 * It reuses the sign-up code (includes/registration-helpers.php): the same table, the same 10-minute life and the
 * same 5-wrong-guesses limit. That is safe because sign-up refuses an email that already has an account, while a reset
 * only does anything for an email that has one.
 *
 * What it never gives away: whether an email has an account. The same answer comes back either way, and the limits
 * on how often a code can be asked for count every request, not just the ones for real accounts.
 */

require_once __DIR__ . '/registration-helpers.php';
require_once __DIR__ . '/rate-limit.php';
require_once __DIR__ . '/audit-helpers.php';

/** After the code is accepted, the person has this long to choose the new password. */
const RESET_WINDOW_MINUTES = 15;

function resetBucket(string $kind, string $email): string
{
    return 'pwreset:' . $kind . ':' . sha1($email);
}

/** Seconds until another code may be requested for this email (0 = now). */
function secondsUntilResetResend(string $email): int
{
    $stmt = getDbConnection()->prepare('SELECT TIMESTAMPDIFF(SECOND, window_start, NOW()) FROM rate_limits WHERE bucket = :b');
    $stmt->execute(['b' => resetBucket('wait', normalizeEmail($email))]);
    $age = $stmt->fetchColumn();

    return $age === false ? 0 : max(0, OTP_RESEND_SECONDS - (int) $age);
}

/**
 * Sends a reset code to the email when it belongs to an active account. The answer is the same whether or not it does.
 *
 * @return array{success: bool, error: string}
 */
function requestPasswordResetCode(string $email, string $ip): array
{
    $email = normalizeEmail($email);
    $pdo = getDbConnection();

    if ($email === '' || strlen($email) > 150 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['success' => false, 'error' => 'Please enter a valid email address.'];
    }
    if (!MAIL_CONFIGURED) {
        return ['success' => false, 'error' => 'Email is not set up on this server, so a code cannot be sent. Please contact the municipal office.'];
    }

    // Every request is counted, whether or not the email has an account, so the limits reveal nothing.
    if (!rateLimitAllow(resetBucket('wait', $email), 1, OTP_RESEND_SECONDS)) {
        return ['success' => false, 'error' => 'A code was just requested. Please wait a minute before asking for another.'];
    }
    if (!rateLimitAllow(resetBucket('hour', $email), OTP_MAX_PER_EMAIL_PER_HOUR, 3600) || !rateLimitAllow('pwreset:ip:' . $ip, OTP_MAX_PER_IP_PER_HOUR, 3600)) {
        return ['success' => false, 'error' => 'Too many codes were requested. Please try again in an hour.'];
    }
    rateLimitCleanup();

    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email AND status = 'active' LIMIT 1");
    $stmt->execute(['email' => $email]);
    if ($stmt->fetchColumn()) {
        // Anything older than a day is useless.
        $pdo->exec('DELETE FROM email_verifications WHERE created_at < NOW() - INTERVAL 1 DAY');

        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $pdo->prepare(
            'INSERT INTO email_verifications (email, code_hash, expires_at, ip_address)
             VALUES (:email, :hash, NOW() + INTERVAL ' . OTP_VALID_MINUTES . ' MINUTE, :ip)'
        )->execute(['email' => $email, 'hash' => password_hash($code, PASSWORD_DEFAULT), 'ip' => $ip]);

        queueAppointmentEmail($email, 'Your LGU Norzagaray password reset code', renderPasswordResetEmailHtml($code));
    }

    return ['success' => true, 'error' => ''];
}

/** Was this email's code accepted a moment ago (the reset window)? */
function isResetVerified(string $email): bool
{
    $stmt = getDbConnection()->prepare(
        'SELECT 1 FROM email_verifications WHERE email = :email AND verified_at > NOW() - INTERVAL ' . RESET_WINDOW_MINUTES . ' MINUTE LIMIT 1'
    );
    $stmt->execute(['email' => normalizeEmail($email)]);

    return (bool) $stmt->fetchColumn();
}

/**
 * Sets the new password for a verified email, then makes the code useless.
 *
 * @return array{success: bool, error: string}
 */
function completePasswordReset(string $email, string $password, string $confirm, string $ip): array
{
    $email = normalizeEmail($email);
    $pdo = getDbConnection();

    if (($problem = passwordProblem($password)) !== null) {
        return ['success' => false, 'error' => $problem];
    }
    if ($password !== $confirm) {
        return ['success' => false, 'error' => 'The two passwords do not match.'];
    }
    if (!isResetVerified($email)) {
        return ['success' => false, 'error' => 'Your verification expired. Please start again.'];
    }

    $stmt = $pdo->prepare("SELECT id, full_name, password FROM users WHERE email = :email AND status = 'active' LIMIT 1");
    $stmt->execute(['email' => $email]);
    $account = $stmt->fetch();
    if (!$account) {
        return ['success' => false, 'error' => 'Your verification expired. Please start again.'];
    }
    if (password_verify($password, $account['password'])) {
        return ['success' => false, 'error' => 'Choose a password different from your current one.'];
    }

    $pdo->prepare('UPDATE users SET password = :password, must_change_password = 0 WHERE id = :id')
        ->execute(['password' => password_hash($password, PASSWORD_DEFAULT), 'id' => $account['id']]);
    $pdo->prepare('DELETE FROM email_verifications WHERE email = :email')->execute(['email' => $email]);
    rateLimitReset('login:' . $ip . ':' . $email); // failed sign-in attempts no longer lock them out

    logAudit('password_reset_by_email', (int) $account['id'], $account['full_name'], 'The account owner chose a new password with an email code.');
    queueAppointmentEmail(
        $email,
        'Your LGU Norzagaray password was changed',
        renderAppointmentEmailHtml(
            'Password changed',
            'The password of your LGU Norzagaray Portal account was just changed using an email code. If this was you, there is nothing more to do.',
            ['If it was not you' => 'Please contact the municipal office or the system administrator right away.']
        )
    );

    return ['success' => true, 'error' => ''];
}

function renderPasswordResetEmailHtml(string $code): string
{
    return '
        <div style="font-family: \'Segoe UI\', Arial, sans-serif; max-width: 480px; margin: 0 auto;">
          <div style="background:#2563eb;color:#ffffff;padding:16px 20px;border-radius:8px 8px 0 0;font-weight:700;">LGU Norzagaray Portal</div>
          <div style="border:1px solid #e2e8f0;border-top:none;padding:24px 20px;border-radius:0 0 8px 8px;">
            <h2 style="margin:0 0 10px;color:#0f172a;font-size:18px;">Reset your password</h2>
            <p style="color:#334155;font-size:14px;margin:0 0 16px;">Use this code to choose a new password for your account:</p>
            <p style="font-size:32px;letter-spacing:8px;font-weight:700;color:#1d4ed8;margin:0 0 16px;text-align:center;">' . htmlspecialchars($code) . '</p>
            <p style="color:#334155;font-size:13px;margin:0 0 6px;">It expires in ' . OTP_VALID_MINUTES . ' minutes. Never share it with anyone, and the LGU will never ask you for it.</p>
            <p style="color:#94a3b8;font-size:12px;margin:14px 0 0;">If you did not ask to reset your password, you can ignore this email. Your password has not been changed.</p>
          </div>
        </div>
    ';
}
