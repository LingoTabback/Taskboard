<?php

use App\DatabaseObjects\Board;
use App\DatabaseObjects\DisplayColumn;

/* @var $columns DisplayColumn[] */
/* @var $boards Board[] */
/* @var $activeBoard Board */
/* @var $columnsURL string */
/* @var $columnCreateURL string */
/* @var $columnEditURL string */
/* @var $columnDeleteURL string */
?>

<main role="main">
    <div class="d-flex p-3">
        <button class="btn btn-outline-primary dropdown-toggle me-2" type="button" id="dropdownBoards" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <?= esc($activeBoard->name) ?>
        </button>
        <div class="dropdown-menu" aria-labelledby="dropdownBoards">
            <?php foreach ($boards as $board): ?>
                <a class="dropdown-item" href="<?= esc($columnsURL) ?>/<?= esc($board->id) ?>"><?= esc($board->name) ?></a>
            <?php endforeach; ?>
        </div>
        <a href="<?= esc($columnCreateURL) ?>">
            <button class="btn btn-outline-primary me-2" type="button" id="btnCreateTask">Neue Spalte</button>
        </a>
    </div>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Board</th>
                <th>Spalte</th>
                <th>Beschreibung</th>
                <th>Sort ID</th>
                <th>Bearbeiten</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($columns as $col): ?>
                <tr>
                    <td><?= esc($col->id) ?></td>
                    <td><?= esc($col->boardName) ?></td>
                    <td><?= esc($col->name) ?></td>
                    <td><?= esc($col->description) ?></td>
                    <td><?= esc($col->sortId) ?></td>
                    <td>
                        <div class="d-flex" style="gap: 0.5em">
                            <a href="<?= esc($columnEditURL) ?>/<?= esc($col->id) ?>"><i class="fas fa-edit"></i></a>
                            <a href="<?= esc($columnDeleteURL) ?>/<?= esc($col->id) ?>"><i class="fas fa-trash"></i></a>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</main>
