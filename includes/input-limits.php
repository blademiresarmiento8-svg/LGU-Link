<?php
/**
 * Length limits for text people type, and one way to report them. Every limit is checked on the server
 * (the browser's maxlength is only a convenience) and is at or below what the database column can hold, so a
 * long entry is refused with a message instead of crashing the page or being cut off without anyone knowing.
 */

/** A note or reason one person writes for another (the TEXT columns hold far more, but nobody needs a page). */
const NOTE_MAX_LENGTH = 1000;

/** A description, agenda or similar body of text. */
const LONG_TEXT_MAX_LENGTH = 5000;

/** Big list boxes the Administrator fills in (the Citizen's Charter requirements, steps and keywords). */
const LIST_TEXT_MAX_LENGTH = 10000;

/**
 * An error message when $value is longer than $max characters, otherwise null.
 * $label starts the sentence: "The title", "The note".
 */
function tooLongMessage(string $label, string $value, int $max): ?string
{
    return mb_strlen($value) > $max ? $label . ' must be ' . number_format($max) . ' characters or fewer.' : null;
}

/** Shortens a file name for display, keeping its extension: "a-very-long-name....pdf". */
function shortenFileName(string $name, int $max = 255): string
{
    if (mb_strlen($name) <= $max) {
        return $name;
    }
    $extension = pathinfo($name, PATHINFO_EXTENSION);
    $suffix = $extension !== '' ? '.' . $extension : '';
    $room = max(1, $max - mb_strlen($suffix) - 1);

    return mb_substr($name, 0, $room) . '…' . $suffix;
}
