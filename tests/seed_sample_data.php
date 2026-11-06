<?php
declare(strict_types=1);
/**
 * Adds sample tasks so you can take screenshots quickly.
 * Run from the project folder:  php tests/seed_sample_data.php
 * To start fresh again, delete data/tasks.json
 */
require __DIR__ . '/../src/storage.php';

$repo = new TaskRepository();
if (count($repo->all()) > 0) {
    echo "data/tasks.json already has tasks. Delete it first if you want a fresh start.\n";
    exit(0);
}
$samples = [
    ['Finish PHP assignment', 'Build the TaskPad app, UML diagrams and README.', 'High', date('Y-m-d', strtotime('+3 days'))],
    ['Email the supervisor', 'Share the repository link.', 'Medium', date('Y-m-d', strtotime('+1 day'))],
    ['Review validation rules', '', 'Low', null],
    ['Submit late fee form', 'This one is overdue.', 'High', date('Y-m-d', strtotime('-2 days'))],
    ['Buy printer paper', 'A4, 500 sheets.', 'Low', null],
];
foreach ($samples as [$title, $desc, $prio, $due]) {
    $repo->add(Task::create(['title' => $title, 'description' => $desc, 'priority' => $prio, 'due' => $due]));
}
echo "Added " . count($samples) . " sample tasks.\n";
