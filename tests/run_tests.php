<?php
declare(strict_types=1);
/**
 * Simple self-checking tests (no libraries needed).
 * Run from the project folder:  php tests/run_tests.php
 */
define('TASKPAD_DATA_FILE', sys_get_temp_dir() . '/taskpad_test_' . bin2hex(random_bytes(4)) . '.json');
require __DIR__ . '/../src/helpers.php';
require __DIR__ . '/../src/storage.php';
require __DIR__ . '/../src/validation.php';

$passed = 0;
$failed = 0;
function check(string $name, bool $ok): void
{
    global $passed, $failed;
    if ($ok) { $passed++; echo "  PASS  $name\n"; } else { $failed++; echo "  FAIL  $name\n"; }
}

$v = new Validator();
echo "Validation\n";
[$ok, $err] = $v->validateCreate(['title' => '', 'priority' => 'Low']);
check('empty title is rejected', !$ok && isset($err['title']));
[$ok, $err] = $v->validateCreate(['title' => "   \t  "]);
check('whitespace-only title is rejected', !$ok && isset($err['title']));
[$ok, $err] = $v->validateCreate(['title' => str_repeat('a', 101)]);
check('title longer than 100 characters is rejected', !$ok && isset($err['title']));
[$ok, $err] = $v->validateCreate(['title' => 'x', 'description' => str_repeat('d', 501)]);
check('description longer than 500 characters is rejected', !$ok && isset($err['description']));
[$ok, $err] = $v->validateCreate(['title' => 'x', 'priority' => 'Urgent']);
check('priority outside the whitelist is rejected', !$ok && isset($err['priority']));
[$ok, $err] = $v->validateCreate(['title' => 'x', 'due' => '2026-02-30']);
check('impossible date 2026-02-30 is rejected', !$ok && isset($err['due']));
[$ok, $err] = $v->validateCreate(['title' => 'x', 'due' => '12/31/2026']);
check('wrong date format is rejected', !$ok && isset($err['due']));
[$ok, $err, $clean] = $v->validateCreate(['title' => '  Buy   milk ', 'description' => ' 2 litres ', 'priority' => 'High', 'due' => '2026-12-31']);
check('valid input is accepted and trimmed', $ok && $clean['title'] === 'Buy milk' && $clean['description'] === '2 litres' && $clean['due'] === '2026-12-31');
[$ok, , $clean] = $v->validateCreate(['title' => 'x']);
check('missing optional fields become empty/null with Medium priority', $ok && $clean['due'] === null && $clean['priority'] === 'Medium');
[$ok] = $v->validateCreate(['title' => ['array']]);
check('array input instead of text is rejected', !$ok);
check('2028-02-29 (leap day) is a valid date', Validator::isValidDate('2028-02-29'));
check('2026-02-29 is not a valid date', !Validator::isValidDate('2026-02-29'));
$f = $v->sanitizeFilters(['q' => '  Report  ', 'priority' => 'Banana']);
check('filters: q trimmed and unknown priority ignored', $f['q'] === 'Report' && $f['priority'] === '');
$f = $v->sanitizeFilters(['q' => ['x'], 'priority' => 'High']);
check('filters: array q ignored, valid priority kept', $f['q'] === '' && $f['priority'] === 'High');
check('e() escapes script tags', e('<script>alert("x")</script>') === '&lt;script&gt;alert(&quot;x&quot;)&lt;/script&gt;');

echo "Repository\n";
$repo = new TaskRepository();
check('empty store returns no tasks', $repo->all() === []);
$a = Task::create(['title' => 'Write report', 'description' => 'Final draft', 'priority' => 'High', 'due' => '2026-12-01']);
$b = Task::create(['title' => 'Buy milk', 'description' => '', 'priority' => 'Low', 'due' => null]);
$repo->add($a);
$repo->add($b);
check('tasks are added and persisted to the JSON file', count($repo->all()) === 2 && is_file(TASKPAD_DATA_FILE));
check('task ids look like UUIDs', Task::isValidId($a['id']) && !Task::isValidId('../etc/passwd'));
check('find() returns the right task', $repo->find($a['id'])['title'] === 'Write report');
$a['completed'] = true;
check('update() marks a task completed', $repo->update($a) && $repo->find($a['id'])['completed'] === true);
check('delete() removes a task', $repo->delete($b['id']) && $repo->find($b['id']) === null && count($repo->all()) === 1);
check('delete() of an unknown id returns false', $repo->delete(Task::newId()) === false);

echo "Filtering and counts\n";
$all = [
    ['id' => '1', 'title' => 'Write Report', 'description' => '', 'priority' => 'High', 'due' => null, 'completed' => false],
    ['id' => '2', 'title' => 'Groceries', 'description' => 'milk and REPORT card', 'priority' => 'Low', 'due' => null, 'completed' => true],
];
check('text filter is case-insensitive', count(TaskRepository::filter($all, 'report', '')) === 2);
check('text filter matches description', TaskRepository::filter($all, 'milk', '')[0]['id'] === '2');
check('priority filter works', count(TaskRepository::filter($all, '', 'High')) === 1);
check('text and priority filters combine', count(TaskRepository::filter($all, 'report', 'Low')) === 1);
check('no matches returns empty list', TaskRepository::filter($all, 'zzz', '') === []);
check('counts are correct', TaskRepository::counts($all) === ['total' => 2, 'open' => 1, 'completed' => 1]);

echo "Robustness\n";
file_put_contents(TASKPAD_DATA_FILE, '{ this is not json');
check('a corrupted JSON file is treated as empty, not a crash', $repo->all() === []);

@unlink(TASKPAD_DATA_FILE);
echo "\n$passed passed, $failed failed\n";
exit($failed === 0 ? 0 : 1);
