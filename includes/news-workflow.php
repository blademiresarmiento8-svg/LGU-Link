<?php
/**
 * Offices write news and announcements; the Municipal Administrator reviews them.
 *
 * Every draft is a "submission". Its life:
 *
 *   draft --send--> pending_review --approve--> approved (live post created/updated)
 *                        |   ^                  \--reject--> rejected --office edits--> pending_review
 *                 admin edits / suggests    office accepts or edits & sends back
 *                        v   |
 *                   awaiting_office
 *
 * The administrator always has the final say: they can edit or suggest changes (the
 * office is told and must respond), approve, reject with a reason, or approve an urgent
 * post even though the office has not responded yet (an override, noted in the history).
 * Every step is stored in news_submission_events with a snapshot of the content, so both
 * sides see exactly who changed what and when.
 *
 * Requires includes/news-helpers.php, news-admin.php (image upload), notification-helpers.php.
 */

require_once __DIR__ . '/input-limits.php';
require_once __DIR__ . '/news-helpers.php';
require_once __DIR__ . '/news-admin.php';
require_once __DIR__ . '/notification-helpers.php';
require_once __DIR__ . '/audit-helpers.php';
require_once __DIR__ . '/department-helpers.php';

/** The editable content of a post/submission, in display order. */
const NEWS_CONTENT_FIELDS = [
    'post_type', 'category', 'title', 'description', 'image',
    'is_activity', 'starts_at', 'ends_at', 'venue', 'requirements', 'contact_info',
    'go_live_at', 'show_until',
];

const NEWS_FIELD_LABELS = [
    'post_type' => 'Type', 'category' => 'Category', 'title' => 'Title', 'description' => 'Text', 'image' => 'Photo',
    'is_activity' => 'Is an activity', 'starts_at' => 'Starts', 'ends_at' => 'Ends', 'venue' => 'Venue',
    'requirements' => 'Requirements', 'contact_info' => 'Contact', 'go_live_at' => 'Posts at', 'show_until' => 'Shown until',
];

const NEWS_STATUS_LABELS = [
    'draft' => 'Draft',
    'pending_review' => 'Waiting for the administrator',
    'awaiting_office' => 'Changes made: waiting for the office',
    'approved' => 'Approved',
    'rejected' => 'Not approved',
    'withdrawn' => 'Withdrawn',
];

const NEWS_STATUS_BADGES = [
    'draft' => 'bg-completed', 'pending_review' => 'bg-pending', 'awaiting_office' => 'bg-forwarded',
    'approved' => 'bg-approved', 'rejected' => 'bg-rejected', 'withdrawn' => 'bg-rejected',
];

const NEWS_TITLE_MAX = 200;
const NEWS_TEXT_MAX = 5000;
const NEWS_REQUIREMENTS_MAX_ITEMS = 15;
const NEWS_REQUIREMENT_MAX_LENGTH = 150;

const NEWS_OFFICE_LINK = '/LGU-Link/office/news-view.php?id=';
const NEWS_ADMIN_LINK = '/LGU-Link/admin/news-review.php?id=';

/* ------------------------------------------------------------------ content */

/** "2026-11-05T09:00" (from a datetime-local input) or "2026-11-05 09:00" to "2026-11-05 09:00:00"; null if empty/invalid. */
function parseLocalDateTime(string $value): ?string
{
    $value = trim(str_replace('T', ' ', $value));
    if ($value === '') {
        return null;
    }
    foreach (['Y-m-d H:i', 'Y-m-d H:i:s'] as $format) {
        $parsed = DateTime::createFromFormat($format, $value);
        if ($parsed !== false && $parsed->format($format) === $value) {
            return $parsed->format('Y-m-d H:i:00');
        }
    }

    return null;
}

/** Datetime for a datetime-local input (or '' when empty). */
function toLocalInput(?string $dateTime): string
{
    return $dateTime ? date('Y-m-d\TH:i', strtotime($dateTime)) : '';
}

/**
 * Cleans and validates the post form.
 *
 * @param array<string, mixed> $input          Raw form fields.
 * @param array                $file           $_FILES['image']-shaped array.
 * @param ?string              $currentImage   The photo the post already has (kept unless replaced/removed).
 * @param bool                 $checkSchedule  true for a new post: every time that was set must still be in the future.
 *                                             false for an existing post: only a time that was changed is checked.
 * @param ?array               $previous       The existing post's content (see newsContentOf), so an unchanged old time is accepted.
 * @return array{errors: string[], content: array<string, mixed>, uploaded: ?string}
 */
function validateNewsContent(array $input, array $file, ?string $currentImage, bool $checkSchedule = true, ?array $previous = null): array
{
    $errors = [];
    $clean = fn(string $key) => trim((string) preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', (string) ($input[$key] ?? '')));

    $postType = $clean('post_type');
    $category = $clean('category');
    $title = trim((string) preg_replace('/\s+/u', ' ', $clean('title')));
    $description = $clean('description');
    $isActivity = !empty($input['is_activity']);

    if (!isset(newsOptions()['types'][$postType])) {
        $errors[] = 'Choose a type for the post.';
    }
    if (!in_array($category, newsCategoryNames(), true)) {
        $errors[] = 'Choose a category.';
    }
    if ($title === '' || mb_strlen($title) > NEWS_TITLE_MAX) {
        $errors[] = 'Enter a title (up to ' . NEWS_TITLE_MAX . ' characters).';
    }
    if ($description === '' || mb_strlen($description) > NEWS_TEXT_MAX) {
        $errors[] = 'Write the text of the post (up to ' . NEWS_TEXT_MAX . ' characters).';
    }

    // A time that has already passed is a mistake when the person has just set it: on a new post any time
    // counts; on an existing post only a time they changed does, so an old post can still be edited.
    $now = date('Y-m-d H:i:s');
    $grace = date('Y-m-d H:i:s', strtotime('-5 minutes'));
    $mustBeFuture = fn(string $key, ?string $value) => $value !== null
        && ($checkSchedule || ($previous !== null && substr((string) ($previous[$key] ?? ''), 0, 16) !== substr($value, 0, 16)));

    // Schedule
    $goLive = parseLocalDateTime($clean('go_live_at'));
    $showUntil = parseLocalDateTime($clean('show_until'));
    $showUntilChosen = $showUntil; // before an activity fills it in by itself
    if ($clean('go_live_at') !== '' && $goLive === null) {
        $errors[] = 'The posting time is not valid.';
    }
    if ($clean('show_until') !== '' && $showUntil === null) {
        $errors[] = 'The "show until" time is not valid.';
    }
    if ($mustBeFuture('go_live_at', $goLive) && $goLive < $grace) {
        $errors[] = 'The posting time is in the past. Leave it empty to post as soon as it is approved.';
    }
    if ($mustBeFuture('show_until', $showUntilChosen) && $showUntilChosen <= $now) {
        $errors[] = 'The "show until" time has already passed. Choose a later time, or leave it empty.';
    }

    // Activity details
    $startsAt = $endsAt = $venue = $requirements = $contact = null;
    if ($isActivity) {
        $startsAt = parseLocalDateTime($clean('starts_at'));
        $endsAt = parseLocalDateTime($clean('ends_at'));
        $venue = $clean('venue');
        $contact = $clean('contact_info');

        if ($startsAt === null || $endsAt === null) {
            $errors[] = 'Enter when the activity starts and ends.';
        } elseif ($endsAt <= $startsAt) {
            $errors[] = 'The activity must end after it starts.';
        } elseif ($mustBeFuture('starts_at', $startsAt) && $startsAt < date('Y-m-d 00:00:00')) {
            // Earlier today is fine ("free medical mission today, 8 AM to 4 PM", posted at 9 AM); a past day is not.
            $errors[] = 'The activity cannot start on a day that has passed. Choose today or a later day.';
        } elseif ($mustBeFuture('ends_at', $endsAt) && $endsAt <= $now) {
            $errors[] = 'The activity end time has already passed. Choose a time that is still ahead.';
        }
        if ($venue === '' || mb_strlen($venue) > 150) {
            $errors[] = 'Enter where the activity is held (up to 150 characters).';
        }
        if (mb_strlen($contact) > 150) {
            $errors[] = 'The contact details must be 150 characters or fewer.';
        }

        // One requirement per line; a leading dash or bullet is dropped.
        $items = [];
        foreach (preg_split('/\R/u', (string) ($input['requirements'] ?? '')) as $line) {
            $line = trim((string) preg_replace('/^[\-\*\x{2022}\s]+/u', '', trim($line)));
            if ($line !== '') {
                $items[] = $line;
            }
        }
        if (count($items) > NEWS_REQUIREMENTS_MAX_ITEMS) {
            $errors[] = 'List at most ' . NEWS_REQUIREMENTS_MAX_ITEMS . ' requirements.';
        }
        foreach ($items as $item) {
            if (mb_strlen($item) > NEWS_REQUIREMENT_MAX_LENGTH) {
                $errors[] = 'Each requirement must be ' . NEWS_REQUIREMENT_MAX_LENGTH . ' characters or fewer.';
                break;
            }
        }
        $requirements = empty($items) ? null : implode("\n", $items);
        $venue = $venue === '' ? null : $venue;
        $contact = $contact === '' ? null : $contact;

        // An activity post disappears after the activity unless told otherwise.
        if ($showUntil === null && $endsAt !== null) {
            $showUntil = $endsAt;
        }
    }

    if ($showUntil !== null && $showUntil <= ($goLive ?? date('Y-m-d H:i:s'))) {
        $errors[] = 'The "show until" time must be after the time it is posted.';
    }

    // Photo (optional): upload a new one, keep the current one, or remove it.
    $upload = uploadNewsImage($file);
    if ($upload['error'] !== null) {
        $errors[] = $upload['error'];
    }
    $image = $upload['path'] ?? (!empty($input['remove_image']) ? null : $currentImage);

    if (!empty($errors) && $upload['path'] !== null) {
        deleteNewsImageFile($upload['path']);
        $upload['path'] = null;
        $image = $currentImage;
    }

    return [
        'errors' => $errors,
        'uploaded' => $upload['path'],
        'content' => [
            'post_type' => $postType, 'category' => $category, 'title' => $title, 'description' => $description,
            'image' => $image, 'is_activity' => $isActivity ? 1 : 0, 'starts_at' => $startsAt, 'ends_at' => $endsAt,
            'venue' => $venue, 'requirements' => $requirements, 'contact_info' => $contact,
            'go_live_at' => $goLive, 'show_until' => $showUntil,
        ],
    ];
}

/** Just the editable fields of a post or submission row. */
function newsContentOf(array $row): array
{
    $content = [];
    foreach (NEWS_CONTENT_FIELDS as $field) {
        $content[$field] = $row[$field] ?? null;
    }

    return $content;
}

/** Human-readable value of one field, for the change history. */
function describeNewsField(string $field, mixed $value): string
{
    if ($value === null || $value === '' || $value === 0 || $value === '0') {
        return $field === 'is_activity' ? 'No' : 'none';
    }

    return match ($field) {
        'post_type' => newsTypeLabel((string) $value),
        'is_activity' => 'Yes',
        'starts_at', 'ends_at', 'go_live_at', 'show_until' => date('M j, Y, g:i A', strtotime((string) $value)),
        'image' => 'a photo',
        'requirements' => implode('; ', newsRequirementsList((string) $value)),
        default => (string) $value,
    };
}

/**
 * What changed between two versions of the content.
 *
 * @return array<int, array{label: string, before: string, after: string}>
 */
function diffNewsContent(array $before, array $after): array
{
    $changes = [];
    foreach (NEWS_CONTENT_FIELDS as $field) {
        $old = $before[$field] ?? null;
        $new = $after[$field] ?? null;
        if ((string) $old !== (string) $new) {
            $changes[] = ['label' => NEWS_FIELD_LABELS[$field], 'before' => describeNewsField($field, $old), 'after' => describeNewsField($field, $new)];
        }
    }

    return $changes;
}

/* ------------------------------------------------------------- submissions */

const NEWS_SUBMISSION_SELECT = 'SELECT s.*, d.name AS department_name, u.full_name AS author_name, p.title AS live_title
    FROM news_submissions s
    JOIN departments d ON d.id = s.department_id
    JOIN users u ON u.id = s.author_user_id
    LEFT JOIN news_posts p ON p.id = s.post_id';

function findNewsSubmission(int $id): ?array
{
    $stmt = getDbConnection()->prepare(NEWS_SUBMISSION_SELECT . ' WHERE s.id = :id');
    $stmt->execute(['id' => $id]);

    return $stmt->fetch() ?: null;
}

/** A department's own submissions, most recently touched first. */
function getNewsSubmissionsForDepartment(int $departmentId): array
{
    $stmt = getDbConnection()->prepare(NEWS_SUBMISSION_SELECT . ' WHERE s.department_id = :dept ORDER BY s.updated_at DESC, s.id DESC');
    $stmt->execute(['dept' => $departmentId]);

    return $stmt->fetchAll();
}

/** Submissions the administrator should look at (or every one, with $all). */
function getNewsSubmissionsForReview(bool $all = false): array
{
    $where = $all ? "s.status <> 'draft'" : "s.status IN ('pending_review', 'awaiting_office')";

    return getDbConnection()->query(NEWS_SUBMISSION_SELECT . " WHERE $where ORDER BY (s.status = 'pending_review') DESC, s.updated_at DESC, s.id DESC")->fetchAll();
}

function countNewsSubmissionsAwaitingAdmin(): int
{
    return (int) getDbConnection()->query("SELECT COUNT(*) FROM news_submissions WHERE status = 'pending_review'")->fetchColumn();
}

function getNewsSubmissionEvents(int $submissionId): array
{
    $stmt = getDbConnection()->prepare('SELECT * FROM news_submission_events WHERE submission_id = :id ORDER BY id ASC');
    $stmt->execute(['id' => $submissionId]);

    return $stmt->fetchAll();
}

/** Appends an entry to a submission's history. */
function addNewsEvent(int $submissionId, array $actor, string $role, string $action, ?string $note, ?array $content): void
{
    getDbConnection()->prepare(
        'INSERT INTO news_submission_events (submission_id, actor_user_id, actor_name, actor_role, action, note, snapshot)
         VALUES (:submission, :actor, :name, :role, :action, :note, :snapshot)'
    )->execute([
        'submission' => $submissionId,
        'actor' => $actor['id'] ?? null,
        'name' => $actor['full_name'] ?? 'System',
        'role' => $role,
        'action' => $action,
        'note' => ($note === null || trim($note) === '') ? null : trim($note),
        'snapshot' => $content === null ? null : json_encode($content, JSON_UNESCAPED_UNICODE),
    ]);
}

/** Writes new content and a new status onto a submission. */
function updateNewsSubmission(int $id, array $content, string $status): void
{
    $sets = [];
    $params = ['id' => $id, 'status' => $status];
    foreach (NEWS_CONTENT_FIELDS as $field) {
        $sets[] = "$field = :$field";
        $params[$field] = $content[$field];
    }
    getDbConnection()->prepare('UPDATE news_submissions SET status = :status, ' . implode(', ', $sets) . ' WHERE id = :id')->execute($params);
}

function setNewsSubmissionStatus(int $id, string $status, ?string $rejectReason = null, bool $decided = false): void
{
    getDbConnection()->prepare(
        'UPDATE news_submissions SET status = :status, reject_reason = :reason, decided_at = ' . ($decided ? 'NOW()' : 'decided_at') . ' WHERE id = :id'
    )->execute(['status' => $status, 'reason' => $rejectReason, 'id' => $id]);
}

/** Starts a draft from scratch, or (with $postId) from an already-live post to propose changes to it. */
function createNewsSubmission(array $user, array $content, ?int $postId = null): int
{
    $pdo = getDbConnection();
    $columns = array_merge(['post_id', 'department_id', 'author_user_id', 'status'], NEWS_CONTENT_FIELDS);
    $params = ['post_id' => $postId, 'department_id' => (int) $user['department_id'], 'author_user_id' => (int) $user['id'], 'status' => 'draft'];
    foreach (NEWS_CONTENT_FIELDS as $field) {
        $params[$field] = $content[$field];
    }
    $pdo->prepare(
        'INSERT INTO news_submissions (' . implode(', ', $columns) . ') VALUES (' . implode(', ', array_map(fn($c) => ":$c", $columns)) . ')'
    )->execute($params);
    $id = (int) $pdo->lastInsertId();
    addNewsEvent($id, $user, 'office', 'created', $postId !== null ? 'Started from a live post, to propose changes.' : null, $content);

    return $id;
}

/** Can this office account act on this submission? (Offices only ever touch their own department's.) */
function ownsNewsSubmission(?array $submission, array $user): bool
{
    return $submission !== null && (int) $submission['department_id'] === (int) ($user['department_id'] ?? 0);
}

/**
 * The office saves its draft, sends it to the administrator, or sends back the
 * administrator's edited version (after editing it further or as it is).
 *
 * @return array{success: bool, errors: string[]}
 */
function officeSaveNewsSubmission(int $id, array $user, array $content, bool $send, ?string $note): array
{
    if (($tooLong = tooLongMessage('The note', trim((string) $note), NOTE_MAX_LENGTH)) !== null) {
        return ['success' => false, 'errors' => [$tooLong]];
    }
    $submission = findNewsSubmission($id);
    if (!ownsNewsSubmission($submission, $user)) {
        return ['success' => false, 'errors' => ['That post no longer exists.']];
    }
    if (!in_array($submission['status'], ['draft', 'rejected', 'awaiting_office'], true)) {
        return ['success' => false, 'errors' => ['This post cannot be edited right now.']];
    }

    $before = newsContentOf($submission);
    $wasAwaiting = $submission['status'] === 'awaiting_office';
    $status = ($send || $wasAwaiting) ? 'pending_review' : 'draft';

    updateNewsSubmission($id, $content, $status);
    if ($status === 'pending_review') {
        setNewsSubmissionStatus($id, 'pending_review');
    }
    $changed = !empty(diffNewsContent($before, $content));
    $action = $wasAwaiting ? 'office_edited' : ($send ? 'submitted' : 'saved');
    addNewsEvent($id, $user, 'office', $action, $note, $content);

    // Photos that were replaced and are no longer used anywhere.
    if (($before['image'] ?? null) !== ($content['image'] ?? null)) {
        deleteNewsImageIfUnused($before['image'] ?? null);
    }

    if ($status === 'pending_review') {
        $who = $submission['department_name'];
        notifyRole('admin', $wasAwaiting
            ? "$who replied to your edits on \"{$content['title']}\"" . ($changed ? ' and made more changes.' : '.')
            : "$who sent a post for review: {$content['title']}", NEWS_ADMIN_LINK . $id);
    }

    return ['success' => true, 'errors' => []];
}

/** The office agrees with the administrator's edits as they are. */
function officeAcceptNewsEdits(int $id, array $user, ?string $note): array
{
    if (($tooLong = tooLongMessage('The note', trim((string) $note), NOTE_MAX_LENGTH)) !== null) {
        return ['success' => false, 'errors' => [$tooLong]];
    }
    $submission = findNewsSubmission($id);
    if (!ownsNewsSubmission($submission, $user) || $submission['status'] !== 'awaiting_office') {
        return ['success' => false, 'errors' => ['There is nothing to accept on this post.']];
    }

    setNewsSubmissionStatus($id, 'pending_review');
    addNewsEvent($id, $user, 'office', 'office_accepted', $note, newsContentOf($submission));
    notifyRole('admin', $submission['department_name'] . ' accepted your changes to "' . $submission['title'] . '". It is ready for your approval.', NEWS_ADMIN_LINK . $id);

    return ['success' => true, 'errors' => []];
}

/** The office takes a post back out of the process. */
function officeWithdrawNewsSubmission(int $id, array $user): array
{
    $submission = findNewsSubmission($id);
    if (!ownsNewsSubmission($submission, $user) || !in_array($submission['status'], ['draft', 'pending_review', 'awaiting_office', 'rejected'], true)) {
        return ['success' => false, 'errors' => ['This post cannot be withdrawn.']];
    }

    $wasInReview = in_array($submission['status'], ['pending_review', 'awaiting_office'], true);
    setNewsSubmissionStatus($id, 'withdrawn');
    addNewsEvent($id, $user, 'office', 'withdrawn', null, null);
    if ($wasInReview) {
        notifyRole('admin', $submission['department_name'] . ' withdrew its post "' . $submission['title'] . '".', NEWS_ADMIN_LINK . $id);
    }

    return ['success' => true, 'errors' => []];
}

/**
 * The administrator changes the wording and/or leaves a note ("suggest"). The office is
 * told and must accept or reply before it comes back for approval.
 */
function adminEditNewsSubmission(int $id, array $admin, ?array $content, string $note): array
{
    if (($tooLong = tooLongMessage('The note', trim($note), NOTE_MAX_LENGTH)) !== null) {
        return ['success' => false, 'errors' => [$tooLong]];
    }
    $submission = findNewsSubmission($id);
    if ($submission === null || !in_array($submission['status'], ['pending_review', 'awaiting_office'], true)) {
        return ['success' => false, 'errors' => ['That post is not in review.']];
    }

    $before = newsContentOf($submission);
    $content ??= $before;
    $changes = diffNewsContent($before, $content);
    if (empty($changes) && trim($note) === '') {
        return ['success' => false, 'errors' => ['Change something or write a note to the office.']];
    }

    updateNewsSubmission($id, $content, 'awaiting_office');
    addNewsEvent($id, $admin, 'admin', empty($changes) ? 'admin_suggested' : 'admin_edited', $note, $content);
    if (($before['image'] ?? null) !== ($content['image'] ?? null)) {
        deleteNewsImageIfUnused($before['image'] ?? null);
    }

    notifyDepartment((int) $submission['department_id'], empty($changes)
        ? 'The Municipal Administrator left a note on your post "' . $content['title'] . '". Please review it.'
        : 'The Municipal Administrator edited your post "' . $content['title'] . '". Please review the changes.', NEWS_OFFICE_LINK . $id);

    return ['success' => true, 'errors' => []];
}

/**
 * Approves a submission, creating or updating the live post. Normally from "pending_review";
 * with $override an urgent post can be approved while it is still "awaiting_office" (a note is
 * then required, and the office is told exactly what went live).
 *
 * @return array{success: bool, errors: string[], post_id?: int}
 */
function adminApproveNewsSubmission(int $id, array $admin, string $note, bool $featured, bool $override = false): array
{
    if (($tooLong = tooLongMessage('The note', trim($note), NOTE_MAX_LENGTH)) !== null) {
        return ['success' => false, 'errors' => [$tooLong]];
    }
    $submission = findNewsSubmission($id);
    if ($submission === null) {
        return ['success' => false, 'errors' => ['That post no longer exists.']];
    }
    $isOverride = $submission['status'] === 'awaiting_office';
    if (!($submission['status'] === 'pending_review' || ($override && $isOverride))) {
        return ['success' => false, 'errors' => [$isOverride
            ? 'The office has not responded to your changes yet. Wait for its reply, or approve it anyway as an urgent post.'
            : 'That post is not waiting for approval.']];
    }
    if ($isOverride && trim($note) === '') {
        return ['success' => false, 'errors' => ['Add a short note explaining why it is approved before the office responded.']];
    }

    $content = newsContentOf($submission);
    $postId = publishNewsContent($submission['post_id'] !== null ? (int) $submission['post_id'] : null, $content, (int) $submission['department_id'], $featured);

    getDbConnection()->prepare("UPDATE news_submissions SET post_id = :post, status = 'approved', decided_at = NOW() WHERE id = :id")->execute(['post' => $postId, 'id' => $id]);
    addNewsEvent($id, $admin, 'admin', $isOverride ? 'approved_override' : 'approved', $note, $content);

    $post = findNewsPostById($postId);
    $when = newsPostState($post) === 'scheduled' ? 'It will be posted on ' . date('F j, Y, g:i A', strtotime((string) $post['display_at'])) . '.' : 'It is now live.';
    notifyDepartment((int) $submission['department_id'], 'Your post "' . $content['title'] . '" was approved' . ($isOverride ? ' (urgent, before your reply)' : '') . '. ' . $when, NEWS_OFFICE_LINK . $id);

    return ['success' => true, 'errors' => [], 'post_id' => $postId];
}

function adminRejectNewsSubmission(int $id, array $admin, string $reason): array
{
    $submission = findNewsSubmission($id);
    if ($submission === null || !in_array($submission['status'], ['pending_review', 'awaiting_office'], true)) {
        return ['success' => false, 'errors' => ['That post is not in review.']];
    }
    $reason = trim($reason);
    if ($reason === '') {
        return ['success' => false, 'errors' => ['Please tell the office why it was not approved.']];
    }
    if (($tooLong = tooLongMessage('The reason', $reason, NOTE_MAX_LENGTH)) !== null) {
        return ['success' => false, 'errors' => [$tooLong]];
    }

    setNewsSubmissionStatus($id, 'rejected', $reason, true);
    addNewsEvent($id, $admin, 'admin', 'rejected', $reason, newsContentOf($submission));
    notifyDepartment((int) $submission['department_id'], 'Your post "' . $submission['title'] . '" was not approved. Open it to see why; you can edit it and send it again.', NEWS_OFFICE_LINK . $id);

    return ['success' => true, 'errors' => []];
}

/* -------------------------------------------------------------- live posts */

/**
 * Creates or updates the live post from approved content. A go-live time that is empty or
 * already past means "now". The administrator controls the homepage carousel ($featured).
 */
function publishNewsContent(?int $postId, array $content, ?int $departmentId, bool $featured): int
{
    $pdo = getDbConnection();
    $now = date('Y-m-d H:i:s');
    $goLive = ($content['go_live_at'] === null || $content['go_live_at'] < $now) ? $now : $content['go_live_at'];
    $existing = $postId !== null ? findNewsPostById($postId) : null;

    $params = [
        'category' => $content['category'], 'post_type' => $content['post_type'], 'title' => $content['title'],
        'description' => $content['description'], 'image' => $content['image'], 'featured' => $featured ? 1 : 0,
        'published_at' => substr($goLive, 0, 10), 'go_live_at' => $goLive, 'show_until' => $content['show_until'],
        'is_activity' => $content['is_activity'], 'starts_at' => $content['starts_at'], 'ends_at' => $content['ends_at'],
        'venue' => $content['venue'], 'requirements' => $content['requirements'], 'contact_info' => $content['contact_info'],
    ];

    if ($existing === null) {
        $params['department_id'] = $departmentId;
        $pdo->prepare(
            'INSERT INTO news_posts (category, post_type, department_id, title, description, image, is_featured, published_at, go_live_at, show_until,
                is_activity, starts_at, ends_at, venue, requirements, contact_info)
             VALUES (:category, :post_type, :department_id, :title, :description, :image, :featured, :published_at, :go_live_at, :show_until,
                :is_activity, :starts_at, :ends_at, :venue, :requirements, :contact_info)'
        )->execute($params);

        return (int) $pdo->lastInsertId();
    }

    // Updating a post that is already live: keep its original go-live moment unless a new, later one was asked for.
    if ($content['go_live_at'] === null || $content['go_live_at'] < $now) {
        $params['go_live_at'] = $existing['go_live_at'] ?: $existing['published_at'] . ' 00:00:00';
        $params['published_at'] = $existing['published_at'];
    }
    $params['id'] = $postId;
    $pdo->prepare(
        'UPDATE news_posts SET category = :category, post_type = :post_type, title = :title, description = :description, image = :image,
            is_featured = :featured, published_at = :published_at, go_live_at = :go_live_at, show_until = :show_until,
            is_activity = :is_activity, starts_at = :starts_at, ends_at = :ends_at, venue = :venue, requirements = :requirements,
            contact_info = :contact_info WHERE id = :id'
    )->execute($params);
    if (($existing['image'] ?? null) !== $content['image']) {
        deleteNewsImageIfUnused($existing['image'] ?? null);
    }

    return $postId;
}

/**
 * The administrator posts or edits a post directly (their own posts, or an edit to an
 * office's live post, in which case that office is told).
 *
 * @return array{success: bool, errors: string[]}
 */
function adminSaveLiveNewsPost(?int $postId, array $admin, array $content, bool $featured): array
{
    $existing = $postId !== null ? findNewsPostById($postId) : null;
    if ($postId !== null && $existing === null) {
        return ['success' => false, 'errors' => ['That post no longer exists.']];
    }

    $departmentId = $existing['department_id'] ?? null;
    $saved = publishNewsContent($postId, $content, $departmentId !== null ? (int) $departmentId : null, $featured);

    if ($existing !== null && $departmentId !== null) {
        notifyDepartment((int) $departmentId, 'The Municipal Administrator edited your live post "' . $content['title'] . '".', '/LGU-Link/news-post.php?id=' . $saved);
    }

    return ['success' => true, 'errors' => []];
}

/** Starts (or reopens) a draft that proposes changes to one of the office's own live posts. */
function startNewsChangeProposal(int $postId, array $user): array
{
    $post = findNewsPostById($postId);
    if ($post === null || (int) ($post['department_id'] ?? 0) !== (int) $user['department_id']) {
        return ['success' => false, 'errors' => ['That post is not yours to change.'], 'id' => 0];
    }
    if (!empty($post['archived_at'])) {
        return ['success' => false, 'errors' => ['The Municipal Administrator has taken this post down, so it cannot be changed.'], 'id' => 0];
    }

    // Continue an open proposal instead of starting a second one.
    $stmt = getDbConnection()->prepare("SELECT id FROM news_submissions WHERE post_id = :post AND status IN ('draft', 'pending_review', 'awaiting_office', 'rejected') ORDER BY id DESC LIMIT 1");
    $stmt->execute(['post' => $postId]);
    $open = $stmt->fetchColumn();
    if ($open !== false) {
        return ['success' => true, 'errors' => [], 'id' => (int) $open];
    }

    return ['success' => true, 'errors' => [], 'id' => createNewsSubmission($user, newsContentOf($post), $postId)];
}

/** The administrator archives a post (hides it from the public, keeps it); the office that wrote it is told. */
function adminArchiveNewsPost(int $postId): bool
{
    $post = findNewsPostById($postId);
    if ($post === null || !empty($post['archived_at'])) {
        return false;
    }
    getDbConnection()->prepare('UPDATE news_posts SET archived_at = NOW() WHERE id = :id AND archived_at IS NULL')->execute(['id' => $postId]);
    logAudit('news_archived', null, $post['title']);
    if ($post['department_id'] !== null) {
        notifyDepartment((int) $post['department_id'], 'The Municipal Administrator archived your post "' . $post['title'] . '". It is no longer on the website.', '/LGU-Link/office/news.php');
    }

    return true;
}

/** The administrator restores an archived post; it shows again according to its own schedule. */
function adminRestoreNewsPost(int $postId): bool
{
    $post = findNewsPostById($postId);
    if ($post === null || empty($post['archived_at'])) {
        return false;
    }
    getDbConnection()->prepare('UPDATE news_posts SET archived_at = NULL WHERE id = :id')->execute(['id' => $postId]);
    logAudit('news_restored', null, $post['title']);
    if ($post['department_id'] !== null) {
        notifyDepartment((int) $post['department_id'], 'The Municipal Administrator restored your post "' . $post['title'] . '".', '/LGU-Link/news-post.php?id=' . $postId);
    }

    return true;
}

/** The administrator permanently deletes a post. Only an archived post can be deleted. */
function adminDeleteLiveNewsPost(int $postId): bool
{
    $post = findNewsPostById($postId);
    if ($post === null || empty($post['archived_at']) || !deleteNewsPost($postId)) {
        return false;
    }
    logAudit('news_deleted', null, $post['title']);

    return true;
}

/** The schedule of a post in plain words: "As soon as it is approved, until Nov 5, 2026, 5:00 PM". */
function describeNewsSchedule(array $content): string
{
    $when = $content['go_live_at'] ?: ($content['display_at'] ?? null); // live posts carry their real posting time
    $from = !empty($when)
        ? 'Posts on ' . date('M j, Y, g:i A', strtotime($when))
        : 'Posts as soon as it is approved';
    $until = !empty($content['show_until'])
        ? ', shown until ' . date('M j, Y, g:i A', strtotime($content['show_until']))
        : ', stays up until removed';

    return $from . $until;
}

/** Label and color for where a live post is right now. */
function newsStateBadge(string $state): array
{
    return match ($state) {
        'scheduled' => ['Scheduled', 'bg-forwarded'],
        'expired' => ['Expired', 'bg-completed'],
        'archived' => ['Archived', 'bg-pending'],
        default => ['Live', 'bg-approved'],
    };
}
