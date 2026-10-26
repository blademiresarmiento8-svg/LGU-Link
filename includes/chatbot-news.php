<?php
/**
 * What the citizen chatbot knows about the news and announcements.
 *
 * For each question it picks a handful of posts and describes them to the AI, with today's date and
 * an exact status for each (posted, no longer posted, and for events: upcoming, happening now or
 * ended), so "is the program still on?" is answered from facts and not from guesses:
 *   1. every event that is upcoming or happening now,
 *   2. the posts that best match the words of the question (and of the last few messages, so a
 *      follow-up like "when is it?" still finds the right post), including ended ones,
 *   3. the newest posts, for "what's new?".
 * Only posts the public could see are used: nothing from drafts, posts waiting for review, scheduled
 * posts that are not live yet, or demo data. Posts that were taken down stay known for 90 days.
 */

require_once __DIR__ . '/../config/mail.php'; // APP_URL, for the path of the News links
require_once __DIR__ . '/news-helpers.php';

/** How long a post that is no longer on the site is still known to the chatbot. */
const CHATBOT_NEWS_ENDED_DAYS = 90;
/** Most posts described in one answer, to keep each question quick and cheap. */
const CHATBOT_NEWS_MAX_POSTS = 20;

/**
 * Words of the question that are worth searching for. Common filler words are dropped, and a few
 * Tagalog words are widened to the English words posts are usually written with.
 *
 * @param string[] $texts the current question first, then earlier ones
 * @return string[]
 */
function chatbotNewsTerms(array $texts): array
{
    static $stop = [
        'the', 'and', 'for', 'are', 'was', 'were', 'what', 'when', 'where', 'who', 'how', 'why', 'which', 'this', 'that', 'there', 'about', 'any',
        'have', 'has', 'can', 'could', 'would', 'should', 'will', 'does', 'did', 'you', 'your', 'our', 'with', 'from', 'tell', 'please', 'know',
        'ano', 'ang', 'mga', 'nga', 'ito', 'iyon', 'kailan', 'saan', 'paano', 'sino', 'bakit', 'may', 'ba', 'po', 'opo', 'pa', 'na', 'ng', 'sa',
        'ako', 'ko', 'mo', 'namin', 'natin', 'para', 'kung', 'ay', 'at', 'yung', 'ung', 'din', 'rin', 'lang', 'pwede', 'puwede', 'gusto',
        'news', 'announcement', 'announcements', 'balita', 'anunsyo', 'program', 'event', 'activity', 'latest', 'new', 'recent', 'update', 'updates',
    ];
    static $widen = [
        'ayuda' => ['assistance', 'aid', 'financial'], 'bakuna' => ['vaccine', 'vaccination'], 'libre' => ['free'],
        'medikal' => ['medical'], 'trabaho' => ['job', 'employment'], 'pamamahagi' => ['distribution'], 'sunog' => ['fire'],
        'baha' => ['flood'], 'bagyo' => ['typhoon'], 'tubig' => ['water'], 'kuryente' => ['power', 'electric'], 'pista' => ['fiesta'],
        'kalusugan' => ['health'], 'edukasyon' => ['education', 'scholarship'], 'pautang' => ['loan'], 'buwis' => ['tax'],
    ];

    $terms = [];
    foreach ($texts as $position => $text) {
        foreach (preg_split('/[^\p{L}\p{N}]+/u', mb_strtolower($text), -1, PREG_SPLIT_NO_EMPTY) as $word) {
            if (mb_strlen($word) < 3 || in_array($word, $stop, true)) {
                continue;
            }
            $terms[$word] = true;
            foreach ($widen[$word] ?? [] as $extra) {
                $terms[$extra] = true;
            }
        }
        if ($position >= 2) {
            break; // the current question and the two before it
        }
    }

    return array_slice(array_keys($terms), 0, 12);
}

/**
 * Every post the chatbot may talk about: live now, or taken down within the last 90 days. Newest first.
 *
 * @return array<int, array<string, mixed>>
 */
function chatbotNewsPool(): array
{
    $stmt = getDbConnection()->query(
        NEWS_SELECT . '
         WHERE n.is_demo = 0
           AND COALESCE(n.go_live_at, n.published_at) <= NOW()
           AND (n.show_until IS NULL OR n.show_until > NOW() - INTERVAL ' . CHATBOT_NEWS_ENDED_DAYS . ' DAY)
         ORDER BY display_at DESC, n.id DESC LIMIT 300'
    );

    return $stmt->fetchAll();
}

/** Where a post is right now, in words the AI can repeat: ['posted' | 'taken down', event state or null]. */
function chatbotNewsState(array $post): array
{
    $now = date('Y-m-d H:i:s');
    $takenDown = !empty($post['show_until']) && $post['show_until'] <= $now;
    $event = null;
    if (!empty($post['is_activity']) && !empty($post['starts_at']) && !empty($post['ends_at'])) {
        $event = activityStatus($post); // upcoming | happening | ended
    }

    return [$takenDown ? 'taken down' : 'posted', $event];
}

/**
 * The posts to describe for this question.
 *
 * @param string[] $texts the question first, then earlier messages
 * @return array<int, array<string, mixed>>
 */
function chatbotNewsSelect(array $texts): array
{
    $pool = chatbotNewsPool();
    $now = date('Y-m-d H:i:s');
    $chosen = [];
    $add = function (array $post) use (&$chosen): void {
        if (count($chosen) < CHATBOT_NEWS_MAX_POSTS) {
            $chosen[(int) $post['id']] = $post;
        }
    };

    // 1. Events that have not finished.
    $events = array_filter($pool, fn($p) => !empty($p['is_activity']) && !empty($p['ends_at']) && $p['ends_at'] > $now);
    usort($events, fn($a, $b) => strcmp((string) $a['starts_at'], (string) $b['starts_at']));
    foreach (array_slice($events, 0, 8) as $post) {
        $add($post);
    }

    // 2. The posts that match the words asked about (ended ones included).
    $terms = chatbotNewsTerms($texts);
    if ($terms) {
        $scored = [];
        foreach ($pool as $post) {
            $title = mb_strtolower((string) $post['title']);
            $body = mb_strtolower($post['description'] . ' ' . $post['category'] . ' ' . ($post['department_name'] ?? '') . ' ' . ($post['venue'] ?? '') . ' ' . ($post['requirements'] ?? ''));
            $score = 0;
            foreach ($terms as $term) {
                $score += (str_contains($title, $term) ? 4 : 0) + (str_contains($body, $term) ? 1 : 0);
            }
            if ($score > 0) {
                $scored[] = [$score, $post];
            }
        }
        usort($scored, fn($a, $b) => $b[0] <=> $a[0] ?: strcmp((string) $b[1]['display_at'], (string) $a[1]['display_at']));
        foreach (array_slice($scored, 0, 8) as [, $post]) {
            $add($post);
        }
    }

    // 3. The newest posts that are still up, for "what's new?".
    $live = array_filter($pool, fn($p) => empty($p['show_until']) || $p['show_until'] > $now);
    foreach (array_slice(array_values($live), 0, 6) as $post) {
        $add($post);
    }

    return array_values($chosen);
}

/** One post, described on a few lines. */
function chatbotNewsDescribe(array $post): string
{
    [$visibility, $event] = chatbotNewsState($post);
    $label = newsTypeLabel($post['post_type']);
    $path = (string) parse_url(APP_URL, PHP_URL_PATH) . '/news-post.php?id=' . (int) $post['id'];

    $lines = ['[' . $label . '] ' . $post['title'] . ' - ' . newsPosterName($post)];
    $status = 'Posted on ' . date('l, M j, Y', strtotime((string) $post['display_at'])) . '. ';
    if ($visibility === 'taken down') {
        $status .= 'No longer posted on the website (taken down ' . date('M j, Y', strtotime($post['show_until'])) . ').';
    } elseif (!empty($post['show_until'])) {
        $status .= 'Shown on the website until ' . date('M j, Y, g:i A', strtotime($post['show_until'])) . '.';
    } else {
        $status .= 'Still posted on the website.';
    }
    $lines[] = '  Status: ' . $status;

    if ($event !== null) {
        $words = ['upcoming' => 'UPCOMING (has not started)', 'happening' => 'HAPPENING NOW', 'ended' => 'ALREADY ENDED'][$event];
        $lines[] = '  Event: ' . $words . '. When: ' . formatActivityRange($post['starts_at'], $post['ends_at']) . (!empty($post['venue']) ? '. Where: ' . $post['venue'] : '');
        $requirements = newsRequirementsList($post['requirements'] ?? '');
        if ($requirements) {
            $lines[] = '  Requirements: ' . implode('; ', $requirements);
        }
        if (!empty($post['contact_info'])) {
            $lines[] = '  Contact: ' . $post['contact_info'];
        }
    }
    $lines[] = '  Details: ' . newsExcerpt((string) $post['description'], 600);
    $lines[] = '  Link: ' . $path;

    return implode("\n", $lines);
}

/**
 * The "News and announcements" section for the AI's instructions, for this question.
 *
 * @param string[] $texts the question first, then earlier messages
 */
function chatbotNewsSection(array $texts): string
{
    $posts = chatbotNewsSelect($texts);
    $lines = [
        '=== News and announcements ===',
        'Today is ' . date('l, F j, Y, g:i A') . ' (Philippine time).',
        '',
        'How to answer questions about news, announcements, programs, events or activities:',
        '- Use only the posts listed below. Never invent a program, date, place, requirement or amount. If nothing below matches, say it is not in the posted news and suggest the News page (' . (string) parse_url(APP_URL, PHP_URL_PATH) . '/news.php) or the office concerned.',
        '- Every post shows its status. Say plainly whether a program is upcoming, happening now, or already ended, by comparing with today\'s date.',
        '- Some posts name no date, only a day or a time such as "this Friday", "tomorrow" or "next week". That always counts from the day the post was posted (shown on its Status line), not from today. Work out the real date from the posted date, state that date, then compare it with today and say clearly if it has already passed. Never say "this week" or "upcoming" for something that has already happened.',
        '- A post that is "no longer posted" has been taken down from the website, so say it is no longer posted, and that the citizen can ask the office whether anything is still open.',
        '- When you mention a post, give its link on its own line, exactly as listed (it starts with a slash).',
        '- Do not decide whether a person qualifies for a program; tell them the posted requirements and to confirm with the office.',
        '',
    ];
    if (!$posts) {
        $lines[] = 'There are no posts to list right now.';
    }
    foreach ($posts as $post) {
        $lines[] = chatbotNewsDescribe($post);
        $lines[] = '';
    }

    return implode("\n", $lines);
}

/** Words that mean the citizen is asking about the news (used by the backup answer when the AI is unavailable). */
function chatbotNewsLooksRelevant(string $message): bool
{
    return (bool) preg_match('/\b(news|announce|announcement|program|programs|event|events|activity|activities|ayuda|balita|anunsyo|assistance|distribution|mission|fiesta|schedule)\b/iu', $message);
}

/**
 * A plain answer from the news without the AI: up to 3 posts that match, with their status and link.
 * Returns null when nothing matches.
 */
function chatbotNewsFallbackReply(string $message, bool $force = false): ?string
{
    if (!$force && !chatbotNewsLooksRelevant($message)) {
        return null;
    }
    $terms = chatbotNewsTerms([$message]);
    $hits = [];
    foreach (chatbotNewsSelect([$message]) as $post) {
        $title = mb_strtolower((string) $post['title'] . ' ' . $post['description']);
        $match = false;
        foreach ($terms as $term) {
            $match = $match || str_contains($title, $term);
        }
        if ($match || !$terms) {
            $hits[] = $post;
        }
        if (count($hits) === 3) {
            break;
        }
    }
    if (!$hits) {
        return $force ? null : "I couldn't find that in the posted news and announcements. You can read everything we have posted on the News page: " . (string) parse_url(APP_URL, PHP_URL_PATH) . '/news.php';
    }

    $out = ["Here is what I found in the news and announcements:"];
    foreach ($hits as $post) {
        [$visibility, $event] = chatbotNewsState($post);
        $status = $visibility === 'taken down' ? 'no longer posted' : 'posted';
        if ($event !== null) {
            $status = ['upcoming' => 'upcoming', 'happening' => 'happening now', 'ended' => 'already ended'][$event];
        }
        $out[] = '';
        $out[] = '- ' . $post['title'] . ' (' . $status . ')' . ($event !== null ? ', ' . formatActivityRange($post['starts_at'], $post['ends_at']) : '');
        $out[] = (string) parse_url(APP_URL, PHP_URL_PATH) . '/news-post.php?id=' . (int) $post['id'];
    }
    $out[] = '';
    $out[] = '(Please confirm with the office, since details can change.)';

    return implode("\n", $out);
}
