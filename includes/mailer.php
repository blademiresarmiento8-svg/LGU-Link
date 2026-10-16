<?php
/**
 * Outgoing appointment notification emails via SMTP (PHPMailer). Requires
 * config/mail.php to already be loaded. Every call is wrapped so a mail
 * failure — or SMTP simply not being configured yet — never breaks the
 * underlying appointment action (submit/forward/reject/schedule all
 * already succeeded in the database before this runs). Same fail-soft
 * philosophy as the chatbot's Claude integration.
 */

use PHPMailer\PHPMailer\Exception as PHPMailerException;
use PHPMailer\PHPMailer\PHPMailer;

function sendAppointmentEmail(string $to, string $subject, string $bodyHtml): bool
{
    if (!MAIL_CONFIGURED) {
        return false;
    }

    $autoload = __DIR__ . '/../vendor/autoload.php';
    if (!file_exists($autoload)) {
        return false;
    }
    require_once $autoload;

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USER;
        $mail->Password = SMTP_PASS;
        $mail->SMTPSecure = SMTP_PORT === 465 ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = SMTP_PORT;

        $mail->setFrom(SMTP_USER, MAIL_FROM_NAME);
        $mail->addAddress($to);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $bodyHtml;
        $mail->AltBody = trim(strip_tags(str_replace(['<br>', '<br/>', '<br />', '</p>', '</tr>'], "\n", $bodyHtml)));

        $mail->send();

        return true;
    } catch (PHPMailerException | \Throwable $e) {
        error_log('Appointment email failed: ' . $e->getMessage());

        return false;
    }
}

/**
 * A small shared HTML shell for every appointment notification email, so
 * each trigger point only has to supply a heading, an intro line, and a
 * label => value list of details. Every value is escaped here — email
 * clients don't execute JavaScript, but unescaped citizen-submitted text
 * (e.g. a title containing a stray closing tag) could still break the
 * template's layout, so this stays defensive regardless.
 */
function renderAppointmentEmailHtml(string $heading, string $intro, array $rows): string
{
    $rowsHtml = '';
    foreach ($rows as $label => $value) {
        if ($value === null || $value === '') {
            continue;
        }
        $rowsHtml .= '<tr>'
            . '<td style="padding:6px 12px;color:#64748b;font-size:13px;vertical-align:top;white-space:nowrap;">' . htmlspecialchars((string) $label) . '</td>'
            . '<td style="padding:6px 12px;font-size:13px;color:#0f172a;">' . nl2br(htmlspecialchars((string) $value)) . '</td>'
            . '</tr>';
    }

    return '
        <div style="font-family: \'Segoe UI\', Arial, sans-serif; max-width: 480px; margin: 0 auto;">
          <div style="background:#2563eb;color:#ffffff;padding:16px 20px;border-radius:8px 8px 0 0;font-weight:700;">
            LGU Norzagaray Portal
          </div>
          <div style="border:1px solid #e2e8f0;border-top:none;padding:20px;border-radius:0 0 8px 8px;">
            <h2 style="margin:0 0 10px;color:#0f172a;font-size:18px;">' . htmlspecialchars($heading) . '</h2>
            <p style="color:#334155;font-size:14px;margin:0 0 14px;">' . nl2br(htmlspecialchars($intro)) . '</p>
            <table style="width:100%;border-collapse:collapse;">' . $rowsHtml . '</table>
            <p style="color:#94a3b8;font-size:12px;margin-top:20px;">This is an automated message from the LGU Norzagaray citizen portal. Please do not reply directly to this email.</p>
          </div>
        </div>
    ';
}
