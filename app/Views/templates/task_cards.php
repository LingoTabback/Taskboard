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
/* @var $taskMoveURL string */
?>

<main role="main">
    <div class="d-flex flex-row justify-content-center pt-5 pb-5">
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
                <?php foreach ($columns as $col): ?>
                    <div class="card no-border shadow-box" style="width: 22.5em; min-width: 22.5em;">
                        <div class="card-header header-column">
                            <h3 class="card-title h5 mb-1"><?= esc($col->name) ?></h3>
                            <small class="mb-0 text-muted"><?= esc($col->description) ?></small>
                        </div>
                        <div class="card-body d-flex flex-column gap-2">
                            <div class="d-flex flex-column gap-2 drag-container" columnid="<?=esc($col->id)?>">
                            <?php foreach ($tasks as $task): ?>
                                <?php if ($task->columnId !== $col->id) continue; ?>
                                <div class="card taskcard no-border draggable" taskid="<?=esc($task->id)?>">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between gap-1 mb-1">
                                            <span class="link-info link-underline-opacity-0 flex-grow-1 drag-handle">
                                                <i class="<?=esc($task->taskTypeIcon)?> fa-fw" title="<?=esc($task->taskTypeName)?>"></i> <?=esc($task->task)?>
                                            </span>
                                            <div class="d-flex" style="gap: 0.5em">
                                                <a href="<?=esc($taskEditURL).'/'.esc($task->id)?>"><i class="fas fa-edit link-info" title="Bearbeiten"></i></a>
                                                <a href="<?=esc($taskDeleteURL).'/'.esc($task->id)?>"><i class="fas fa-trash link-info" title="Löschen"></i></a>
                                            </div>
                                        </div>
                                        <div class="mb-1 d-flex justify-content-between">
                                            <div class="text-secondary-emphasis">
                                                <i class="fa-regular fa-calendar fa-fw"></i> <?=esc($task->createDate->format('d.m.Y'))?>
                                            </div>
                                            <?php if ($task->useReminder): ?>
                                                <div class="text-secondary-emphasis">
                                                    <i class="fa-regular fa-bell fa-fw text-danger"></i><?=esc($task->remindDate->format('d.m.Y H:i'))?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="mb-1 d-flex <?php echo ($task->notes !== '' ? 'justify-content-between' : 'justify-content-end'); ?> ">
                                            <?php if ($task->notes !== ''): ?>
                                                <button class="btn btn-secondary opacity-75" style="background: none" data-bs-toggle="collapse" data-bs-target="#notes<?=esc($task->id)?>">
                                                    <i class="fa-regular fa-comment-dots fa-fw"></i> Notizen
                                                </button>
                                            <?php endif; ?>
                                            <span class="rounded-circle text-xs personlogo" title="<?=esc($task->userFirstName).' '.esc($task->userLastName)?>" style="color: #FFFFFF; background-color: <?=ColorUtils::colorFromId($task->userId)?>;">
                                                <?=esc(substr($task->userFirstName, 0, 1) . substr($task->userLastName, 0, 1))?>
                                            </span>
                                        </div>
                                        <?php if ($task->notes !== ''): ?>
                                            <div class="collapse mt-2" id="notes<?=esc($task->id)?>">
                                                <div class="card p-0 no-border" style="background-color: rgb(20, 20, 20)">
                                                    <div class="card-body p-2 overflow-y-auto" style="max-height: 10em">
                                                        <?=nl2br(esc($task->notes))?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            </div>
                            <div class="d-flex justify-content-center">
                                <a href="<?=esc($taskCreateURL).'/'.esc($col->id)?>" class="link-info" title="Neuer Task">
                                    <i class="fas fa-plus-circle fa-2x"></i>
                                </a>
                            </div>
                        </div>
                    </div>
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
        $('.drag-container').each(function (i, el) {
            new Sortable(el, {
                group: 'tasks',
                draggable: '.draggable',
                handle: '.drag-handle',
                chosenClass: 'dragging',
                animation: 150,
                revertOnSpill: true,
                scroll: true,
                delay: 250,
                delayOnTouchOnly: true,
                onEnd: moveTask
            });
        });
    });

    function moveTask(evt) {
        let sibling = $(evt.to).children().get(evt.newIndex + 1);
        $.ajax({
            url: "<?=esc($taskMoveURL)?>",
            method: 'post',
            data: {
                taskid: evt.item.getAttribute('taskid'),
                siblingid: sibling !== undefined && sibling.hasAttribute('taskid') ? sibling.getAttribute('taskid') : -1,
                targetcol: evt.to.getAttribute('columnid')
            },
            dataType: 'json'
        });
    }
</script>
