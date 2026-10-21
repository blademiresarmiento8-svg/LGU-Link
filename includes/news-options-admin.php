<?php
/**
 * System-admin-only changes to the news Type and Category lists (sysadmin/news-options.php).
 *
 * - A type has a fixed internal key (its slug, made from the first name given) that posts are
 *   stored under, so renaming it later changes only what people read.
 * - A category is stored on posts by name, so renaming one updates every post, draft and
 *   history entry that used it.
 * - Something that posts still use can be hidden (not offered for new posts) but not deleted.
 * - There is always at least one visible type and one visible category.
 *
 * Requires includes/news-helpers.php and audit-helpers.php. Every function returns
 * array{success: bool, errors: string[]}.
 */

require_once __DIR__ . '/news-helpers.php';
require_once __DIR__ . '/audit-helpers.php';

const NEWS_TYPE_LABEL_MAX = 40;
const NEWS_TYPE_DESCRIPTION_MAX = 120;
const NEWS_CATEGORY_NAME_MAX = 60;

function newsOptionFail(string ...$errors): array
{
    return ['success' => false, 'errors' => $errors];
}

/** How many posts and drafts use this type. */
function newsTypeUsage(string $slug): int
{
    $pdo = getDbConnection();
    $total = 0;
    foreach (['news_posts', 'news_submissions'] as $table) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM $table WHERE post_type = :slug");
        $stmt->execute(['slug' => $slug]);
        $total += (int) $stmt->fetchColumn();
    }

    return $total;
}

/** How many posts and drafts use this category. */
function newsCategoryUsage(string $name): int
{
    $pdo = getDbConnection();
    $total = 0;
    foreach (['news_posts', 'news_submissions'] as $table) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM $table WHERE category = :name");
        $stmt->execute(['name' => $name]);
        $total += (int) $stmt->fetchColumn();
    }

    return $total;
}

/** "Public Notices" -> "public-notices"; made unique against the existing types. */
function newsTypeSlugFor(string $label): string
{
    $slug = trim((string) preg_replace('/[^a-z0-9]+/', '-', strtolower($label)), '-');
    $slug = substr($slug !== '' ? $slug : 'type', 0, 26);

    $taken = array_keys(newsOptions(true)['types']);
    $candidate = $slug;
    for ($i = 2; in_array($candidate, $taken, true); $i++) {
        $candidate = $slug . '-' . $i;
    }

    return $candidate;
}

function newsOptionNameTaken(string $table, string $column, string $value, ?int $exceptId): bool
{
    $stmt = getDbConnection()->prepare("SELECT id FROM $table WHERE LOWER($column) = LOWER(:value) AND id <> :id LIMIT 1");
    $stmt->execute(['value' => $value, 'id' => $exceptId ?? 0]);

    return (bool) $stmt->fetchColumn();
}

function activeNewsOptionCount(string $table, ?int $exceptId): int
{
    $stmt = getDbConnection()->prepare("SELECT COUNT(*) FROM $table WHERE is_active = 1 AND id <> :id");
    $stmt->execute(['id' => $exceptId ?? 0]);

    return (int) $stmt->fetchColumn();
}

function nextNewsOptionOrder(string $table): int
{
    return (int) getDbConnection()->query("SELECT COALESCE(MAX(sort_order), 0) + 1 FROM $table")->fetchColumn();
}

/** Adds a type ($id null) or edits one. */
function saveNewsType(?int $id, array $input): array
{
    $label = trim((string) preg_replace('/\s+/u', ' ', (string) ($input['label'] ?? '')));
    $description = trim((string) ($input['description'] ?? ''));
    $icon = (string) ($input['icon'] ?? '');
    $color = (string) ($input['color'] ?? '');
    $homeLimit = (int) ($input['home_limit'] ?? 0);
    $active = !empty($input['is_active']);

    $errors = [];
    if ($label === '' || mb_strlen($label) > NEWS_TYPE_LABEL_MAX) {
        $errors[] = 'Give the type a name (up to ' . NEWS_TYPE_LABEL_MAX . ' characters).';
    } elseif (newsOptionNameTaken('news_types', 'label', $label, $id)) {
        $errors[] = 'There is already a type called "' . $label . '".';
    }
    if (mb_strlen($description) > NEWS_TYPE_DESCRIPTION_MAX) {
        $errors[] = 'The description must be ' . NEWS_TYPE_DESCRIPTION_MAX . ' characters or fewer.';
    }
    if (!isset(NEWS_ICONS[$icon])) {
        $errors[] = 'Choose an icon.';
    }
    if (!isset(NEWS_COLORS[$color])) {
        $errors[] = 'Choose a color.';
    }
    if ($homeLimit < 1 || $homeLimit > 12) {
        $errors[] = 'The number of posts on the homepage must be between 1 and 12.';
    }

    $existing = null;
    if ($id !== null) {
        $stmt = getDbConnection()->prepare('SELECT * FROM news_types WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $existing = $stmt->fetch() ?: null;
        if ($existing === null) {
            return newsOptionFail('That type no longer exists.');
        }
    }
    if (!$active && activeNewsOptionCount('news_types', $id) === 0) {
        $errors[] = 'At least one type must stay visible, or nobody could write a post.';
    }
    if (!empty($errors)) {
        return ['success' => false, 'errors' => $errors];
    }

    $pdo = getDbConnection();
    if ($existing === null) {
        $pdo->prepare('INSERT INTO news_types (slug, label, description, icon, color, home_limit, sort_order, is_active) VALUES (:slug, :label, :description, :icon, :color, :limit, :order, :active)')
            ->execute(['slug' => newsTypeSlugFor($label), 'label' => $label, 'description' => $description === '' ? null : $description, 'icon' => $icon, 'color' => $color, 'limit' => $homeLimit, 'order' => nextNewsOptionOrder('news_types'), 'active' => $active ? 1 : 0]);
        logAudit('news_options_changed', null, null, 'Added the news type "' . $label . '"');
    } else {
        $pdo->prepare('UPDATE news_types SET label = :label, description = :description, icon = :icon, color = :color, home_limit = :limit, is_active = :active WHERE id = :id')
            ->execute(['label' => $label, 'description' => $description === '' ? null : $description, 'icon' => $icon, 'color' => $color, 'limit' => $homeLimit, 'active' => $active ? 1 : 0, 'id' => $id]);
        logAudit('news_options_changed', null, null, 'Updated the news type "' . $existing['label'] . '"' . ($existing['label'] !== $label ? ' (now "' . $label . '")' : ''));
    }
    newsOptions(true);

    return ['success' => true, 'errors' => []];
}

function deleteNewsType(int $id): array
{
    $pdo = getDbConnection();
    $stmt = $pdo->prepare('SELECT * FROM news_types WHERE id = :id');
    $stmt->execute(['id' => $id]);
    $type = $stmt->fetch();
    if (!$type) {
        return newsOptionFail('That type no longer exists.');
    }
    if (newsTypeUsage($type['slug']) > 0) {
        return newsOptionFail('"' . $type['label'] . '" is used by existing posts, so it cannot be deleted. Hide it instead and it will no longer be offered for new posts.');
    }
    if ((int) $type['is_active'] === 1 && activeNewsOptionCount('news_types', $id) === 0) {
        return newsOptionFail('At least one type must stay.');
    }

    $pdo->prepare('DELETE FROM news_types WHERE id = :id')->execute(['id' => $id]);
    logAudit('news_options_changed', null, null, 'Deleted the news type "' . $type['label'] . '"');
    newsOptions(true);

    return ['success' => true, 'errors' => []];
}

/** Adds a category ($id null) or edits one; renaming updates every post and draft that used the old name. */
function saveNewsCategory(?int $id, array $input): array
{
    $name = trim((string) preg_replace('/\s+/u', ' ', (string) ($input['name'] ?? '')));
    $color = (string) ($input['color'] ?? '');
    $active = !empty($input['is_active']);

    $errors = [];
    if ($name === '' || mb_strlen($name) > NEWS_CATEGORY_NAME_MAX) {
        $errors[] = 'Give the category a name (up to ' . NEWS_CATEGORY_NAME_MAX . ' characters).';
    } elseif (newsOptionNameTaken('news_categories', 'name', $name, $id)) {
        $errors[] = 'There is already a category called "' . $name . '".';
    }
    if (!isset(NEWS_COLORS[$color])) {
        $errors[] = 'Choose a color.';
    }

    $existing = null;
    if ($id !== null) {
        $stmt = getDbConnection()->prepare('SELECT * FROM news_categories WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $existing = $stmt->fetch() ?: null;
        if ($existing === null) {
            return newsOptionFail('That category no longer exists.');
        }
    }
    if (!$active && activeNewsOptionCount('news_categories', $id) === 0) {
        $errors[] = 'At least one category must stay visible, or nobody could write a post.';
    }
    if (!empty($errors)) {
        return ['success' => false, 'errors' => $errors];
    }

    $pdo = getDbConnection();
    if ($existing === null) {
        $pdo->prepare('INSERT INTO news_categories (name, color, sort_order, is_active) VALUES (:name, :color, :order, :active)')
            ->execute(['name' => $name, 'color' => $color, 'order' => nextNewsOptionOrder('news_categories'), 'active' => $active ? 1 : 0]);
        logAudit('news_options_changed', null, null, 'Added the news category "' . $name . '"');
    } else {
        $pdo->beginTransaction();
        $pdo->prepare('UPDATE news_categories SET name = :name, color = :color, is_active = :active WHERE id = :id')
            ->execute(['name' => $name, 'color' => $color, 'active' => $active ? 1 : 0, 'id' => $id]);
        if ($existing['name'] !== $name) {
            renameNewsCategoryEverywhere($existing['name'], $name);
        }
        $pdo->commit();
        logAudit('news_options_changed', null, null, 'Updated the news category "' . $existing['name'] . '"' . ($existing['name'] !== $name ? ' (now "' . $name . '")' : ''));
    }
    newsOptions(true);

    return ['success' => true, 'errors' => []];
}

/** Posts and drafts store the category by name, and so do the history snapshots; keep them all in step. */
function renameNewsCategoryEverywhere(string $old, string $new): void
{
    $pdo = getDbConnection();
    foreach (['news_posts', 'news_submissions'] as $table) {
        $pdo->prepare("UPDATE $table SET category = :new WHERE category = :old")->execute(['new' => $new, 'old' => $old]);
    }

    $needle = '"category":' . json_encode($old, JSON_UNESCAPED_UNICODE);
    $stmt = $pdo->prepare('SELECT id, snapshot FROM news_submission_events WHERE snapshot LIKE :pattern');
    $stmt->execute(['pattern' => '%' . addcslashes($needle, '%_\\') . '%']);
    $update = $pdo->prepare('UPDATE news_submission_events SET snapshot = :snapshot WHERE id = :id');
    foreach ($stmt->fetchAll() as $event) {
        $snapshot = json_decode($event['snapshot'], true);
        if (is_array($snapshot) && ($snapshot['category'] ?? null) === $old) {
            $snapshot['category'] = $new;
            $update->execute(['snapshot' => json_encode($snapshot, JSON_UNESCAPED_UNICODE), 'id' => $event['id']]);
        }
    }
}

function deleteNewsCategory(int $id): array
{
    $pdo = getDbConnection();
    $stmt = $pdo->prepare('SELECT * FROM news_categories WHERE id = :id');
    $stmt->execute(['id' => $id]);
    $category = $stmt->fetch();
    if (!$category) {
        return newsOptionFail('That category no longer exists.');
    }
    if (newsCategoryUsage($category['name']) > 0) {
        return newsOptionFail('"' . $category['name'] . '" is used by existing posts, so it cannot be deleted. Hide it instead and it will no longer be offered for new posts.');
    }
    if ((int) $category['is_active'] === 1 && activeNewsOptionCount('news_categories', $id) === 0) {
        return newsOptionFail('At least one category must stay.');
    }

    $pdo->prepare('DELETE FROM news_categories WHERE id = :id')->execute(['id' => $id]);
    logAudit('news_options_changed', null, null, 'Deleted the news category "' . $category['name'] . '"');
    newsOptions(true);

    return ['success' => true, 'errors' => []];
}

/** Moves a type or category one place up or down the list. */
function moveNewsOption(string $kind, int $id, string $direction): array
{
    $table = $kind === 'type' ? 'news_types' : ($kind === 'category' ? 'news_categories' : null);
    if ($table === null || !in_array($direction, ['up', 'down'], true)) {
        return newsOptionFail('Unknown action.');
    }

    $pdo = getDbConnection();
    $ids = array_map('intval', $pdo->query("SELECT id FROM $table ORDER BY sort_order, id")->fetchAll(PDO::FETCH_COLUMN));
    $index = array_search($id, $ids, true);
    $swapWith = $direction === 'up' ? $index - 1 : $index + 1;
    if ($index === false || $swapWith < 0 || $swapWith >= count($ids)) {
        return ['success' => true, 'errors' => []]; // already at the end of the list
    }

    [$ids[$index], $ids[$swapWith]] = [$ids[$swapWith], $ids[$index]];
    $update = $pdo->prepare("UPDATE $table SET sort_order = :order WHERE id = :id");
    foreach ($ids as $position => $optionId) {
        $update->execute(['order' => $position + 1, 'id' => $optionId]);
    }
    newsOptions(true);

    return ['success' => true, 'errors' => []];
}
