<?php
declare(strict_types=1);

/** Escape a value for safe output inside HTML (prevents XSS). */
function e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Redirect and stop. 303 "See Other" is the correct status after a POST (PRG pattern). */
function redirect(string $url, int $status = 303): void
{
    header('Location: ' . $url, true, $status);
    exit;
}

/** Build the list URL from already-sanitized filters, e.g. index.php?q=milk&priority=High */
function list_url(array $filters = []): string
{
    $params = [];
    if (!empty($filters['q'])) {
        $params['q'] = $filters['q'];
    }
    if (!empty($filters['priority'])) {
        $params['priority'] = $filters['priority'];
    }
    return 'index.php' . ($params ? '?' . http_build_query($params) : '');
}

/** Turn "2026-11-05" into "Nov 5, 2026" (or return an empty string). */
function format_due(?string $due): string
{
    if ($due === null || $due === '') {
        return '';
    }
    $d = DateTime::createFromFormat('Y-m-d', $due);
    return $d ? $d->format('M j, Y') : '';
}
