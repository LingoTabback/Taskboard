<?php

use App\DatabaseObjects\Board;
use App\DatabaseObjects\DisplayTask;

/* @var $tasks DisplayTask[] */
/* @var $boards Board[] */
/* @var $activeBoard Board */
/* @var $boardsURL string */
/* @var $taskCreateURL string */
/* @var $taskEditURL string */
/* @var $taskDeleteURL string */
?>

<div class="d-flex p-2">
    <button class="btn btn-primary dropdown-toggle me-2" type="button" id="dropdownBoards" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <?= esc($activeBoard->name) ?>
    </button>
    <div class="dropdown-menu" aria-labelledby="dropdownBoards">
        <?php foreach ($boards as $board): ?>
            <a class="dropdown-item" href="<?= esc($boardsURL) ?>/<?= esc($board->id) ?>"><?= esc($board->name) ?></a>
        <?php endforeach; ?>
    </div>
    <a href="<?= esc($taskCreateURL) ?>">
        <button class="btn btn-primary me-2" type="button" id="btnCreateTask">Neuer Task</button>
    </a>
</div>
<table class="table">
    <thead>
        <tr>
            <th>Task</th>
            <th>Spalte</th>
            <th>Person</th>
            <th>Notizen</th>
            <th>Bearbeiten</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($tasks as $task): ?>
            <tr>
                <td><?= esc($task->task) ?></td>
                <td><?= esc($task->columnName) ?></td>
                <td><?= esc($task->userFirstName) ?> <?= esc($task->userLastName) ?></td>
                <td><?= esc($task->notes) ?></td>
                <td>
                    <div class="d-flex" style="gap: 0.5em">
                        <a href="<?= esc($taskEditURL) ?>/<?= esc($task->id) ?>"><i class="fas fa-edit"></i></a>
                        <a href="<?= esc($taskDeleteURL) ?>/<?= esc($task->id) ?>"><i class="fas fa-trash"></i></a>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
