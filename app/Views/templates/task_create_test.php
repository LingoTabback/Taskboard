<?php

use App\DatabaseObjects\Column;
use App\DatabaseObjects\Task;
use App\DatabaseObjects\User;

/* @var $users User[] */
/* @var $columns Column[] */
/* @var $activeTask Task */
/* @var $submitURL string */
/* @var $abortURL string */
/* @var $isDelete bool */

$showDelete = isset($isDelete) && $isDelete;
$showCreate = !$showDelete && !isset($activeTask);
$showEdit = !$showCreate && !$showDelete && isset($activeTask);
$remindDateTime = !$showCreate ? $activeTask->remindDate : new DateTime();
?>

<div class="container pb-4">
    <div class="card mt-4">
        <div class="card-header">
            <div class="d-flex justify-content-between">
                <?php if ($showDelete): ?>
                    <div class="h3">Task löschen</div>
                <?php elseif ($showCreate): ?>
                    <div class="h3">Task neu erstellen</div>
                <?php else: ?>
                    <div class="h3">Task bearbeiten</div>
                <?php endif; ?>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    <div class="card-content">
                        <div class="card-body">
                            <form id="form" action="<?= esc($submitURL) ?>" method="post">
                                <fieldset <?php if ($showDelete) echo 'disabled'; ?>>
                                    <!-- Task -->
                                    <div class="row mb-3">
                                        <label for="task" class="col-sm-2 form-label">Task:</label>
                                        <div class="col-sm-10">
                                            <div class="input-group">
                                                <input type="text" class="form-control rounded me-3 "
                                                       id="task" name="task" placeholder="Task eingeben..."
                                                       value="<?php if (!$showCreate) echo esc($activeTask->task); ?>"/>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Board -->
                                    <div class="row mb-2">
                                        <label for="columnid" class="col-sm-2 form-label">Board & Spalte:</label>
                                        <div class="col-sm-10">
                                            <div class="form-group row mb-2">
                                                <div class="input-group">
                                                    <select name="columnid" id="columnid" class="form-select">
                                                        <?php foreach ($columns as $c): ?>
                                                            <option value="<?= esc($c->id) ?>" <?php if (!$showCreate && $activeTask->columnId === $c->id) echo 'selected'; ?>><?= esc($c->name) ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Kontakt -->
                                    <div class="row mb-3">
                                        <label for="personid" class="col-sm-2 form-label">Kontakt:</label>
                                        <div class="col-sm-10">
                                            <div class="form-group row mb-2">
                                                <div class="input-group">
                                                    <select name="personid" id="personid" class="form-select">
                                                        <?php foreach ($users as $p): ?>
                                                            <option value="<?= esc($p->id) ?>" <?php if (!$showCreate && $activeTask->userId === $p->id) echo 'selected'; ?>><?= esc($p->lastName) ?>, <?= esc($p->firstName) ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Erinnerung -->
                                    <div class="row mb-2">
                                        <label for="reminderdate" class="col-sm-2 form-label">Erinnerung:</label>
                                        <div class="col-sm-5">
                                            <div class="input-group mb-2">
                                                <div class="input-group-text">
                                                    <span title="Erinnerungsdatum" data-bs-toggle="tooltip"><i class="far fa-calendar-alt"></i></span>
                                                </div>
                                                <input type="text" data-provide="datepicker" class="form-control rounded-end"
                                                       id="reminderdate" name="reminderdate"
                                                       value="<?php echo $remindDateTime->format('d.m.Y'); ?>">
                                                <div id="feedbackreminderdate" class="invalid-feedback"></div>
                                            </div>
                                        </div>
                                        <div class="col-sm-5">
                                            <div class="input-group">
                                                <div class="input-group-text">
                                                    <span title="Erinnerungsuhrzeit" data-bs-toggle="tooltip"><i class="far fa-clock"></i></span>
                                                </div>
                                                <input type="text" class="form-control"
                                                       id="remindertime" name="remindertime"
                                                       value="<?php echo $remindDateTime->format('H:i'); ?>">
                                                <div class="input-group-text rounded-end">
                                                    <input title="Erinnerung" data-bs-toggle="tooltip" id="reminderuse" name="reminderuse" class="form-check-input" type="checkbox" value="1" <?php if (!$showCreate && $activeTask->useReminder) echo 'checked'; ?>>
                                                </div>
                                                <div id="feedbackremindertime" class="invalid-feedback"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Notizen -->
                                    <div class="form-group mb-2 row">
                                        <label for="notes" class="col-sm-2 col-form-label">Notizen:</label>
                                        <div class="col-sm-10">
                                            <textarea class="form-control" rows="3" id="notes" name="notes" placeholder="Notizen..."><?php if (!$showCreate) echo esc($activeTask->notes) ?></textarea>
                                        </div>
                                    </div>
                                </fieldset>
                                <div class="row mt-4">
                                    <div class="col-sm-12">
                                        <?php if ($showDelete): ?>
                                            <button class="btn btn-danger mb-2 me-2" type="submit">
                                                <i class="fas fa-trash"></i>
                                                <span class="d-none d-sm-inline-flex">Löschen</span>
                                            </button>
                                        <?php else: ?>
                                        <button class="btn btn-success mb-2 me-2" type="submit">
                                            <i class="fas fa-save"></i>
                                            <span class="d-none d-sm-inline-flex">Speichern</span>
                                        </button>
                                        <?php endif; ?>
                                        <a href="<?= esc($abortURL) ?>">
                                            <button class="btn btn-primary mb-2" type="button">
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
