<?php

use App\Cells\InvalidFeedback;
use App\DatabaseObjects\Column;
use App\DatabaseObjects\Task;
use App\DatabaseObjects\User;

/* @var $users User[] */
/* @var $columns Column[] */
/* @var $activeTask Task */
/* @var $submitURL string */
/* @var $abortURL string */
/* @var $isDelete bool */
/* @var $errorMessages array<string, string> */
/* @var $oldPost array<string, string> */

$showDelete = isset($isDelete) && $isDelete;
$showCreate = !$showDelete && !isset($activeTask);
$showEdit = !$showCreate && !$showDelete && isset($activeTask);
$remindDateTime = !$showCreate ? $activeTask->remindDate : new DateTime();

if (!isset($errorMessages))
    $errorMessages = [];
$hasErrors = !empty($errorMessages);
?>

<div class="container pb-4">
    <div class="card mt-4">
        <div class="card-header">
            <div class="d-flex justify-content-between">
                <?php if ($showDelete): ?>
                    <div class="h3">Task löschen</div>
                <?php elseif ($showCreate): ?>
                    <div class="h3">Task erstellen</div>
                <?php else: ?>
                    <div class="h3">Task bearbeiten</div>
                <?php endif; ?>
            </div>
        </div>
        <div class="card-body pt-0 pb-0">
            <div class="row">
                <div class="col-12">
                    <div class="card-content">
                        <div class="card-body">
                            <form id="form" action="<?= esc($submitURL) ?>" method="post">
                                <fieldset <?php if ($showDelete) echo 'disabled'; ?>>
                                    <!-- Task -->
                                    <div class="form-group has-validation mb-4 mt-0">
                                        <label for="task" class="form-label mb-0">Task:</label>
                                        <input type="text" class="form-control rounded <?php if ($hasErrors && isset($errorMessages['task'])) echo 'is-invalid'; ?>"
                                               id="task" name="task" placeholder="Task eingeben..."
                                               value="<?php if (isset($oldPost['task'])) echo $oldPost['task']; elseif (!$showCreate) echo esc($activeTask->task); ?>"/>
                                        <?= InvalidFeedback::render($errorMessages, 'task') ?>
                                    </div>
                                    <!-- Board -->
                                    <div class="form-group has-validation mb-4">
                                        <label for="columnid" class="form-label mb-0">Board & Spalte:</label>
                                        <select name="columnid" id="columnid" class="form-select <?php if ($hasErrors && isset($errorMessages['columnid'])) echo 'is-invalid'; ?>">
                                            <?php foreach ($columns as $c): ?>
                                                <option value="<?= esc($c->id) ?>" <?php if ((isset($oldPost['columnid']) && $oldPost['columnid'] == $c->id) || !$showCreate && $activeTask->columnId === $c->id) echo 'selected'; ?>><?= esc($c->name) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <?= InvalidFeedback::render($errorMessages, 'columnid') ?>
                                    </div>
                                    <!-- Kontakt -->
                                    <div class="form-group has-validation mb-4">
                                        <label for="personid" class="form-label mb-0">Kontakt:</label>
                                        <select name="personid" id="personid" class="form-select <?php if ($hasErrors && isset($errorMessages['personid'])) echo 'is-invalid'; ?>">
                                            <?php foreach ($users as $p): ?>
                                                <option value="<?= esc($p->id) ?>" <?php if ((isset($oldPost['personid']) && $oldPost['personid'] == $p->id) || !$showCreate && $activeTask->userId === $p->id) echo 'selected'; ?>><?= esc($p->lastName) ?>, <?= esc($p->firstName) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <?= InvalidFeedback::render($errorMessages, 'personid') ?>
                                    </div>
                                    <!-- Erinnerung -->
                                    <div class="form-group has-validation">
                                        <label for="reminderdate" class="form-label mb-0">Erinnerung:</label>
                                        <div class="row">
                                            <div class="col-sm-6 mb-2">
                                                <div class="input-group">
                                                    <div class="input-group-text">
                                                        <span title="Erinnerungsdatum" data-bs-toggle="tooltip"><i class="far fa-calendar-alt"></i></span>
                                                    </div>
                                                    <input type="text" data-provide="datepicker" class="form-control rounded-end <?php if ($hasErrors && isset($errorMessages['reminderdate'])) echo 'is-invalid'; ?>"
                                                           id="reminderdate" name="reminderdate"
                                                           value="<?= $remindDateTime->format('d.m.Y') ?>">
                                                    <?= InvalidFeedback::render($errorMessages, 'reminderdate') ?>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 mb-4">
                                                <div class="input-group">
                                                    <div class="input-group-text">
                                                        <span title="Erinnerungsuhrzeit" data-bs-toggle="tooltip"><i class="far fa-clock"></i></span>
                                                    </div>
                                                    <input type="text" class="form-control <?php if ($hasErrors && isset($errorMessages['remindertime'])) echo 'is-invalid'; ?>"
                                                           id="remindertime" name="remindertime"
                                                           value="<?= $remindDateTime->format('H:i') ?>">
                                                    <?= InvalidFeedback::render($errorMessages, 'remindertime') ?>
                                                    <div class="input-group-text">
                                                    <div class="form-check form-switch">
                                                        <input title="Erinnerung" data-bs-toggle="tooltip" id="reminderuse" name="reminderuse" class="form-check-input" type="checkbox" value="1" <?php if (!$showCreate && $activeTask->useReminder) echo 'checked'; ?>>
                                                    </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Notizen -->
                                    <div class="form-group has-validation mb-4">
                                        <label for="notes" class="form-label mb-0">Notizen:</label>
                                        <textarea class="form-control <?php if ($hasErrors && isset($errorMessages['notes'])) echo 'is-invalid'; ?>"
                                                  rows="3" id="notes" name="notes" placeholder="Notizen..."><?php if (isset($oldPost['notes'])) echo $oldPost['notes']; elseif (!$showCreate) echo esc($activeTask->notes) ?></textarea>
                                    </div>
                                </fieldset>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <?php if ($showDelete): ?>
                                            <button class="btn btn-danger bg-danger-subtle2 mb-2 me-2" type="submit">
                                                <i class="fas fa-trash"></i>
                                                <span class="d-none d-sm-inline-flex">Löschen</span>
                                            </button>
                                        <?php else: ?>
                                        <button class="btn btn-success bg-success-subtle2 mb-2 me-2" type="submit">
                                            <i class="fas fa-save"></i>
                                            <span class="d-none d-sm-inline-flex">Speichern</span>
                                        </button>
                                        <?php endif; ?>
                                        <a href="<?= esc($abortURL) ?>">
                                            <button class="btn btn-warning mb-2" type="button">
                                                <i class="fas fa-window-close"></i>
                                                <span class="d-none d-sm-inline-flex">Abbrechen</span>
                                            </button>
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $('#reminderdate').datetimepicker({
        timepicker: false,
        yearStart: 2010,
        format: 'd.m.Y',
        scrollInput : false
    });

    $('#remindertime').datetimepicker({
        datepicker: false,
        timepicker: true,
        Timeformat: 'G:i',
        step: 15,
        format: 'G:i'
    });
</script>
