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
    <div class="d-flex flex-row justify-content-center">
        <div class="card no-border shadow-box">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center gap-2">
                    <span class="h3 flex-grow-1">Boards</span>
                    <div class="input-group" style="width: auto">
                        <div class="input-group-text bg-input">
                            <span title="Suche"><i class="fa-solid fa-magnifying-glass align-self-center" style="color: var(--bs-secondary-color)"></i></span>
                        </div>
                        <input type="search" class="form-control" id="search-boards" placeholder="Boards suchen..." style="max-width: 20em">
                    </div>
                    <a href="<?=esc($createURL)?>" class="link-info" title="Neues Board">
                        <i class="fas fa-plus-circle fa-2x"></i>
                    </a>
                </div>
            </div>
            <div class="card-body grid-container gap-3" style="min-width: 22em; max-width: 90vw;">
                <?php foreach ($boards as $board): ?>
                    <div class="card no-border shadow-box overflow-hidden boardcard" searchstr="<?=esc($board->name)?>">
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

<script>
    let input;
    let fuse;
    let boards;

    $(document).ready(function () {
        input = $('#search-boards').get(0);
        boards = $('.boardcard').get();

        fuse = new Fuse(boards, {
            keys: ['searchstr'],
            shouldSort: true,
            ignoreLocation: true,
            threshold: 0.5,
            getFn: (el, key) => el.getAttribute(key),
        });

        let timeout;
        input.addEventListener('input', function () {
            clearTimeout(timeout);
            timeout = setTimeout(search, 500);
        });
    });

    function search() {
        if (input.value === '') {
            for (let b of boards)
                b.style.display = 'block';
        }
        else {
            for (let b of boards)
                b.style.display = 'none';
            for (let res of fuse.search(input.value))
                res.item.style.display = 'block';
        }
    }
</script>
