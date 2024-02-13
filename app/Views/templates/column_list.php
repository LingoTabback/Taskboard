<?php

use App\DatabaseObjects\Board;
use App\DatabaseObjects\DisplayColumn;

/* @var $columns DisplayColumn[] */
/* @var $boards Board[] */
/* @var $activeBoard Board */
/* @var $boardsURL string */
/* @var $tasksURL string */
/* @var $columnsURL string */
/* @var $columnCreateURL string */
/* @var $columnEditURL string */
/* @var $columnDeleteURL string */
/* @var $boardCreateURL string */
/* @var $columnMoveURL string */
?>

<main role="main">
    <div class="d-flex flex-row justify-content-center pt-5 pb-5">
        <div class="card no-border shadow-box">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="h3">Spalten</span>
                    <div class="d-flex align-items-center gap-2">
                        <a href="<?=esc($columnCreateURL)?>" class="link-info" title="Neue Spalte">
                            <i class="fas fa-plus-circle fa-2x"></i>
                        </a>
                        <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownBoards" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <?= esc($activeBoard->name) ?>
                        </button>
                        <div class="dropdown-menu shadow-box" aria-labelledby="dropdownBoards">
                            <?php foreach ($boards as $board): ?>
                                <a class="dropdown-item" href="<?= esc($boardsURL).'/'.esc($board->id) ?>"><?= esc($board->name) ?></a>
                            <?php endforeach; ?>
                            <a class="dropdown-item link-info" href="<?=esc($boardCreateURL)?>">
                                <i class="fas fa-plus"></i> Neu
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body grid-container gap-3 drag-container" style="min-width: 22em; max-width: 90vw;" boardid="<?=esc($activeBoard->id)?>">
                <?php foreach ($columns as $col): ?>
                    <div class="card no-border shadow-box overflow-hidden draggable" colid="<?=esc($col->id)?>">
                        <div class="card-header header-column d-flex justify-content-between flex-wrap gap-2 w-auto">
                            <h3 class="card-title h5 mb-1 flex-grow-1 drag-handle"><?= esc($col->name) ?></h3>
                            <div class="d-flex" style="gap: 0.5em">
                                <a href="<?= esc($columnEditURL).'/'.esc($col->id) ?>"><i class="fas fa-edit link-info" title="Bearbeiten"></i></a>
                                <a href="<?= esc($columnDeleteURL).'/'.esc($col->id) ?>"><i class="fas fa-trash link-info" title="Löschen"></i></a>
                            </div>
                        </div>
                        <div class="card-body d-flex flex-column align-items-start gap-2">
                            <a href="<?=esc($tasksURL).'/'.$col->boradId?>" class="link-info link-underline-opacity-0">
                                <i class="fa-solid fa-list-check"></i>
                                Tasks: <?=esc($col->numTasks)?>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</main>

<script>
    $(document).ready(function () {
        $('.drag-container').each(function (i, el) {
            new Sortable(el, {
                group: 'cols',
                draggable: '.draggable',
                handle: '.drag-handle',
                chosenClass: 'dragging',
                animation: 150,
                revertOnSpill: true,
                scroll: true,
                delay: 250,
                delayOnTouchOnly: true,
                onEnd: moveCol
            });
        });
    });

    function moveCol(evt) {
        let sibling = $(evt.to).children().get(evt.newIndex + 1);
        $.ajax({
            url: "<?=esc($columnMoveURL)?>",
            method: 'post',
            data: {
                colid: evt.item.getAttribute('colid'),
                siblingid: sibling !== undefined && sibling.hasAttribute('colid') ? sibling.getAttribute('colid') : -1,
                targetbrd: evt.to.getAttribute('boardid')
            },
            dataType: 'json'
        });
    }
</script>
