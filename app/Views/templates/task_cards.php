<?php

use App\DatabaseObjects\Board;
use App\DatabaseObjects\Column;
use App\DatabaseObjects\DisplayTask;
use App\Cells\ColorUtils;

/* @var $columns Column[] */
/* @var $tasks DisplayTask[] */
/* @var $boards Board[] */
/* @var $activeBoard Board */
/* @var $boardsURL string */
/* @var $taskCreateURL string */
/* @var $taskEditURL string */
/* @var $taskDeleteURL string */
/* @var $columnCreateURL string */
/* @var $boardCreateURL string */
?>

<main role="main">
    <div class="d-flex flex-row justify-content-center pt-5">
        <div class="card no-border shadow-box" style="max-width: 90%; min-width: 25em;">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="h3">Tasks</span>
                    <div class="d-flex">
                        <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownBoards" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <?= esc($activeBoard->name) ?>
                        </button>
                        <div class="dropdown-menu shadow-box" aria-labelledby="dropdownBoards">
                            <?php foreach ($boards as $board): ?>
                                <a class="dropdown-item" href="<?= esc($boardsURL).'/'.esc($board->id) ?>"><?= esc($board->name) ?></a>
                            <?php endforeach; ?>
                            <a class="dropdown-item link-info" href="<?= esc($boardCreateURL) ?>">
                                <i class="fas fa-plus"></i> Neu
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body d-flex flex-row flex-nowrap overflow-auto gap-2">
                <?php foreach ($columns as $col): ?><div class="card no-border shadow-box" style="width: 22.5em; min-width: 22.5em;">
                    <div class="card-header header-column">
                        <h3 class="card-title h5 mb-1"><?= esc($col->name) ?></h3>
                        <small class="mb-0 text-muted"><?= esc($col->description) ?></small>
                    </div>
                    <div class="card-body d-flex flex-column gap-2 drag-container">
                        <?php foreach ($tasks as $task): ?><?php if ($task->columnId !== $col->id) continue; ?>
                            <div class="card taskcard no-border cursor-grab">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between mb-1">
                                        <a href="<?=esc($taskEditURL)?>/<?=esc($task->id)?>" class="link-info link-underline-opacity-0">
                                            <i class="<?=esc($task->taskTypeIcon)?> fa-fw" title="<?=esc($task->taskTypeName)?>"></i> <?=esc($task->task)?>
                                        </a>
                                        <div class="dropdown position-static">
                                            <a class="btn btn-link ps-0 pt-0 pb-0 pe-2" role="button" data-bs-toggle="dropdown" data-boundary="viewport" aria-haspopup="true" aria-expanded="false" >
                                                <i class="fas fa-caret-square-down text-info"></i>
                                            </a>
                                            <div class="dropdown-menu shadow-box">
                                                <a class="dropdown-item text-info" href="<?=esc($taskEditURL).'/'.esc($task->id)?>">
                                                    <span title="Bearbeiten" class="icon-menu"><i class="fas fa-edit"></i></span> Bearbeiten
                                                </a>
                                                <a class="dropdown-item text-info" href="<?=esc($taskDeleteURL).'/'.esc($task->id)?>">
                                                    <span title="Löschen" class="icon-menu"><i class="fas fa-trash"></i></span> Löschen
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-1 d-flex justify-content-between">
                                        <div class="text-secondary-emphasis">
                                            <i class="fa-regular fa-calendar fa-fw"></i> <?=esc($task->createDate->format('d.m.Y'))?>
                                        </div>
                                        <?php if ($task->useReminder): ?><div class="text-secondary-emphasis">
                                            <i class="fa-regular fa-bell fa-fw text-danger"></i><?=esc($task->remindDate->format('d.m.Y H:i'))?></div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="mb-1 d-flex <?php echo ($task->notes !== '' ? 'justify-content-between' : 'justify-content-end'); ?> ">
                                        <?php if ($task->notes !== ''): ?><button class="btn btn-secondary opacity-75" style="background: none" data-bs-toggle="collapse" data-bs-target="#notes<?=esc($task->id)?>"><i class="fa-regular fa-comment-dots fa-fw"></i> Notizen</button>
                                        <?php endif; ?>
                                        <span class="rounded-circle text-xs personlogo" title="<?=esc($task->userFirstName).' '.esc($task->userLastName)?>" style="color: #FFFFFF; background-color: <?=ColorUtils::colorFromId($task->userId)?>;">
                                            <?=esc(substr($task->userFirstName, 0, 1) . substr($task->userLastName, 0, 1))?>
                                        </span>
                                    </div>
                                    <?php if ($task->notes !== ''): ?>
                                        <div class="collapse mt-2" id="notes<?=esc($task->id)?>">
                                            <div class="card p-0" style="background-color: #202020">
                                                <div class="card-body p-2 overflow-y-scroll" style="max-height: 10em">
                                                    <?=nl2br(esc($task->notes))?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <div class="d-flex justify-content-center non-draggable">
                            <a href="<?=esc($taskCreateURL).'/'.esc($col->id)?>" class="link-info" title="Neuer Task">
                                <i class="fas fa-plus-circle fa-2x"></i>
                            </a>
                        </div>
                    </div></div>
                <?php endforeach; ?>
                <div class="d-flex flex-column justify-content-center p-5">
                    <a href="<?=esc($columnCreateURL)?>" class="text-center link-info" title="Neue Spalte">
                        <i class="fas fa-plus-circle fa-4x shadow-svg"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
    $(document).ready(function () {
        let dra = dragula({
            isContainer: function (el) {
                return el.classList.contains('drag-container');
            },
            moves: function (el, source, handle, sibling) {
                return !el.classList.contains('non-draggable');
            },
            accepts: function (el, target, source, sibling) {
                return !(sibling === null);
            },
            invalid: function (el, handle) {
                return false;
            },
            direction: 'vertical',
            copy: false,
            copySortSource: false,
            removeOnSpill: false,
            revertOnSpill: true,
            mirrorContainer: document.body,
            ignoreInputTextSelection: false,
            slideFactorX: 10,
            slideFactorY: 10
        });
        dra.on('drag', function (el, target, source, sibling) {
            el.style.cursor = 'grabbing';
        });
        dra.on('drop', function (el, target, source, sibling) {
            el.style.cursor = 'grab';
            //optional, wenn SpaltenId und Reihenfolge updaten will
        });
        dra.on('cancel', function (el, container, source) {
            el.style.cursor = 'grab';
        });
    });
</script>