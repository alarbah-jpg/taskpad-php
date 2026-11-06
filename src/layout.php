<?php
declare(strict_types=1);

/** Print the top of every page: <head>, header, navigation and any flash messages. */
function page_header(string $title, string $active = ''): void
{
    header('Content-Type: text/html; charset=UTF-8');
    $nav = function (string $key, string $href, string $label) use ($active): string {
        $current = $key === $active ? ' aria-current="page"' : '';
        return '<a href="' . e($href) . '"' . $current . '>' . e($label) . '</a>';
    };
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($title) ?> | TaskPad PHP</title>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<a class="skip-link" href="#main">Skip to main content</a>
<header class="site-header">
  <div class="container header-inner">
    <a class="brand" href="index.php">TaskPad <span>PHP</span></a>
    <nav aria-label="Main">
      <?= $nav('list', 'index.php', 'Tasks') ?>
      <?= $nav('create', 'create.php', 'Add task') ?>
    </nav>
  </div>
</header>
<main id="main" class="container">
<?php foreach (flash_get() as $f):
        $type = in_array($f['type'], ['success', 'error', 'info'], true) ? $f['type'] : 'info';
        $role = $type === 'error' ? 'alert' : 'status'; ?>
  <div class="flash flash-<?= e($type) ?>" role="<?= e($role) ?>"><?= e($f['message']) ?></div>
<?php endforeach;
}

/** Print the bottom of every page. */
function page_footer(): void
{
    ?>
</main>
<footer class="site-footer">
  <div class="container">
    <p>TaskPad PHP &middot; tasks are stored in <code>data/tasks.json</code> &middot;
      <a href="export.php">View tasks as JSON</a></p>
  </div>
</footer>
</body>
</html>
<?php
}
