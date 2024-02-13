<?php

use App\DatabaseObjects\DisplayBoard;

/* @var $boards DisplayBoard[] */
/* @var $createURL string */
/* @var $editURL string */
/* @var $deleteURL string */
/* @var $tasksURL string */
/* @var $colsURL string */
?>

<main role="main">
    <div class="d-flex flex-row justify-content-center pt-5 pb-5">
        <div class="card no-border shadow-box">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="h3">Boards</span>
                    <a href="<?=esc($createURL)?>" class="link-info" title="Neues Board">
                        <i class="fas fa-plus-circle fa-2x"></i>
                    </a>
                </div>
            </div>
            <div class="card-body grid-container gap-3" style="min-width: 22em; max-width: 90vw;">
                <?php foreach ($boards as $board): ?>
                    <div class="card no-border shadow-box overflow-hidden">
                        <div class="card-header header-column d-flex justify-content-between flex-wrap gap-2">
                            <h3 class="card-title h5 mb-1"><?= esc($board->name) ?></h3>
                            <div class="d-flex" style="gap: 0.5em">
                                <a href="<?= esc($editURL).'/'.esc($board->id) ?>"><i class="fas fa-edit link-info" title="Bearbeiten"></i></a>
                                <a href="<?= esc($deleteURL).'/'.esc($board->id) ?>"><i class="fas fa-trash link-info" title="Löschen"></i></a>
                            </div>
                        </div>
                        <div class="card-body d-flex flex-column align-items-start gap-2">
                            <a href="<?=esc($colsURL).'/'.$board->id?>" class="link-info link-underline-opacity-0">
                                <i class="fa-solid fa-chart-simple fa-rotate-180"></i>
                                Spalten: <?=esc($board->numColumns)?>
                            </a>
                            <a href="<?=esc($tasksURL).'/'.$board->id?>" class="link-info link-underline-opacity-0">
                                <i class="fa-solid fa-list-check"></i>
                                Tasks: <?=esc($board->numTasks)?>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</main>
