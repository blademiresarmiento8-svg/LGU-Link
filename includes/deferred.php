<?php
/**
 * Lets a request answer the browser first and do the slow network work (emails,
 * the live-update signal to Pusher) afterwards.
 *
 * Sending an email takes several seconds on a slow connection, and a single
 * action can send several. Done before the redirect, the person waits for all of
 * them. A handler opts in with
 *     define('DEFER_SLOW_TASKS', true);
 * at the top, and finishes with redirectAndRunDeferred('page.php?done=1') instead
 * of header('Location: ...'); exit;. Anything queued with runOrDefer() meanwhile
 * runs once the browser already has its answer. Without the opt-in, tasks simply
 * run immediately, as before.
 */

/** @var callable[] $GLOBALS['__deferred_tasks'] */
$GLOBALS['__deferred_tasks'] = [];

function runOrDefer(callable $task): void
{
    if (defined('DEFER_SLOW_TASKS') && DEFER_SLOW_TASKS) {
        $GLOBALS['__deferred_tasks'][] = $task;

        return;
    }

    $task();
}

/**
 * Redirects the browser right away, then runs every queued task. The session is
 * released first: PHP locks it while a script runs, so holding it would make the
 * page the browser loads next wait for the background work to finish.
 */
function redirectAndRunDeferred(string $url): never
{
    ignore_user_abort(true);
    @set_time_limit(300);
    session_write_close();

    header('Location: ' . $url);
    header('Connection: close');
    header('Content-Length: 0');
    while (ob_get_level() > 0) {
        ob_end_flush();
    }
    flush();
    if (function_exists('fastcgi_finish_request')) {
        fastcgi_finish_request();
    }

    $tasks = $GLOBALS['__deferred_tasks'];
    $GLOBALS['__deferred_tasks'] = [];
    foreach ($tasks as $task) {
        try {
            $task();
        } catch (\Throwable $e) {
            error_log('Deferred task failed: ' . $e->getMessage());
        }
    }

    exit;
}
