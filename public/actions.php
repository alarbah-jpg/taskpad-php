<?php
declare(strict_types=1);
require __DIR__ . '/../src/bootstrap.php';

// Changing data must use POST, never GET.
if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    header('Content-Type: text/plain; charset=UTF-8');
    echo "405 Method Not Allowed. Use the Complete or Delete buttons on the task list (POST only).\n";
    exit;
}

$validator = new Validator();
$filters   = $validator->sanitizeFilters($_POST);
$back      = list_url($filters);              // rebuilt from whitelisted values, never from raw input

if (!csrf_verify($_POST['csrf_token'] ?? null)) {
    flash_set('error', 'Your session expired. Please try again.');
    redirect($back);
}

$action = is_string($_POST['action'] ?? null) ? $_POST['action'] : '';
$id     = $_POST['id'] ?? '';

if (!in_array($action, ['complete', 'delete'], true) || !Task::isValidId($id)) {
    flash_set('error', 'That request was not valid.');
    redirect($back);
}

try {
    $repo = new TaskRepository();
    $task = $repo->find($id);
    if ($task === null) {
        flash_set('error', 'That task no longer exists.');
    } elseif ($action === 'complete') {
        if (!empty($task['completed'])) {
            flash_set('info', 'Task "' . $task['title'] . '" was already completed.');
        } else {
            $task['completed'] = true;
            $repo->update($task);
            flash_set('success', 'Task "' . $task['title'] . '" was marked as completed.');
        }
    } else {
        $repo->delete($id);
        flash_set('success', 'Task "' . $task['title'] . '" was deleted.');
    }
} catch (RuntimeException $ex) {
    flash_set('error', 'The change could not be saved. Check that the data folder is writable.');
}

redirect($back);
