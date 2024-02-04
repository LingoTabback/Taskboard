<?php

use App\DatabaseObjects\Board;
use App\DatabaseObjects\DisplayColumn;

/* @var $boards Board[] */
/* @var $createURL string */
/* @var $editURL string */
/* @var $deleteURL string */
?>

<main role="main">
    <div class="d-flex p-3">
        <a href="<?= esc($createURL) ?>">
            <button class="btn btn-primary me-2" type="button" id="btnCreateTask">Neues Board</button>
        </a>
    </div>
    <table class="table">
        <thead>
            <tr>
                <th>Board</th>
                <th>Bearbeiten</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($boards as $board): ?>
                <tr>
                    <td><?= esc($board->name) ?></td>
                    <td>
                        <div class="d-flex" style="gap: 0.5em">
                            <a href="<?= esc($editURL) ?>/<?= esc($board->id) ?>"><i class="fas fa-edit"></i></a>
                            <a href="<?= esc($deleteURL) ?>/<?= esc($board->id) ?>"><i class="fas fa-trash"></i></a>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</main>
