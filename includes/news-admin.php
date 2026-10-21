<?php
/**
 * Photo upload handling and deleting live posts. (Writing posts, and the review of
 * offices' posts, is in includes/news-workflow.php.) Requires includes/news-helpers.php (getDbConnection(),
 * getAllNewsPosts()) to already be loaded.
 */

const NEWS_UPLOAD_DIR = __DIR__ . '/../assets/uploads/news';
const NEWS_UPLOAD_URL_PREFIX = 'assets/uploads/news';
const NEWS_MAX_UPLOAD_BYTES = 5 * 1024 * 1024; // 5MB

/**
 * Validates an uploaded image ($_FILES['image']-shaped array) and moves
 * it into assets/uploads/news/ under a generated, collision-proof name.
 * The extension is derived from the file's real detected type (not the
 * client-supplied filename), and only real images are accepted.
 *
 * @return array{success: bool, path: ?string, error: ?string}
 */
function uploadNewsImage(array $file): array
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return ['success' => false, 'path' => null, 'error' => null];
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'path' => null, 'error' => 'Image upload failed. Please try again.'];
    }

    if ($file['size'] > NEWS_MAX_UPLOAD_BYTES) {
        return ['success' => false, 'path' => null, 'error' => 'Image is too large (max 5MB).'];
    }

    $info = @getimagesize($file['tmp_name']);
    if ($info === false) {
        return ['success' => false, 'path' => null, 'error' => 'That file is not a valid image.'];
    }

    $extensionByMime = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/gif' => 'gif',
        'image/webp' => 'webp',
    ];
    $extension = $extensionByMime[$info['mime']] ?? null;
    if ($extension === null) {
        return ['success' => false, 'path' => null, 'error' => 'Unsupported image type. Use JPG, PNG, GIF, or WebP.'];
    }

    if (!is_dir(NEWS_UPLOAD_DIR) && !mkdir(NEWS_UPLOAD_DIR, 0777, true) && !is_dir(NEWS_UPLOAD_DIR)) {
        return ['success' => false, 'path' => null, 'error' => 'Could not prepare the upload folder.'];
    }

    $filename = bin2hex(random_bytes(12)) . '.' . $extension;
    $destination = NEWS_UPLOAD_DIR . '/' . $filename;

    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        return ['success' => false, 'path' => null, 'error' => 'Could not save the uploaded image.'];
    }

    return ['success' => true, 'path' => NEWS_UPLOAD_URL_PREFIX . '/' . $filename, 'error' => null];
}

function deleteNewsImageFile(string $relativePath): void
{
    $full = __DIR__ . '/../' . ltrim($relativePath, '/');
    if (is_file($full)) {
        @unlink($full);
    }
}

/** Deletes an uploaded photo file only when no post or submission still uses it. */
function deleteNewsImageIfUnused(?string $path): void
{
    if ($path === null || $path === '') {
        return;
    }
    $pdo = getDbConnection();
    foreach (['news_posts', 'news_submissions'] as $table) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM $table WHERE image = :image");
        $stmt->execute(['image' => $path]);
        if ((int) $stmt->fetchColumn() > 0) {
            return;
        }
    }
    deleteNewsImageFile($path);
}

function deleteNewsPost(int $id): bool
{
    $post = findNewsPostById($id);
    if ($post === null) {
        return false;
    }

    $pdo = getDbConnection();
    $stmt = $pdo->prepare('DELETE FROM news_posts WHERE id = :id');
    $stmt->execute(['id' => $id]);

    if ($stmt->rowCount() > 0) {
        deleteNewsImageIfUnused($post['image'] ?? null);

        return true;
    }

    return false;
}
