<?php
declare(strict_types=1);
require __DIR__ . '/../src/bootstrap.php';

// Read-only JSON view of the (optionally filtered) tasks. Demonstrates a non-HTML Content-Type.
if (!in_array($_SERVER['REQUEST_METHOD'] ?? 'GET', ['GET', 'HEAD'], true)) {
    http_response_code(405);
    header('Allow: GET, HEAD');
    exit;
}

$filters = (new Validator())->sanitizeFilters($_GET);
$tasks   = TaskRepository::sortForDisplay(
    TaskRepository::filter((new TaskRepository())->all(), $filters['q'], $filters['priority'])
);

header('Content-Type: application/json; charset=UTF-8');
echo json_encode(
    ['count' => count($tasks), 'tasks' => $tasks],
    JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP
), "\n";
