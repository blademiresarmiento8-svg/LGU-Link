<?php
/**
 * Chatbot backend for the citizen portal.
 *
 * Primary path: calls an LLM with the full Citizen's Charter data, the
 * office/department list and the news and announcements that fit the question
 * (includes/chatbot-news.php) as system-prompt context, plus this session's
 * conversation history, so it understands natural phrasing and remembers
 * earlier turns. Google Gemini is used when config/gemini-key.local.php has a
 * key; Claude (Anthropic) when config/anthropic-key.local.php has one, either as the
 * only AI or as the backup when Gemini is unavailable.
 *
 * Fallback path: if neither key is configured or the API call fails,
 * degrades to the original rule-based keyword matcher below so the widget
 * never just breaks.
 */

require_once __DIR__ . '/../includes/auth.php';
requireLogin();
require_once __DIR__ . '/../includes/charter-helpers.php';
require_once __DIR__ . '/../includes/rate-limit.php';
require_once __DIR__ . '/../includes/department-helpers.php';
require_once __DIR__ . '/../includes/chatbot-news.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['reply' => 'Unsupported request method.']);
    exit;
}

if (!verifyCsrfToken($_SERVER['HTTP_X_CSRF_TOKEN'] ?? null)) {
    http_response_code(400);
    echo json_encode(['reply' => 'Your session expired. Please refresh the page.']);
    exit;
}
// Every question may cost an AI request: 15 a minute per person, 200 an hour per device.
if (!rateLimitAllow('chat:' . (int) $_SESSION['user_id'], 15, 60) || !rateLimitAllow('chat-ip:' . clientIp(), 200, 3600)) {
    http_response_code(429);
    echo json_encode(['reply' => 'You are asking too fast. Please wait a moment and try again.']);
    exit;
}
rateLimitCleanup();

$input = json_decode(file_get_contents('php://input', false, null, 0, 20000), true);
$message = mb_substr(trim((string) ($input['message'] ?? '')), 0, 500);

if ($message === '') {
    echo json_encode(['reply' => "I didn't catch that — could you type your question again?"]);
    exit;
}

$lang = ($input['lang'] ?? 'en') === 'tl' ? 'tl' : 'en';

$reply = getAiReply($message, $lang);
if ($reply === null) {
    $reply = getRuleBasedReply($message);
    // The AI path saves its own turns; an answer from the backup matcher is saved here, so the
    // conversation looks the same on every page.
    $_SESSION['chatbot_history'] = array_slice(array_merge(
        is_array($_SESSION['chatbot_history'] ?? null) ? $_SESSION['chatbot_history'] : [],
        [['role' => 'user', 'content' => $message], ['role' => 'assistant', 'content' => $reply]]
    ), -20);
}

echo json_encode(['reply' => $reply]);
exit;

/**
 * Try answering via an LLM, with the Citizen's Charter as context and this
 * session's chat history for memory. Returns null (never throws) if no API
 * key is configured or the request fails, so the caller can fall back.
 */
function getAiReply(string $message, string $lang = 'en'): ?string
{
    require_once __DIR__ . '/../config/gemini.php';
    require_once __DIR__ . '/../config/anthropic.php';

    if (GEMINI_API_KEY === '' && ANTHROPIC_API_KEY === '') {
        return null;
    }

    if (!isset($_SESSION['chatbot_history']) || !is_array($_SESSION['chatbot_history'])) {
        $_SESSION['chatbot_history'] = [];
    }

    $history = $_SESSION['chatbot_history'];
    $history[] = ['role' => 'user', 'content' => $message];

    // The news that fit this question, and the last two things the citizen asked before it (so "when is it?" finds the post).
    $earlier = array_values(array_reverse(array_column(array_filter($_SESSION['chatbot_history'], fn($t) => $t['role'] === 'user'), 'content')));
    $recent = array_merge([$message], array_slice($earlier, 0, 2));
    $systemPrompt = getCharterSystemPrompt() . "\n\n" . chatbotStepsSection($recent) . "\n\n" . chatbotNewsSection($recent) . "\n\n" . getLanguageInstruction($lang);
    // Gemini first when it has a key. If it is down or busy (it sometimes answers "high demand"), the
    // Claude key, when there is one, answers instead, so the chatbot keeps working.
    $reply = null;
    if (GEMINI_API_KEY !== '') {
        $reply = requestGeminiReply($systemPrompt, $history);
    }
    if (($reply === null || $reply === '') && ANTHROPIC_API_KEY !== '') {
        $reply = requestClaudeReply($systemPrompt, $history);
    }

    if ($reply === null || $reply === '') {
        return null;
    }

    $history[] = ['role' => 'assistant', 'content' => $reply];
    // Cap history so the conversation doesn't grow (and cost) unbounded.
    if (count($history) > 20) {
        $history = array_slice($history, -20);
    }
    $_SESSION['chatbot_history'] = $history;

    return $reply;
}

/**
 * The citizen picks the reply language with the chat widget's EN / TL switch.
 * It goes at the END of the prompt so it wins over the examples above it.
 */
function getLanguageInstruction(string $lang): string
{
    if ($lang === 'tl') {
        return "=== Reply language ===\nReply in Tagalog (Filipino) for every message, even if the citizen writes in English. Use a polite, friendly tone with \"po\" and \"opo\" where natural. Keep official document names, office names, and amounts exactly as listed above (they may stay in English).";
    }

    return "=== Reply language ===\nReply in English for every message, even if the citizen writes in Tagalog.";
}

/**
 * One Gemini turn over plain HTTPS. $history is [['role' => 'user'|'assistant', 'content' => ...], ...].
 * Returns the reply text, or null on any failure (logged to the PHP error log).
 */
function requestGeminiReply(string $systemPrompt, array $history): ?string
{
    $contents = [];
    foreach ($history as $turn) {
        $contents[] = [
            'role' => $turn['role'] === 'assistant' ? 'model' : 'user',
            'parts' => [['text' => $turn['content']]],
        ];
    }

    // "High demand" (503) and "too many requests" (429) are usually over in a second or two: try once more.
    for ($attempt = 1; $attempt <= 2; $attempt++) {
        $ch = curl_init('https://generativelanguage.googleapis.com/v1beta/models/' . rawurlencode(GEMINI_MODEL) . ':generateContent');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTPHEADER => ['x-goog-api-key: ' . GEMINI_API_KEY, 'Content-Type: application/json'],
            CURLOPT_POSTFIELDS => json_encode([
                'system_instruction' => ['parts' => [['text' => $systemPrompt]]],
                'contents' => $contents,
                'generationConfig' => ['maxOutputTokens' => 1024, 'temperature' => 0.3],
            ]),
        ]);
        $body = curl_exec($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($body !== false && in_array($status, [429, 503], true) && $attempt === 1) {
            sleep(2);
            continue;
        }
        break;
    }

    if ($body === false || $status !== 200) {
        $detail = $body === false ? $curlError : (json_decode($body, true)['error']['message'] ?? ('HTTP ' . $status));
        error_log('Gemini chatbot request failed (' . $status . '): ' . $detail);
        return null;
    }

    $parts = json_decode($body, true)['candidates'][0]['content']['parts'] ?? [];
    $reply = '';
    foreach ($parts as $part) {
        $reply .= $part['text'] ?? '';
    }

    return trim($reply);
}

/** One Claude turn through Anthropic's PHP library; null on any failure. */
function requestClaudeReply(string $systemPrompt, array $history): ?string
{
    $autoload = __DIR__ . '/../vendor/autoload.php';
    if (!file_exists($autoload)) {
        return null;
    }
    require_once $autoload;

    try {
        $client = new \Anthropic\Client(apiKey: ANTHROPIC_API_KEY);
        $response = $client->messages->create(
            model: ANTHROPIC_MODEL,
            maxTokens: 1024,
            system: $systemPrompt,
            messages: $history,
        );
    } catch (\Throwable $e) {
        error_log('Anthropic chatbot request failed: ' . $e->getMessage());
        return null;
    }

    $reply = '';
    foreach ($response->content as $block) {
        if ($block->type === 'text') {
            $reply .= $block->text;
        }
    }

    return trim($reply);
}

/**
 * Builds the system prompt: assistant persona, the LGU's real offices and
 * their department heads (from the departments table), and the full
 * Citizen's Charter knowledge base flattened into plain text, so the model can answer any
 * phrasing of a question the rule-based matcher's keyword list would miss.
 */
function getCharterSystemPrompt(): string
{
    static $prompt = null;
    if ($prompt !== null) {
        return $prompt;
    }

    $services = getCharterServices();

    $lines = [
        "You are the LGU Norzagaray virtual assistant, embedded as a chat widget in the LGU Norzagaray citizen portal.",
        "You help citizens with questions about local government services, using the Citizen's Charter data listed below as your source of truth.",
        "",
        "How to answer:",
        "- If the question matches one of the listed services, answer using exactly that data — title, requirements, fee, and processing time. Do not invent or guess figures that aren't listed.",
        "- When someone asks how to apply, what the steps or process are, or who may apply, answer from the \"How to apply\" section for that service, in order, one short numbered line per step. If that section is missing for the service, say the steps aren't on record and suggest asking at the office.",
        "- Remember the conversation. Resolve follow-ups like \"how much is that\" or \"what about the renewal\" using whichever service was just discussed.",
        "- If nothing below matches, say so plainly and suggest the citizen contact the relevant office, or visit the Municipal Compound, A. Payumo St., Barangay Poblacion, Norzagaray, Bulacan.",
        "- For general questions (office hours, location, departments, filing a complaint, appointments) answer briefly: offices are open Monday-Friday 8:00 AM-5:00 PM except holidays; complaints/feedback go to the Office of the Mayor at 0965-377-5267 or officeofthemayor.norzagaray@gmail.com (also ARTA at complaints@arta.gov.ph); the LGU's offices and their heads are listed below.",
        "- Questions about who heads an office: answer from the office list below. If an office has no head listed, say that the head isn't on record and suggest contacting the Municipal Hall.",
        "- Never ask for or repeat personal details such as ID numbers, addresses, or phone numbers. If a citizen shares them, tell them not to and that they can bring them to the office.",
        "- Keep replies short and scannable for a small chat bubble: a one-line intro, a plain dash-bulleted requirements list, then fee and processing time. No markdown headers, no asterisk bold.",
        "- End a service answer with a brief reminder to confirm at the counter since requirements can change.",
        "",
        "=== LGU offices and department heads ===",
    ];

    foreach (getAllDepartments() as $department) {
        $head = trim((string) $department['department_head']);
        $lines[] = '- ' . $department['name'] . ': ' . ($head !== '' ? 'headed by ' . $head : 'no head on record');
    }

    $lines[] = "";
    $lines[] = "=== Citizen's Charter services ===";

    foreach ($services as $service) {
        $lines[] = "";
        $lines[] = $service['title'] . ' (' . $service['office'] . ')';
        foreach ($service['requirements'] as $requirement) {
            $lines[] = '- ' . $requirement;
        }
        $lines[] = 'Fee: ' . $service['fee'];
        $lines[] = 'Processing time: ' . $service['processing_time'];
    }

    $prompt = implode("\n", $lines);

    return $prompt;
}

// ---------------------------------------------------------------------
// Rule-based fallback (used when no API key is configured, or the API
// call fails). Same keyword-matching engine as before.
// ---------------------------------------------------------------------

function getRuleBasedReply(string $message): string
{
    $text = strtolower($message);

    foreach (getGreetingRules() as $rule) {
        foreach ($rule['keywords'] as $keyword) {
            if (hasWordMatch($text, $keyword)) {
                return $rule['reply'];
            }
        }
    }

    $service = findMatchingService($text);
    if ($service !== null) {
        // The steps are added when the citizen asks how to apply; the plain answer stays short.
        return formatServiceReply($service, (bool) preg_match('/\b(steps?|process|procedure|how (do|to|can)|apply|paano|hakbang)\b/i', $text));
    }

    // A question about the news, programs or events: answer from the posted news.
    if (($newsReply = chatbotNewsFallbackReply($message)) !== null) {
        return $newsReply;
    }

    foreach (getGeneralRules() as $rule) {
        foreach ($rule['keywords'] as $keyword) {
            if (hasWordMatch($text, $keyword)) {
                return $rule['reply'];
            }
        }
    }

    // Not a service either: maybe a word that appears in a posted program or announcement.
    if (($newsReply = chatbotNewsFallbackReply($message, true)) !== null) {
        return $newsReply;
    }

    return "I couldn't find that exact service in the Citizen's Charter. Try naming the document or service directly — for example \"business permit requirements\", \"senior citizen ID\", \"birth certificate copy\", or \"real property tax\". You can also ask me about office hours, departments, or how to file a complaint.";
}

/**
 * Word-boundary substring match: matches "new" at the start of "news" or
 * "newly" (useful stemming), but never inside "renew" or "hi" inside
 * "child", the way a plain str_contains() would.
 */
function hasWordMatch(string $haystack, string $needle): bool
{
    if ($needle === '') {
        return false;
    }

    return (bool) preg_match('/\b' . preg_quote($needle, '/') . '/iu', $haystack);
}

/**
 * Search the Citizen's Charter knowledge base for the best keyword match.
 * Uses word-overlap rather than exact phrase matching, so an inserted word
 * (e.g. "copy of MY birth certificate" vs. keyword "copy of birth
 * certificate") doesn't break the match — while still requiring most of
 * the keyword's words to be present, at real word boundaries.
 */
function findMatchingService(string $text): ?array
{
    $services = getCharterServices();

    $bestMatch = null;
    $bestScore = 0;

    foreach ($services as $service) {
        foreach ($service['keywords'] as $keyword) {
            $keyword = trim($keyword);
            $words = array_values(array_filter(explode(' ', $keyword), fn($w) => $w !== ''));
            $wordCount = count($words);
            if ($wordCount === 0) {
                continue;
            }

            $matched = 0;
            foreach ($words as $word) {
                if (hasWordMatch($text, $word)) {
                    $matched++;
                }
            }

            $ratio = $matched / $wordCount;
            $isConfidentMatch = $wordCount === 1 ? $matched === 1 : ($ratio >= 0.75 && $matched >= 2);

            if ($isConfidentMatch) {
                $score = $matched * 10 + $wordCount;
                if ($score > $bestScore) {
                    $bestScore = $score;
                    $bestMatch = $service;
                }
            }
        }
    }

    return $bestMatch;
}

/**
 * The "how to apply" steps and who may avail for the services the citizen is asking about (this question
 * and the two before it). Only these are sent, so the always-on prompt stays small.
 */
function chatbotStepsSection(array $texts): string
{
    $found = [];
    foreach ($texts as $text) {
        $service = findMatchingService(strtolower((string) $text));
        if ($service !== null) {
            $found[$service['id']] = $service;
        }
        if (count($found) >= 2) {
            break;
        }
    }

    $lines = ['=== How to apply (steps) for the services being discussed ==='];
    foreach ($found as $service) {
        $lines[] = '';
        $lines[] = $service['title'] . ' (' . $service['office'] . ')';
        if ($service['who_may_avail'] !== '') {
            $lines[] = 'Who may avail: ' . $service['who_may_avail'];
        }
        if (empty($service['steps'])) {
            $lines[] = 'No steps on record for this service.';
            continue;
        }
        foreach ($service['steps'] as $i => $step) {
            $lines[] = ($i + 1) . '. ' . $step;
        }
    }
    if (empty($found)) {
        $lines[] = '(No specific service is being discussed right now.)';
    }

    return implode("\n", $lines);
}

function formatServiceReply(array $service, bool $withSteps = false): string
{
    $lines = [];
    $lines[] = $service['title'] . ' — ' . $service['office'];
    $lines[] = '';
    $lines[] = 'Requirements:';
    foreach ($service['requirements'] as $requirement) {
        $lines[] = '• ' . $requirement;
    }
    $lines[] = '';
    $lines[] = 'Fee: ' . $service['fee'];
    $lines[] = 'Processing time: ' . $service['processing_time'];
    if ($withSteps && !empty($service['steps'])) {
        $lines[] = '';
        $lines[] = 'How to apply:';
        foreach ($service['steps'] as $i => $step) {
            $lines[] = ($i + 1) . '. ' . $step;
        }
    }
    $lines[] = '';
    $lines[] = '(From the Norzagaray Citizen\'s Charter 2025. Bring originals plus the noted photocopies, and confirm at the counter since requirements and steps can change.)';

    return implode("\n", $lines);
}

function getGreetingRules(): array
{
    return [
        [
            'keywords' => ['hi', 'hello', 'kumusta', 'magandang'],
            'reply' => "Hello! I'm the LGU Norzagaray virtual assistant. I know the requirements, fees, and processing times for services listed in the Citizen's Charter — just tell me the document or service you need, e.g. \"requirements for a mayor's permit\" or \"senior citizen ID\".",
        ],
        [
            'keywords' => ['thank', 'salamat'],
            'reply' => "You're welcome! Let me know if there's anything else you'd like to ask about LGU Norzagaray services.",
        ],
        [
            'keywords' => ['bye', 'goodbye', 'paalam'],
            'reply' => "Take care! Feel free to open this chat again anytime you have a question.",
        ],
    ];
}

function getGeneralRules(): array
{
    return [
        [
            'keywords' => ['appointment', 'schedule', 'book', 'reschedule', 'cancel'],
            'reply' => "Online appointment booking is coming soon on this portal. For now, you may visit or call your target department directly to set a desk appointment.",
        ],
        [
            'keywords' => ['hour', 'open', 'close', 'schedule ng opisina', 'oras'],
            'reply' => "LGU Norzagaray offices are generally open Monday to Friday, 8:00 AM to 5:00 PM, except on holidays. Specific offices may have different cut-off times for accepting same-day transactions.",
        ],
        [
            'keywords' => ['location', 'address', 'where is', 'saan'],
            'reply' => "Most offices are at the Municipal Compound, A. Payumo St., Barangay Poblacion, Norzagaray, Bulacan. Ask me about a specific office (e.g. \"Municipal Health Office\") and I can tell you which department handles it.",
        ],
        [
            'keywords' => ['department', '29 department', 'list of offices', 'what offices'],
            'reply' => "LGU Norzagaray's frontline offices include: BPLO, Municipal Assessor's Office, Civil Registrar, Engineering, Health Office, Planning & Development, Environment & Natural Resources, Social Welfare, Treasurer's Office, and the Municipal Hospital. Tell me what you need and I'll point you to the right one.",
        ],
        [
            'keywords' => ['complaint', 'feedback', 'reklamo', 'file a complaint'],
            'reply' => "You can send feedback or a complaint to the Office of the Mayor at 0965-377-5267 or officeofthemayor.norzagaray@gmail.com. It gets logged and forwarded to the concerned office, with a response within 3 days. You may also contact the Anti-Red Tape Authority (ARTA) at complaints@arta.gov.ph.",
        ],
        [
            'keywords' => ['fee', 'cost', 'bayad', 'magkano', 'how much'],
            'reply' => "Fees depend on the specific document or service — tell me which one (e.g. \"birth certificate\", \"business permit\", \"cedula\") and I'll give you the exact amount from the Citizen's Charter.",
        ],
        [
            'keywords' => ['requirement', 'document', 'certificate', 'permit', 'apply', 'request', 'id'],
            'reply' => "I can help with that — tell me the specific document or ID you need (e.g. \"business permit\", \"senior citizen ID\", \"birth certificate\", \"building permit\") and I'll give you the exact requirements, fee, and processing time from the Citizen's Charter.",
        ],
        [
            'keywords' => ['help', 'menu', 'what can you do'],
            'reply' => "I'm built on LGU Norzagaray's Citizen's Charter — ask me about requirements, fees, or processing time for any service (business permits, civil registry documents, building permits, health services, social welfare IDs, property tax, and more). Just name the document or service.",
        ],
    ];
}
