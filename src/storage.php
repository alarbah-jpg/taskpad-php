<?php
declare(strict_types=1);

/** Where tasks are stored. Tests can define TASKPAD_DATA_FILE first to use a temp file. */
if (!defined('TASKPAD_DATA_FILE')) {
    define('TASKPAD_DATA_FILE', __DIR__ . '/../data/tasks.json');
}

/**
 * A task is stored as a plain array:
 * id, title, description, priority (Low|Medium|High), due (YYYY-MM-DD|null), completed (bool), created (ISO date)
 */
final class Task
{
    public const PRIORITIES = ['Low', 'Medium', 'High'];

    /** Build a new task record from already-validated, sanitized input. */
    public static function create(array $clean): array
    {
        return [
            'id'          => self::newId(),
            'title'       => $clean['title'],
            'description' => $clean['description'],
            'priority'    => $clean['priority'],
            'due'         => $clean['due'],
            'completed'   => false,
            'created'     => date('c'),
        ];
    }

    /** Random UUID v4 string. */
    public static function newId(): string
    {
        $b = random_bytes(16);
        $b[6] = chr((ord($b[6]) & 0x0f) | 0x40);
        $b[8] = chr((ord($b[8]) & 0x3f) | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($b), 4));
    }

    public static function isValidId($id): bool
    {
        return is_string($id) && preg_match('/^[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}$/', $id) === 1;
    }
}

/** Reads and writes tasks in a JSON file, using file locks so two requests cannot corrupt it. */
final class TaskRepository
{
    private string $path;

    public function __construct(?string $path = null)
    {
        $this->path = $path ?? TASKPAD_DATA_FILE;
    }

    /** @return array[] all tasks */
    public function all(): array
    {
        if (!is_file($this->path)) {
            return [];
        }
        $fh = @fopen($this->path, 'rb');
        if ($fh === false) {
            return [];
        }
        flock($fh, LOCK_SH);
        $raw = stream_get_contents($fh);
        flock($fh, LOCK_UN);
        fclose($fh);
        $data = json_decode((string) $raw, true);
        return is_array($data) ? array_values($data) : [];
    }

    public function find(string $id): ?array
    {
        foreach ($this->all() as $task) {
            if (($task['id'] ?? null) === $id) {
                return $task;
            }
        }
        return null;
    }

    public function add(array $task): void
    {
        $this->mutate(function (array $tasks) use ($task): array {
            $tasks[] = $task;
            return $tasks;
        });
    }

    /** Replace the task with the same id. Returns false if it was not found. */
    public function update(array $task): bool
    {
        $found = false;
        $this->mutate(function (array $tasks) use ($task, &$found): array {
            foreach ($tasks as $i => $t) {
                if (($t['id'] ?? null) === $task['id']) {
                    $tasks[$i] = $task;
                    $found = true;
                }
            }
            return $tasks;
        });
        return $found;
    }

    /** Remove the task with this id. Returns false if it was not found. */
    public function delete(string $id): bool
    {
        $found = false;
        $this->mutate(function (array $tasks) use ($id, &$found): array {
            $kept = [];
            foreach ($tasks as $t) {
                if (($t['id'] ?? null) === $id) {
                    $found = true;
                } else {
                    $kept[] = $t;
                }
            }
            return $kept;
        });
        return $found;
    }

    /** Case-insensitive text match on title/description, plus an optional priority filter. */
    public static function filter(array $tasks, string $q, string $priority): array
    {
        return array_values(array_filter($tasks, function (array $t) use ($q, $priority): bool {
            if ($priority !== '' && ($t['priority'] ?? '') !== $priority) {
                return false;
            }
            if ($q !== '') {
                $hay = ($t['title'] ?? '') . "\n" . ($t['description'] ?? '');
                return text_contains_ci($hay, $q);
            }
            return true;
        }));
    }

    /** @return array{total:int, open:int, completed:int} */
    public static function counts(array $tasks): array
    {
        $done = 0;
        foreach ($tasks as $t) {
            if (!empty($t['completed'])) {
                $done++;
            }
        }
        return ['total' => count($tasks), 'open' => count($tasks) - $done, 'completed' => $done];
    }

    /** Open tasks first, then by due date (no date last), then newest first. */
    public static function sortForDisplay(array $tasks): array
    {
        usort($tasks, function (array $a, array $b): int {
            $c = (int) !empty($a['completed']) <=> (int) !empty($b['completed']);
            if ($c !== 0) {
                return $c;
            }
            $da = $a['due'] ?? null;
            $db = $b['due'] ?? null;
            if ($da !== $db) {
                if ($da === null) {
                    return 1;
                }
                if ($db === null) {
                    return -1;
                }
                return strcmp($da, $db);
            }
            return strcmp((string) ($b['created'] ?? ''), (string) ($a['created'] ?? ''));
        });
        return $tasks;
    }

    /** Read-modify-write under an exclusive lock. */
    private function mutate(callable $change): void
    {
        $dir = dirname($this->path);
        if (!is_dir($dir) && !@mkdir($dir, 0775, true) && !is_dir($dir)) {
            throw new RuntimeException('Cannot create data directory.');
        }
        $fh = @fopen($this->path, 'c+');
        if ($fh === false) {
            throw new RuntimeException('Cannot open the tasks file for writing.');
        }
        try {
            if (!flock($fh, LOCK_EX)) {
                throw new RuntimeException('Cannot lock the tasks file.');
            }
            $data = json_decode((string) stream_get_contents($fh), true);
            $tasks = $change(is_array($data) ? array_values($data) : []);
            $json = json_encode(array_values($tasks), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            if ($json === false) {
                throw new RuntimeException('Cannot encode tasks as JSON.');
            }
            ftruncate($fh, 0);
            rewind($fh);
            fwrite($fh, $json);
            fflush($fh);
            flock($fh, LOCK_UN);
        } finally {
            fclose($fh);
        }
    }
}
