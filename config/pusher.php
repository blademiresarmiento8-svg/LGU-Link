<?php
/**
 * Pusher Channels configuration, used to nudge an open page the moment a new
 * notification is saved (the notification itself always lives in the database
 * — Pusher is only the "go look" signal).
 *
 * Reads PUSHER_APP_ID, PUSHER_KEY, PUSHER_SECRET, PUSHER_CLUSTER from the
 * .env file (see .env.example). Without them, PUSHER_CONFIGURED is false and
 * the bell simply updates on page load instead — same graceful-degradation
 * approach as config/mail.php.
 */

require_once __DIR__ . '/mail.php'; // loads .env and defines envValue()

define('PUSHER_APP_ID', envValue('PUSHER_APP_ID'));
define('PUSHER_KEY', envValue('PUSHER_KEY'));
define('PUSHER_SECRET', envValue('PUSHER_SECRET'));
define('PUSHER_CLUSTER', envValue('PUSHER_CLUSTER') ?: 'ap1');

define('PUSHER_CONFIGURED', PUSHER_APP_ID !== '' && PUSHER_KEY !== '' && PUSHER_SECRET !== '');
