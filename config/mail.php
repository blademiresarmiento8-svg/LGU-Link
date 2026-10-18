<?php
/**
 * SMTP configuration for outgoing appointment notification emails.
 *
 * Reads SMTP_HOST, SMTP_PORT, SMTP_USER, SMTP_PASS from a .env file in the
 * project root (see .env.example) via vlucas/phpdotenv. Without a
 * configured .env, MAIL_CONFIGURED is false and includes/mailer.php
 * silently no-ops — same graceful-degradation approach as
 * config/anthropic.php for the chatbot's API key, so the appointment
 * workflow still works end-to-end even if email hasn't been set up yet.
 */

$autoload = __DIR__ . '/../vendor/autoload.php';
if (file_exists($autoload)) {
    require_once $autoload;
}

if (class_exists(\Dotenv\Dotenv::class)) {
    \Dotenv\Dotenv::createImmutable(dirname(__DIR__))->safeLoad();
}

/**
 * phpdotenv populates $_ENV/$_SERVER by default but not getenv()'s process
 * environment table (it skips putenv() for thread-safety on non-CLI
 * SAPIs), so read from those directly rather than getenv().
 */
function envValue(string $key): string
{
    return (string) ($_ENV[$key] ?? $_SERVER[$key] ?? '');
}

define('SMTP_HOST', envValue('SMTP_HOST'));
define('SMTP_PORT', (int) envValue('SMTP_PORT'));
define('SMTP_USER', envValue('SMTP_USER'));
define('SMTP_PASS', envValue('SMTP_PASS'));

const MAIL_FROM_NAME = 'LGU Norzagaray Portal';

define('MAIL_CONFIGURED', SMTP_HOST !== '' && SMTP_PORT > 0 && SMTP_USER !== '' && SMTP_PASS !== '');

// Public address of the portal, used to build links inside emails. Set APP_URL
// in .env when the site is hosted somewhere other than localhost.
define('APP_URL', rtrim(envValue('APP_URL') ?: 'http://localhost/LGU-Link', '/'));
