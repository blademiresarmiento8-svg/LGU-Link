<?php
/**
 * The rule for passwords people choose for themselves (citizen sign-up and the
 * change-password screen): at least 8 characters, with an uppercase letter, a
 * lowercase letter, and a number. The same wording is used in the forms.
 *
 * Passwords an admin sets for someone else (a new office account, or the temporary
 * password after a reset) only need 8 characters: those are handed over to the
 * person, and the temporary ones must be changed at next login anyway.
 */

/** Shown next to the password fields. */
const PASSWORD_RULE_TEXT = 'At least 8 characters, with an uppercase letter, a lowercase letter, and a number.';

/** The same rule as an HTML pattern, so the browser can warn before submitting. */
const PASSWORD_RULE_PATTERN = '(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,72}';

/** A message when the password does not meet the rule, or null when it does. */
function passwordProblem(string $password): ?string
{
    if (strlen($password) > 72) {
        return 'Password must be 72 characters or fewer.';
    }
    if (strlen($password) < 8 || !preg_match('/[a-z]/', $password) || !preg_match('/[A-Z]/', $password) || !preg_match('/\d/', $password)) {
        return 'Password must be at least 8 characters and include an uppercase letter, a lowercase letter, and a number.';
    }

    return null;
}
