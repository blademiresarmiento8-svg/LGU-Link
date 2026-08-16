<?php
/**
 * Admin-only write operations for the news_posts table, including image
 * upload handling. Requires includes/news-helpers.php (getDbConnection(),
 * getAllNewsPosts()) to already be loaded.
 */

const NEWS_UPLOAD_DIR = __DIR__ . '/../assets/uploads/news';
const NEWS_UPLOAD_URL_PREFIX = 'assets/uploads/news';
const NEWS_MAX_UPLOAD_BYTES = 5 * 1024 * 1024; // 5MB

const NEWS_ALLOWED_CATEGORIES = [
    'Official Announcement',
    'Emergency Advisory',
    'Public Health Update',
    'Holiday Advisory',
];

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

/**
 * Validates and saves a news post. Pass $id = null to create a new post,
 * or an existing post's id to update it in place. $file is the
 * $_FILES['image']-shaped array; required on create, optional on update
 * (omitting it keeps the post's current image).
 *
 * @return array{success: bool, errors: string[]}
 */
function saveNewsPost(?int $id, array $data, array $file): array
{
    $category = trim((string) ($data['category'] ?? ''));
    $title = trim((string) ($data['title'] ?? ''));
    $description = trim((string) ($data['description'] ?? ''));
    $publishedAt = trim((string) ($data['published_at'] ?? ''));
    $isFeatured = !empty($data['is_featured']) ? 1 : 0;

    $errors = [];
    if (!in_array($category, NEWS_ALLOWED_CATEGORIES, true)) {
        $errors[] = 'Choose a valid announcement category.';
    }
    if ($title === '') {
        $errors[] = 'Title is required.';
    }
    if ($description === '') {
        $errors[] = 'Content description is required.';
    }
    $publishedDate = \DateTime::createFromFormat('Y-m-d', $publishedAt);
    if ($publishedDate === false || $publishedDate->format('Y-m-d') !== $publishedAt) {
        $errors[] = 'Enter a valid publish date.';
    }

    $upload = uploadNewsImage($file);
    if ($upload['error'] !== null) {
        $errors[] = $upload['error'];
    }

    $existing = $id !== null ? findNewsPostById($id) : null;
    if ($id !== null && $existing === null) {
        $errors[] = 'That post no longer exists.';
    }

    if ($id === null && $upload['path'] === null && $upload['error'] === null) {
        $errors[] = 'An image is required.';
    }

    if (!empty($errors)) {
        // Don't leave an orphaned upload behind if validation failed elsewhere.
        if ($upload['path'] !== null) {
            deleteNewsImageFile($upload['path']);
        }

        return ['success' => false, 'errors' => $errors];
    }

    $imagePath = $upload['path'] ?? $existing['image'] ?? null;
    $oldImagePath = $upload['path'] !== null ? ($existing['image'] ?? null) : null;

    $pdo = getDbConnection();

    if ($id === null) {
        $stmt = $pdo->prepare(
            'INSERT INTO news_posts (category, title, description, image, is_featured, published_at)
             VALUES (:category, :title, :description, :image, :is_featured, :published_at)'
        );
        $stmt->execute([
            'category' => $category,
            'title' => $title,
            'description' => $description,
            'image' => $imagePath,
            'is_featured' => $isFeatured,
            'published_at' => $publishedAt,
        ]);
    } else {
        $stmt = $pdo->prepare(
            'UPDATE news_posts
             SET category = :category, title = :title, description = :description,
                 image = :image, is_featured = :is_featured, published_at = :published_at
             WHERE id = :id'
        );
        $stmt->execute([
            'category' => $category,
            'title' => $title,
            'description' => $description,
            'image' => $imagePath,
            'is_featured' => $isFeatured,
            'published_at' => $publishedAt,
            'id' => $id,
        ]);
    }

    // Replacing an image on update: remove the file it replaced.
    if ($oldImagePath !== null && $oldImagePath !== $imagePath) {
        deleteNewsImageFile($oldImagePath);
    }

    return ['success' => true, 'errors' => []];
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
        deleteNewsImageFile($post['image']);

        return true;
    }

    return false;
}
