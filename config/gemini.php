<?php
/**
 * Google Gemini configuration for the LLM-backed chatbot.
 *
 * To enable it: create config/gemini-key.local.php (same folder) containing
 *   <?php define('GEMINI_API_KEY', 'your-key');
 * with a key from https://aistudio.google.com/apikey. That file is gitignored.
 *
 * Without a key the chatbot tries the Anthropic key (config/anthropic.php),
 * and without either it falls back to the rule-based Citizen's Charter
 * matcher in user/chatbot-api.php.
 */

// Google retires models for new accounts over time; if the chatbot suddenly
// falls back to the rule-based answers, check the server error log and pick a
// currently available model name from AI Studio.
define('GEMINI_MODEL', 'gemini-3.1-flash-lite');

$localKeyFile = __DIR__ . '/gemini-key.local.php';
if (file_exists($localKeyFile)) {
    require_once $localKeyFile;
}

if (!defined('GEMINI_API_KEY')) {
    define('GEMINI_API_KEY', '');
}
