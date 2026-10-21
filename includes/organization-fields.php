<?php
/**
 * The optional "Organization" and "Position" details a person can give: on the
 * sign-up form, and on every appointment request (prefilled from their account).
 * One shared check so both places accept exactly the same thing.
 */

const ORGANIZATION_MAX_LENGTH = 100;

/**
 * Cleans and checks the two optional fields from a form.
 *
 * @param array<string, mixed> $input Raw form fields: organization, position_title.
 * @return array{errors: string[], organization: ?string, position_title: ?string}
 */
function validateOrganizationFields(array $input): array
{
    $errors = [];
    $values = [];

    foreach (['organization' => 'organization', 'position_title' => 'position'] as $field => $label) {
        // Collapse whitespace and drop control characters; the text is always HTML-escaped when shown.
        $value = trim((string) preg_replace('/[\x00-\x1F\x7F]+/u', ' ', (string) ($input[$field] ?? '')));
        $value = trim((string) preg_replace('/\s+/u', ' ', $value));

        if (mb_strlen($value) > ORGANIZATION_MAX_LENGTH) {
            $errors[] = "Please enter a shorter $label (up to " . ORGANIZATION_MAX_LENGTH . ' characters).';
        }
        $values[$field] = $value === '' ? null : $value;
    }

    return ['errors' => $errors, 'organization' => $values['organization'], 'position_title' => $values['position_title']];
}
