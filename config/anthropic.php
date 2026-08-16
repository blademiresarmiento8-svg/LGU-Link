<?php
/**
 * Anthropic API configuration for the LLM-backed chatbot.
 *
 * To enable the smart chatbot: copy config/anthropic-key.local.example.php
 * to config/anthropic-key.local.php and paste in your API key from
 * https://console.anthropic.com/settings/keys. That file is gitignored.
 *
 * Without a key, the chatbot automatically falls back to the rule-based
 * Citizen's Charter matcher in chatbot-api.php.
 */

define('ANTHROPIC_MODEL', 'claude-haiku-4-5');

$localKeyFile = __DIR__ . '/anthropic-key.local.php';
if (file_exists($localKeyFile)) {
    require_once $localKeyFile;
}

if (!defined('ANTHROPIC_API_KEY')) {
    define('ANTHROPIC_API_KEY', '');
}
