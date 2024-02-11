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
    <div class="d-flex flex-row justify-content-between align-items-center p-3">
        <button class="btn btn-secondary dropdown-toggle me-2" type="button" id="dropdownBoards" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <?= esc($activeBoard->name) ?>
        </button>
        <div class="dropdown-menu" aria-labelledby="dropdownBoards">
            <?php foreach ($boards as $board): ?>
                <a class="dropdown-item" href="<?= esc($columnsURL) ?>/<?= esc($board->id) ?>"><?= esc($board->name) ?></a>
            <?php endforeach; ?>
        </div>
        <a href="<?= esc($columnCreateURL) ?>">
            <button class="btn btn-secondary me-2" type="button" id="btnCreateTask">Neue Spalte</button>
        </a>
    </div>
    <div class="row">
        <?php foreach ($columns as $col): ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title"><?= esc($col->name) ?></h5>
                        <p class="card-text"><?= esc($col->description) ?></p>
                        <p class="card-text"><strong>Board:</strong> <?= esc($col->boardName) ?></p>
                        <p class="card-text"><strong>ID:</strong> <?= esc($col->id) ?></p>
                        <p class="card-text"><strong>Sort ID:</strong> <?= esc($col->sortId) ?></p>
                        <div class="mt-auto">
                            <div class="d-flex justify-content-between">
                                <a href="<?= esc($columnEditURL) ?>/<?= esc($col->id) ?>" class="btn btn-primary"><i class="fas fa-edit"></i> Bearbeiten</a>
                                <a href="<?= esc($columnDeleteURL) ?>/<?= esc($col->id) ?>" class="btn btn-danger"><i class="fas fa-trash"></i> Löschen</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        <!-- New Card with Plus Button -->
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body d-flex flex-column align-items-center justify-content-center">
                    <button class="btn btn-outline-primary" onclick="window.location.href='<?= esc($columnCreateURL) ?>'">
                        <i class="fas fa-plus"></i> Neue Spalte
                    </button>
                </div>
            </div>
        </div>
    </div>
</main>