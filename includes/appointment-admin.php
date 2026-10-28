<?php
require_once __DIR__ . '/input-limits.php';
require_once __DIR__ . '/schedule-helpers.php';
require_once __DIR__ . '/organization-fields.php';
require_once __DIR__ . '/registration-helpers.php';

/**
 * Write operations for the appointments table. Requires
 * includes/appointment-helpers.php (getDbConnection(), APPOINTMENT_CATEGORIES,
 * findAppointmentById()), includes/appointment-uploads.php
 * (uploadAppointmentAttachment()), and includes/department-helpers.php
 * (findDepartmentById(), used by the Municipal Administrator's forward
 * action) to already be loaded.
 */

/**
 * Validates and inserts a new appointment request from a citizen. Always a
 * create — citizens cannot edit a submitted request, only cancel it (see
 * cancelAppointmentByCitizen()) while it's still pending_review or forwarded.
 *
 * @param array<string, mixed> $data Raw $_POST fields: category, title,
 *   description, contact_number, email, preferred_date, preferred_time.
 * @param array $file $_FILES['attachment']-shaped array.
 * @return array{success: bool, errors: string[], id?: int, email?: string, title?: string}
 */
function insertAppointment(int $userId, array $data, array $file): array
{
    $category = trim((string) ($data['category'] ?? ''));
    $title = trim((string) ($data['title'] ?? ''));
    $description = trim((string) ($data['description'] ?? ''));
    $contactNumber = trim((string) ($data['contact_number'] ?? ''));
    $email = trim((string) ($data['email'] ?? ''));
    $preferredDate = trim((string) ($data['preferred_date'] ?? ''));
    $preferredTime = trim((string) ($data['preferred_time'] ?? ''));

    $errors = [];
    if (!in_array($category, APPOINTMENT_CATEGORIES, true)) {
        $errors[] = 'Choose a valid concern category.';
    }
    if ($title === '') {
        $errors[] = 'Title is required.';
    } elseif (($tooLong = tooLongMessage('The title', $title, 200)) !== null) {
        $errors[] = $tooLong;
    }
    if ($description === '') {
        $errors[] = 'Description is required.';
    } elseif (($tooLong = tooLongMessage('The description', $description, LONG_TEXT_MAX_LENGTH)) !== null) {
        $errors[] = $tooLong;
    }
    // Stored the same way as at sign-up: 09XXXXXXXXX.
    if ($contactNumber === '') {
        $errors[] = 'Contact number is required.';
    } elseif (($mobile = normalizePhilippineMobile($contactNumber)) === null) {
        $errors[] = 'Please enter a valid mobile number, like 09123456789.';
    } else {
        $contactNumber = $mobile;
    }
    if ($email === '' || strlen($email) > 150 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'A valid email address is required.';
    }

    if ($preferredDate !== '') {
        $parsedDate = \DateTime::createFromFormat('Y-m-d', $preferredDate);
        if ($parsedDate === false || $parsedDate->format('Y-m-d') !== $preferredDate) {
            $errors[] = 'Enter a valid preferred date.';
            $preferredDate = '';
        }
    }
    if ($preferredTime !== '') {
        $parsedTime = \DateTime::createFromFormat('H:i', $preferredTime);
        if ($parsedTime === false || $parsedTime->format('H:i') !== $preferredTime) {
            $errors[] = 'Enter a valid preferred time.';
            $preferredTime = '';
        }
    }

    $organization = validateOrganizationFields($data);
    $errors = array_merge($errors, $organization['errors']);

    // A preferred date/time is optional, but when given it must be bookable.
    if ($preferredDate !== '' || $preferredTime !== '') {
        $errors = array_merge($errors, validateBookingDateTime($preferredDate, $preferredTime));
    }

    $upload = uploadAppointmentAttachment($file);
    if ($upload['error'] !== null) {
        $errors[] = $upload['error'];
    }

    if (!empty($errors)) {
        // Don't leave an orphaned upload behind if validation failed elsewhere.
        if ($upload['path'] !== null) {
            deleteAppointmentAttachmentFile($upload['path']);
        }

        return ['success' => false, 'errors' => $errors];
    }

    $pdo = getDbConnection();
    $stmt = $pdo->prepare(
        'INSERT INTO appointments (
            user_id, category, title, description, attachment_path, attachment_original_name,
            contact_number, email, organization, position_title, preferred_date, preferred_time
        ) VALUES (
            :user_id, :category, :title, :description, :attachment_path, :attachment_original_name,
            :contact_number, :email, :organization, :position_title, :preferred_date, :preferred_time
        )'
    );
    $stmt->execute([
        'user_id' => $userId,
        'category' => $category,
        'title' => $title,
        'description' => $description,
        'attachment_path' => $upload['path'],
        'attachment_original_name' => $upload['path'] !== null && isset($file['name']) ? shortenFileName((string) $file['name']) : null,
        'contact_number' => $contactNumber,
        'email' => $email,
        'organization' => $organization['organization'],
        'position_title' => $organization['position_title'],
        'preferred_date' => $preferredDate !== '' ? $preferredDate : null,
        'preferred_time' => $preferredTime !== '' ? $preferredTime : null,
    ]);

    return ['success' => true, 'errors' => [], 'id' => (int) $pdo->lastInsertId(), 'email' => $email, 'title' => $title];
}

/**
 * Lets a citizen cancel their own request, only while it's still
 * pending_review or forwarded (once an office schedules it, cancelling
 * needs a human conversation, not a self-service button).
 */
function cancelAppointmentByCitizen(int $appointmentId, int $userId): bool
{
    $pdo = getDbConnection();
    $stmt = $pdo->prepare(
        "UPDATE appointments
         SET status = 'cancelled'
         WHERE id = :id AND user_id = :user_id AND status IN ('pending_review', 'forwarded')"
    );
    $stmt->execute(['id' => $appointmentId, 'user_id' => $userId]);

    return $stmt->rowCount() > 0;
}

/**
 * Municipal Administrator triage action: forwards a pending concern to the
 * department that should handle it. Only allowed while still
 * pending_review — a concern is triaged once, then it's the office's turn.
 *
 * @return array{success: bool, errors: string[]}
 */
function forwardAppointmentToDepartment(int $appointmentId, int $departmentId, string $adminNotes, int $reviewerUserId): array
{
    if (($tooLong = tooLongMessage('The note', trim($adminNotes), NOTE_MAX_LENGTH)) !== null) {
        return ['success' => false, 'errors' => [$tooLong]];
    }
    if (findDepartmentById($departmentId) === null) {
        return ['success' => false, 'errors' => ['Choose a valid department.']];
    }
    if (in_array($departmentId, getSuspendedDepartmentIds(), true)) {
        return ['success' => false, 'errors' => ['That office\'s account is suspended. Choose another department or ask the system admin to unsuspend it.']];
    }
    if (!departmentHasOfficeAccount($departmentId)) {
        return ['success' => false, 'errors' => ['That department has no office account yet, so nobody could schedule the concern.']];
    }

    $pdo = getDbConnection();
    $stmt = $pdo->prepare(
        "UPDATE appointments
         SET status = 'forwarded', department_id = :department_id, admin_notes = :admin_notes, reviewed_by = :reviewer, reviewed_at = NOW()
         WHERE id = :id AND status = 'pending_review'"
    );
    $stmt->execute([
        'department_id' => $departmentId,
        'admin_notes' => $adminNotes !== '' ? $adminNotes : null,
        'reviewer' => $reviewerUserId,
        'id' => $appointmentId,
    ]);

    if ($stmt->rowCount() === 0) {
        return ['success' => false, 'errors' => ['That request is no longer awaiting review.']];
    }

    return ['success' => true, 'errors' => []];
}

/**
 * Municipal Administrator triage action: rejects a pending concern with a
 * reason the citizen will see. Only allowed while still pending_review.
 *
 * @return array{success: bool, errors: string[]}
 */
function rejectAppointment(int $appointmentId, string $reason, int $reviewerUserId): array
{
    $reason = trim($reason);
    if ($reason === '') {
        return ['success' => false, 'errors' => ['A rejection reason is required.']];
    }
    if (($tooLong = tooLongMessage('The reason', $reason, NOTE_MAX_LENGTH)) !== null) {
        return ['success' => false, 'errors' => [$tooLong]];
    }

    $pdo = getDbConnection();
    $stmt = $pdo->prepare(
        "UPDATE appointments
         SET status = 'rejected', rejection_reason = :reason, reviewed_by = :reviewer, reviewed_at = NOW()
         WHERE id = :id AND status = 'pending_review'"
    );
    $stmt->execute([
        'reason' => $reason,
        'reviewer' => $reviewerUserId,
        'id' => $appointmentId,
    ]);

    if ($stmt->rowCount() === 0) {
        return ['success' => false, 'errors' => ['That request is no longer awaiting review.']];
    }

    return ['success' => true, 'errors' => []];
}

/**
 * Office action: sets the actual schedule for a request forwarded to that
 * office. Scoped to $departmentId (taken from the acting office account's
 * own session, never trusted from the request) so one office can never
 * schedule another office's appointment even if it guesses an id. Only
 * allowed while still forwarded.
 *
 * @return array{success: bool, errors: string[]}
 */
function scheduleAppointment(int $appointmentId, int $departmentId, string $scheduledDate, string $scheduledTime, string $officeNote, int $scheduledByUserId): array
{
    $errors = [];

    $parsedDate = \DateTime::createFromFormat('Y-m-d', $scheduledDate);
    if ($parsedDate === false || $parsedDate->format('Y-m-d') !== $scheduledDate) {
        $errors[] = 'Enter a valid schedule date.';
    }
    $parsedTime = \DateTime::createFromFormat('H:i', $scheduledTime);
    if ($parsedTime === false || $parsedTime->format('H:i') !== $scheduledTime) {
        $errors[] = 'Enter a valid schedule time.';
    }

    if (empty($errors)) {
        $errors = validateBookingDateTime($scheduledDate, $scheduledTime);
    }
    if (($tooLong = tooLongMessage('The note', trim($officeNote), NOTE_MAX_LENGTH)) !== null) {
        $errors[] = $tooLong;
    }

    if (!empty($errors)) {
        return ['success' => false, 'errors' => $errors];
    }

    $appointment = findAppointmentById($appointmentId);
    if ($appointment === null || (int) $appointment['department_id'] !== $departmentId) {
        return ['success' => false, 'errors' => ['That request does not belong to your office.']];
    }

    $officeNote = trim($officeNote);
    $adminNotes = trim((string) ($appointment['admin_notes'] ?? ''));
    if ($officeNote !== '') {
        $adminNotes = $adminNotes !== '' ? "{$adminNotes}\n\nOffice note: {$officeNote}" : "Office note: {$officeNote}";
    }

    $pdo = getDbConnection();
    $stmt = $pdo->prepare(
        "UPDATE appointments
         SET status = 'scheduled', scheduled_date = :scheduled_date, scheduled_time = :scheduled_time,
             admin_notes = :admin_notes, scheduled_by = :scheduled_by, scheduled_at = NOW()
         WHERE id = :id AND department_id = :department_id AND status = 'forwarded'"
    );
    $stmt->execute([
        'scheduled_date' => $scheduledDate,
        'scheduled_time' => $scheduledTime,
        'admin_notes' => $adminNotes !== '' ? $adminNotes : null,
        'scheduled_by' => $scheduledByUserId,
        'id' => $appointmentId,
        'department_id' => $departmentId,
    ]);

    if ($stmt->rowCount() === 0) {
        return ['success' => false, 'errors' => ['That request is no longer awaiting a schedule.']];
    }

    return ['success' => true, 'errors' => []];
}

/**
 * Office action: moves an already scheduled appointment to a new date and time. The same working-day,
 * working-hour and holiday rules as scheduling apply, and the citizen is given a reason. The old and new
 * times are kept in the appointment's notes. Scoped to $departmentId like scheduleAppointment().
 *
 * @return array{success: bool, errors: string[], previous?: array{date: string, time: ?string}}
 */
function rescheduleAppointment(int $appointmentId, int $departmentId, string $scheduledDate, string $scheduledTime, string $reason, int $byUserId): array
{
    $errors = [];

    $parsedDate = \DateTime::createFromFormat('Y-m-d', $scheduledDate);
    if ($parsedDate === false || $parsedDate->format('Y-m-d') !== $scheduledDate) {
        $errors[] = 'Enter a valid new date.';
    }
    $parsedTime = \DateTime::createFromFormat('H:i', $scheduledTime);
    if ($parsedTime === false || $parsedTime->format('H:i') !== $scheduledTime) {
        $errors[] = 'Enter a valid new time.';
    }
    if (empty($errors)) {
        $errors = validateBookingDateTime($scheduledDate, $scheduledTime);
    }
    $reason = trim($reason);
    if ($reason === '' || mb_strlen($reason) > 255) {
        $errors[] = 'Give the citizen a short reason for the change (up to 255 characters).';
    }
    if (!empty($errors)) {
        return ['success' => false, 'errors' => $errors];
    }

    $appointment = findAppointmentById($appointmentId);
    if ($appointment === null || (int) $appointment['department_id'] !== $departmentId) {
        return ['success' => false, 'errors' => ['That request does not belong to your office.']];
    }
    if ($appointment['status'] !== 'scheduled') {
        return ['success' => false, 'errors' => ['Only a scheduled appointment can be moved.']];
    }
    if ($appointment['scheduled_date'] === $scheduledDate && substr((string) $appointment['scheduled_time'], 0, 5) === $scheduledTime) {
        return ['success' => false, 'errors' => ['That is the same date and time as now. Choose a different one.']];
    }

    $note = 'Rescheduled by the office from ' . appointmentWhenText($appointment['scheduled_date'], $appointment['scheduled_time'])
        . ' to ' . appointmentWhenText($scheduledDate, $scheduledTime . ':00') . ': ' . $reason;
    $notes = trim((string) ($appointment['admin_notes'] ?? ''));
    $notes = $notes !== '' ? $notes . "\n\n" . $note : $note;

    $stmt = getDbConnection()->prepare(
        "UPDATE appointments
         SET scheduled_date = :scheduled_date, scheduled_time = :scheduled_time, admin_notes = :admin_notes,
             scheduled_by = :scheduled_by, scheduled_at = NOW()
         WHERE id = :id AND department_id = :department_id AND status = 'scheduled'"
    );
    $stmt->execute([
        'scheduled_date' => $scheduledDate,
        'scheduled_time' => $scheduledTime,
        'admin_notes' => $notes,
        'scheduled_by' => $byUserId,
        'id' => $appointmentId,
        'department_id' => $departmentId,
    ]);
    if ($stmt->rowCount() === 0) {
        return ['success' => false, 'errors' => ['That appointment is no longer scheduled.']];
    }

    return ['success' => true, 'errors' => [], 'previous' => ['date' => $appointment['scheduled_date'], 'time' => $appointment['scheduled_time']]];
}

/**
 * Office action: cancels a scheduled appointment, with a reason the citizen can read (kept in the notes).
 *
 * @return array{success: bool, errors: string[]}
 */
function cancelAppointmentByOffice(int $appointmentId, int $departmentId, string $reason): array
{
    $reason = trim($reason);
    if ($reason === '' || mb_strlen($reason) > 255) {
        return ['success' => false, 'errors' => ['Give the citizen a short reason for the cancellation (up to 255 characters).']];
    }

    $appointment = findAppointmentById($appointmentId);
    if ($appointment === null || (int) $appointment['department_id'] !== $departmentId) {
        return ['success' => false, 'errors' => ['That request does not belong to your office.']];
    }

    $note = 'Cancelled by the office: ' . $reason;
    $notes = trim((string) ($appointment['admin_notes'] ?? ''));
    $notes = $notes !== '' ? $notes . "\n\n" . $note : $note;

    $stmt = getDbConnection()->prepare(
        "UPDATE appointments SET status = 'cancelled', admin_notes = :admin_notes
         WHERE id = :id AND department_id = :department_id AND status = 'scheduled'"
    );
    $stmt->execute(['admin_notes' => $notes, 'id' => $appointmentId, 'department_id' => $departmentId]);
    if ($stmt->rowCount() === 0) {
        return ['success' => false, 'errors' => ['Only a scheduled appointment can be cancelled.']];
    }

    return ['success' => true, 'errors' => []];
}

/**
 * Office action: marks a scheduled appointment as completed. Scoped to
 * $departmentId the same way as scheduleAppointment().
 */
function markAppointmentCompleted(int $appointmentId, int $departmentId): bool
{
    $pdo = getDbConnection();
    $stmt = $pdo->prepare(
        "UPDATE appointments
         SET status = 'completed', completed_at = NOW()
         WHERE id = :id AND department_id = :department_id AND status = 'scheduled'"
    );
    $stmt->execute(['id' => $appointmentId, 'department_id' => $departmentId]);

    return $stmt->rowCount() > 0;
}
