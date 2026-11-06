<?php
declare(strict_types=1);

/* Small text helpers that use mbstring when it is installed and fall back otherwise. */
function text_length(string $s): int
{
    return function_exists('mb_strlen') ? mb_strlen($s, 'UTF-8') : strlen($s);
}

function text_contains_ci(string $haystack, string $needle): bool
{
    if ($needle === '') {
        return true;
    }
    return function_exists('mb_stripos')
        ? mb_stripos($haystack, $needle, 0, 'UTF-8') !== false
        : stripos($haystack, $needle) !== false;
}

/** Validates and sanitizes everything that comes from $_POST / $_GET. */
final class Validator
{
    public const TITLE_MAX = 100;
    public const DESC_MAX  = 500;
    public const Q_MAX     = 100;

    /**
     * @param array $input usually $_POST
     * @return array [bool isValid, array errors (field => message), array sanitized]
     */
    public function validateCreate(array $input): array
    {
        $title       = preg_replace('/\s+/u', ' ', self::clean($input['title'] ?? '')) ?? '';
        $title       = trim($title);
        $description = self::clean($input['description'] ?? '');
        $priority    = self::clean($input['priority'] ?? 'Medium');
        $due         = self::clean($input['due'] ?? '');
        $errors      = [];

        if ($title === '') {
            $errors['title'] = 'Title is required.';
        } elseif (text_length($title) > self::TITLE_MAX) {
            $errors['title'] = 'Title must be ' . self::TITLE_MAX . ' characters or fewer.';
        }

        if (text_length($description) > self::DESC_MAX) {
            $errors['description'] = 'Description must be ' . self::DESC_MAX . ' characters or fewer.';
        }

        if (!in_array($priority, Task::PRIORITIES, true)) {
            $errors['priority'] = 'Choose Low, Medium or High.';
            $priority = 'Medium';
        }

        if ($due !== '' && !self::isValidDate($due)) {
            $errors['due'] = 'Enter a real date in YYYY-MM-DD format, for example 2026-12-31.';
        }

        $sanitized = [
            'title'       => $title,
            'description' => $description,
            'priority'    => $priority,
            'due'         => $due === '' ? null : $due,
        ];
        return [$errors === [], $errors, $sanitized];
    }

    /**
     * Safely read the ?q=...&priority=... query string.
     * @return array{q:string, priority:string}
     */
    public function sanitizeFilters(array $query): array
    {
        $q = preg_replace('/\s+/u', ' ', self::clean($query['q'] ?? '')) ?? '';
        $q = trim($q);
        if (text_length($q) > self::Q_MAX) {
            $q = function_exists('mb_substr') ? mb_substr($q, 0, self::Q_MAX, 'UTF-8') : substr($q, 0, self::Q_MAX);
        }
        $priority = self::clean($query['priority'] ?? '');
        if (!in_array($priority, Task::PRIORITIES, true)) {
            $priority = '';
        }
        return ['q' => $q, 'priority' => $priority];
    }

    /** YYYY-MM-DD shape (regex) and a real calendar date (checkdate). */
    public static function isValidDate(string $value): bool
    {
        if (!preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $value, $m)) {
            return false;
        }
        return checkdate((int) $m[2], (int) $m[3], (int) $m[1]);
    }

    /** Non-strings (e.g. ?q[]=x) become '', invalid UTF-8 and control characters are removed, text is trimmed. */
    private static function clean($value): string
    {
        if (!is_string($value) || preg_match('//u', $value) !== 1) {
            return '';
        }
        $value = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $value) ?? '';
        $value = str_replace("\r\n", "\n", $value);
        return trim($value);
    }
}
