<?php
declare(strict_types=1);
require __DIR__ . '/../src/bootstrap.php';

$validator = new Validator();
$repo      = new TaskRepository();

$filters  = $validator->sanitizeFilters($_GET);               // safe values from ?q=...&priority=...
$allTasks = $repo->all();
$tasks    = TaskRepository::sortForDisplay(TaskRepository::filter($allTasks, $filters['q'], $filters['priority']));
$counts   = TaskRepository::counts($allTasks);
$isFiltered = $filters['q'] !== '' || $filters['priority'] !== '';
$today    = date('Y-m-d');

page_header('Tasks', 'list');
?>
<div class="page-title">
  <h1>Your tasks</h1>
  <a class="btn btn-primary" href="create.php">+ Add task</a>
</div>

<section aria-labelledby="summary-heading">
  <h2 id="summary-heading" class="visually-hidden">Summary</h2>
  <dl class="stats">
    <div class="stat"><dt>Total</dt><dd><?= e($counts['total']) ?></dd></div>
    <div class="stat"><dt>Open</dt><dd><?= e($counts['open']) ?></dd></div>
    <div class="stat"><dt>Completed</dt><dd><?= e($counts['completed']) ?></dd></div>
  </dl>
</section>

<form class="filters" method="get" action="index.php" role="search" aria-label="Filter tasks">
  <div class="field">
    <label for="q">Search title or description</label>
    <input type="search" id="q" name="q" value="<?= e($filters['q']) ?>" maxlength="<?= e(Validator::Q_MAX) ?>" placeholder="e.g. report">
  </div>
  <div class="field">
    <label for="priority">Priority</label>
    <select id="priority" name="priority">
      <option value="">All priorities</option>
      <?php foreach (Task::PRIORITIES as $p): ?>
        <option value="<?= e($p) ?>"<?= $filters['priority'] === $p ? ' selected' : '' ?>><?= e($p) ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="filter-actions">
    <button type="submit" class="btn btn-primary">Apply filters</button>
    <?php if ($isFiltered): ?><a class="btn btn-secondary" href="index.php">Clear</a><?php endif; ?>
  </div>
</form>

<?php if ($isFiltered): ?>
  <p class="results-note" role="status">Showing <strong><?= e(count($tasks)) ?></strong> of <strong><?= e($counts['total']) ?></strong> tasks.</p>
<?php endif; ?>

<?php if ($counts['total'] === 0): ?>
  <div class="empty-state">
    <h2>No tasks yet</h2>
    <p>Add your first task to get started.</p>
    <a class="btn btn-primary" href="create.php">+ Add task</a>
  </div>
<?php elseif (count($tasks) === 0): ?>
  <div class="empty-state">
    <h2>No matching tasks</h2>
    <p>Nothing matches your search. Try different words or clear the filters.</p>
    <a class="btn btn-secondary" href="index.php">Clear filters</a>
  </div>
<?php else: ?>
  <ul class="task-list">
  <?php foreach ($tasks as $t):
      $done    = !empty($t['completed']);
      $due     = $t['due'] ?? null;
      $overdue = !$done && $due !== null && $due < $today;
      $prio    = in_array($t['priority'] ?? '', Task::PRIORITIES, true) ? $t['priority'] : 'Medium'; ?>
    <li class="task<?= $done ? ' task-done' : '' ?>">
      <div class="task-body">
        <h3 class="task-title"><?= e($t['title'] ?? '') ?></h3>
        <p class="task-meta">
          <span class="badge badge-<?= e(strtolower($prio)) ?>">Priority: <?= e($prio) ?></span>
          <span class="badge <?= $done ? 'badge-completed' : 'badge-open' ?>"><?= $done ? 'Completed' : 'Open' ?></span>
          <?php if ($overdue): ?><span class="badge badge-overdue">Overdue</span><?php endif; ?>
          <?php if ($due): ?><span class="due">Due <?= e(format_due($due)) ?></span><?php endif; ?>
        </p>
        <?php if (($t['description'] ?? '') !== ''): ?>
          <p class="task-desc"><?= nl2br(e($t['description'])) ?></p>
        <?php endif; ?>
      </div>
      <div class="task-actions">
        <?php if (!$done): ?>
        <form method="post" action="actions.php">
          <?= csrf_field() ?>
          <input type="hidden" name="action" value="complete">
          <input type="hidden" name="id" value="<?= e($t['id'] ?? '') ?>">
          <input type="hidden" name="q" value="<?= e($filters['q']) ?>">
          <input type="hidden" name="priority" value="<?= e($filters['priority']) ?>">
          <button type="submit" class="btn btn-success">Complete<span class="visually-hidden">: <?= e($t['title'] ?? '') ?></span></button>
        </form>
        <?php endif; ?>
        <form method="post" action="actions.php" onsubmit="return confirm('Delete this task? This cannot be undone.');">
          <?= csrf_field() ?>
          <input type="hidden" name="action" value="delete">
          <input type="hidden" name="id" value="<?= e($t['id'] ?? '') ?>">
          <input type="hidden" name="q" value="<?= e($filters['q']) ?>">
          <input type="hidden" name="priority" value="<?= e($filters['priority']) ?>">
          <button type="submit" class="btn btn-danger">Delete<span class="visually-hidden">: <?= e($t['title'] ?? '') ?></span></button>
        </form>
      </div>
    </li>
  <?php endforeach; ?>
  </ul>
<?php endif; ?>
<?php page_footer();
