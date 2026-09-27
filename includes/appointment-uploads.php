<?php
/**
 * Attachment upload handling for citizen appointment requests. Modeled on
 * includes/news-admin.php's uploadNewsImage(): real-type validation via
 * getimagesize()/finfo (never the client-supplied filename or MIME),
 * collision-proof random filenames, auto-created upload folder. Unlike the
 * news upload, this accepts an image OR a PDF, and images are recompressed
 * (resized + re-encoded) so citizens don't need to shrink phone-camera
 * photos themselves before attaching them.
 */

const APPOINTMENT_UPLOAD_DIR = __DIR__ . '/../assets/uploads/appointments';
const APPOINTMENT_UPLOAD_URL_PREFIX = 'assets/uploads/appointments';
const APPOINTMENT_MAX_UPLOAD_BYTES = 8 * 1024 * 1024; // 8MB, checked before any processing
const APPOINTMENT_MAX_IMAGE_DIMENSION = 1600; // px, longest side after compression
const APPOINTMENT_IMAGE_QUALITY = 78; // JPEG quality, 0-100

/**
 * Validates an uploaded attachment ($_FILES['attachment']-shaped array) and
 * moves it into assets/uploads/appointments/ under a generated,
 * collision-proof name. Images are resized/recompressed; PDFs are stored
 * as-is (size-capped only — no safe way to shrink a PDF without a new
 * library). Returns success with a null path when no file was chosen at
 * all, since the attachment is optional.
 *
 * @return array{success: bool, path: ?string, error: ?string}
 */
function uploadAppointmentAttachment(array $file): array
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return ['success' => true, 'path' => null, 'error' => null];
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'path' => null, 'error' => 'File upload failed. Please try again.'];
    }

    if ($file['size'] > APPOINTMENT_MAX_UPLOAD_BYTES) {
        return ['success' => false, 'path' => null, 'error' => 'File is too large (max 8MB).'];
    }

    if (!is_dir(APPOINTMENT_UPLOAD_DIR) && !mkdir(APPOINTMENT_UPLOAD_DIR, 0777, true) && !is_dir(APPOINTMENT_UPLOAD_DIR)) {
        return ['success' => false, 'path' => null, 'error' => 'Could not prepare the upload folder.'];
    }

    $info = @getimagesize($file['tmp_name']);
    if ($info !== false) {
        return compressAndStoreAppointmentImage($file['tmp_name'], $info);
    }

    if (isAppointmentPdf($file['tmp_name'])) {
        return storeAppointmentPdf($file['tmp_name']);
    }

    return ['success' => false, 'path' => null, 'error' => 'Unsupported file type. Attach a JPG, PNG, GIF, WebP, or PDF file.'];
}

/** Real-type PDF check: detected MIME via finfo, plus the file's magic bytes — never the client filename. */
function isAppointmentPdf(string $tmpPath): bool
{
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $tmpPath);
    finfo_close($finfo);

    if ($mime !== 'application/pdf') {
        return false;
    }

    $handle = fopen($tmpPath, 'rb');
    $header = $handle !== false ? fread($handle, 5) : '';
    if ($handle !== false) {
        fclose($handle);
    }

    return $header === '%PDF-';
}

function storeAppointmentPdf(string $tmpPath): array
{
    $filename = bin2hex(random_bytes(12)) . '.pdf';
    $destination = APPOINTMENT_UPLOAD_DIR . '/' . $filename;

    if (!move_uploaded_file($tmpPath, $destination)) {
        return ['success' => false, 'path' => null, 'error' => 'Could not save the uploaded file.'];
    }

    return ['success' => true, 'path' => APPOINTMENT_UPLOAD_URL_PREFIX . '/' . $filename, 'error' => null];
}

/**
 * Resizes (cap: APPOINTMENT_MAX_IMAGE_DIMENSION on the longer side) and
 * re-encodes an image so citizens don't need to shrink phone photos
 * themselves. PNGs with an alpha channel stay PNG (max compression) so
 * transparency isn't destroyed; everything else is re-encoded as JPEG,
 * which is dramatically smaller for photos.
 */
function compressAndStoreAppointmentImage(string $tmpPath, array $info): array
{
    $loaders = [
        'image/jpeg' => 'imagecreatefromjpeg',
        'image/png' => 'imagecreatefrompng',
        'image/gif' => 'imagecreatefromgif',
        'image/webp' => 'imagecreatefromwebp',
    ];
    $mime = $info['mime'];
    $loader = $loaders[$mime] ?? null;
    if ($loader === null) {
        return ['success' => false, 'path' => null, 'error' => 'Unsupported image type. Use JPG, PNG, GIF, or WebP.'];
    }

    $source = @$loader($tmpPath);
    if ($source === false) {
        return ['success' => false, 'path' => null, 'error' => 'That file is not a valid image.'];
    }

    $keepAsPng = $mime === 'image/png' && pngHasAlphaChannel($tmpPath);

    $originalWidth = imagesx($source);
    $originalHeight = imagesy($source);
    $longestSide = max($originalWidth, $originalHeight);

    if ($longestSide > APPOINTMENT_MAX_IMAGE_DIMENSION) {
        $scale = APPOINTMENT_MAX_IMAGE_DIMENSION / $longestSide;
        $targetWidth = (int) round($originalWidth * $scale);
        $targetHeight = (int) round($originalHeight * $scale);
    } else {
        $targetWidth = $originalWidth;
        $targetHeight = $originalHeight;
    }

    $canvas = imagecreatetruecolor($targetWidth, $targetHeight);

    if ($keepAsPng) {
        imagealphablending($canvas, false);
        imagesavealpha($canvas, true);
        $transparent = imagecolorallocatealpha($canvas, 0, 0, 0, 127);
        imagefilledrectangle($canvas, 0, 0, $targetWidth, $targetHeight, $transparent);
    }

    imagecopyresampled($canvas, $source, 0, 0, 0, 0, $targetWidth, $targetHeight, $originalWidth, $originalHeight);
    imagedestroy($source);

    $extension = $keepAsPng ? 'png' : 'jpg';
    $filename = bin2hex(random_bytes(12)) . '.' . $extension;
    $destination = APPOINTMENT_UPLOAD_DIR . '/' . $filename;

    $saved = $keepAsPng
        ? imagepng($canvas, $destination, 9)
        : imagejpeg($canvas, $destination, APPOINTMENT_IMAGE_QUALITY);
    imagedestroy($canvas);

    if (!$saved) {
        return ['success' => false, 'path' => null, 'error' => 'Could not save the uploaded image.'];
    }

    return ['success' => true, 'path' => APPOINTMENT_UPLOAD_URL_PREFIX . '/' . $filename, 'error' => null];
}

/**
 * Reads the PNG IHDR chunk's color-type byte directly (offset 25) rather
 * than decoding the whole image — color type 4 (grayscale+alpha) or 6
 * (truecolor+alpha) means the image has a real alpha channel worth
 * preserving. Indexed PNGs with a tRNS transparency chunk (color type 3)
 * are treated as opaque here; that's a rare case for document attachments
 * and JPEG re-encoding is worth it for the size savings.
 */
function pngHasAlphaChannel(string $path): bool
{
    $handle = fopen($path, 'rb');
    if ($handle === false) {
        return false;
    }

    $header = fread($handle, 26);
    fclose($handle);

    if ($header === false || strlen($header) < 26) {
        return false;
    }

    $colorType = ord($header[25]);

    return $colorType === 4 || $colorType === 6;
}

function deleteAppointmentAttachmentFile(string $relativePath): void
{
    $full = __DIR__ . '/../' . ltrim($relativePath, '/');
    if (is_file($full)) {
        @unlink($full);
    }
}
