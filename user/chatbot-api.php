<?php
/**
 * Chatbot backend for the citizen portal.
 *
 * Primary path: calls Claude (Anthropic API) with the full Citizen's Charter
 * data as system-prompt context, plus this session's conversation history,
 * so it understands natural phrasing and remembers earlier turns.
 *
 * Fallback path: if no API key is configured (config/anthropic-key.local.php
 * missing) or the API call fails, degrades to the original rule-based
 * keyword matcher below so the widget never just breaks.
 */

require_once __DIR__ . '/../includes/auth.php';
requireLogin();
require_once __DIR__ . '/../includes/charter-helpers.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['reply' => 'Unsupported request method.']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$message = trim((string) ($input['message'] ?? ''));

if ($message === '') {
    echo json_encode(['reply' => "I didn't catch that — could you type your question again?"]);
    exit;
}

$reply = getAiReply($message);
if ($reply === null) {
    $reply = getRuleBasedReply($message);
}

echo json_encode(['reply' => $reply]);
exit;

/**
 * Try answering via Claude, with the Citizen's Charter as context and this
 * session's chat history for memory. Returns null (never throws) if no API
 * key is configured or the request fails, so the caller can fall back.
 */
function getAiReply(string $message): ?string
{
    require_once __DIR__ . '/../config/anthropic.php';

    if (ANTHROPIC_API_KEY === '') {
        return null;
    }

    $autoload = __DIR__ . '/../vendor/autoload.php';
    if (!file_exists($autoload)) {
        return null;
    }
    require_once $autoload;

    if (!isset($_SESSION['chatbot_history']) || !is_array($_SESSION['chatbot_history'])) {
        $_SESSION['chatbot_history'] = [];
    }

    $history = $_SESSION['chatbot_history'];
    $history[] = ['role' => 'user', 'content' => $message];

    try {
        $client = new \Anthropic\Client(apiKey: ANTHROPIC_API_KEY);
        $response = $client->messages->create(
            model: ANTHROPIC_MODEL,
            maxTokens: 1024,
            system: getCharterSystemPrompt(),
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
    $reply = trim($reply);

    if ($reply === '') {
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
 * Builds the system prompt: assistant persona + the full Citizen's Charter
 * knowledge base flattened into plain text, so Claude can answer any
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
        "- Remember the conversation. Resolve follow-ups like \"how much is that\" or \"what about the renewal\" using whichever service was just discussed.",
        "- If nothing below matches, say so plainly and suggest the citizen contact the relevant office, or visit the Municipal Compound, A. Payumo St., Barangay Poblacion, Norzagaray, Bulacan.",
        "- For general questions (office hours, location, departments, filing a complaint, appointments) answer briefly: offices are open Monday-Friday 8:00 AM-5:00 PM except holidays; complaints/feedback go to the Office of the Mayor at 0965-377-5267 or officeofthemayor.norzagaray@gmail.com (also ARTA at complaints@arta.gov.ph); frontline offices include BPLO, Municipal Assessor's Office, Civil Registrar, Engineering, Health Office, MPDO, MENRO, MSWDO, Treasurer's Office, and the Municipal Hospital.",
        "- Keep replies short and scannable for a small chat bubble: a one-line intro, a plain dash-bulleted requirements list, then fee and processing time. No markdown headers, no asterisk bold.",
        "- End a service answer with a brief reminder to confirm at the counter since requirements can change.",
        "",
        "=== Citizen's Charter services ===",
    ];

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
        return formatServiceReply($service);
    }

    foreach (getGeneralRules() as $rule) {
        foreach ($rule['keywords'] as $keyword) {
            if (hasWordMatch($text, $keyword)) {
                return $rule['reply'];
            }
        }
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

function formatServiceReply(array $service): string
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
    $lines[] = '';
    $lines[] = '(From the Norzagaray Citizen\'s Charter 2025. Bring originals plus the noted photocopies, and confirm at the counter since requirements can change.)';

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
            'keywords' => ['department', '27 department', 'list of offices', 'what offices'],
            'reply' => "LGU Norzagaray's frontline offices include: BPLO, Municipal Assessor's Office, Civil Registrar, Engineering, Health Office, Planning & Development (MPDO), Environment & Natural Resources (MENRO), Social Welfare (MSWDO), Treasurer's Office, and the Municipal Hospital. Tell me what you need and I'll point you to the right one.",
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
