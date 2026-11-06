<?php
declare(strict_types=1);
require __DIR__ . '/../src/bootstrap.php';

$validator = new Validator();
$repo      = new TaskRepository();
$errors    = [];
$values    = ['title' => '', 'description' => '', 'priority' => 'Medium', 'due' => ''];

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    // 1. validate + sanitize on the server
    [$isValid, $errors, $clean] = $validator->validateCreate($_POST);
    $values = [
        'title'       => $clean['title'],
        'description' => $clean['description'],
        'priority'    => $clean['priority'],
        'due'         => $clean['due'] ?? '',
    ];

    // 2. check the CSRF token
    if (!csrf_verify($_POST['csrf_token'] ?? null)) {
        $isValid = false;
        $errors['form'] = 'Your session expired. Please submit the form again.';
    }

    // 3. save and redirect (Post/Redirect/Get) so refreshing never submits twice
    if ($isValid) {
        try {
            $repo->add(Task::create($clean));
            flash_set('success', 'Task "' . $clean['title'] . '" was added.');
            redirect('index.php');
        } catch (RuntimeException $ex) {
            $errors['form'] = 'The task could not be saved. Check that the data folder is writable.';
        }
    }

    http_response_code(422); // validation failed: show the form again with the user's entries
}

page_header('Add task', 'create');
$err = function (string $field) use ($errors): string {
    return isset($errors[$field]) ? '<p class="error-text" id="' . e($field) . '-error">' . e($errors[$field]) . '</p>' : '';
};
$invalid = function (string $field) use ($errors): string {
    return isset($errors[$field]) ? ' aria-invalid="true" aria-describedby="' . e($field) . '-error"' : '';
};
?>
<div class="page-title"><h1>Add a task</h1></div>

<?php if ($errors): ?>
  <div class="flash flash-error" role="alert">
    <strong>Please fix the following:</strong>
    <ul>
      <?php foreach ($errors as $msg): ?><li><?= e($msg) ?></li><?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>

<form class="card form" method="post" action="create.php" novalidate>
  <?= csrf_field() ?>

  <div class="field<?= isset($errors['title']) ? ' has-error' : '' ?>">
    <label for="title">Title <span class="required">(required)</span></label>
    <input type="text" id="title" name="title" value="<?= e($values['title']) ?>" maxlength="150" autocomplete="off"<?= $invalid('title') ?>>
    <?= $err('title') ?>
  </div>

  <div class="field<?= isset($errors['description']) ? ' has-error' : '' ?>">
    <label for="description">Description <span class="optional">(optional)</span></label>
    <textarea id="description" name="description" rows="4"<?= $invalid('description') ?>><?= e($values['description']) ?></textarea>
    <?= $err('description') ?>
  </div>

  <div class="field-row">
    <div class="field<?= isset($errors['priority']) ? ' has-error' : '' ?>">
      <label for="priority">Priority</label>
      <select id="priority" name="priority"<?= $invalid('priority') ?>>
        <?php foreach (Task::PRIORITIES as $p): ?>
          <option value="<?= e($p) ?>"<?= $values['priority'] === $p ? ' selected' : '' ?>><?= e($p) ?></option>
        <?php endforeach; ?>
      </select>
      <?= $err('priority') ?>
    </div>

    <div class="field<?= isset($errors['due']) ? ' has-error' : '' ?>">
      <label for="due">Due date <span class="optional">(optional)</span></label>
      <input type="date" id="due" name="due" value="<?= e($values['due']) ?>"<?= $invalid('due') ?>>
      <?= $err('due') ?>
    </div>
  </div>

  <div class="form-actions">
    <button type="submit" class="btn btn-primary">Save task</button>
    <a class="btn btn-secondary" href="index.php">Cancel</a>
  </div>
</form>
<?php page_footer();
