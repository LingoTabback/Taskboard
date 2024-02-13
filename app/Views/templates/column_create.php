<?php

use App\DatabaseObjects\Column;
use App\Cells\InvalidFeedback;

/* @var $activeColumn Column */
/* @var $submitURL string */
/* @var $abortURL string */
/* @var $isDelete bool */
/* @var $errorMessages array<string, string> */
/* @var $oldPost array<string, string> */

$showDelete = isset($isDelete) && $isDelete;
$showCreate = !$showDelete && !isset($activeColumn);
$showEdit = !$showCreate && !$showDelete && isset($activeColumn);

if (!isset($errorMessages))
    $errorMessages = [];
$hasErrors = !empty($errorMessages);
?>

<div class="container pb-5">
    <div class="card mt-5 no-border shadow-box">
        <div class="card-header">
            <div class="d-flex justify-content-between">
                <?php if ($showDelete): ?>
                    <div class="h3">Spalte löschen</div>
                <?php elseif ($showCreate): ?>
                    <div class="h3">Spalte erstellen</div>
                <?php else: ?>
                    <div class="h3">Spalte bearbeiten</div>
                <?php endif; ?>
            </div>
        </div>
        <div class="card-body pt-0 pb-0">
            <div class="row">
                <div class="col-12">
                    <div class="card-content">
                        <div class="card-body">
                            <form id="form" action="<?= esc($submitURL) ?>" method="post">
                                <fieldset <?php if ($showDelete) echo 'disabled'; ?>>
                                    <!-- Spalte -->
                                    <div class="form-group has-validation mb-4 mt-0">
                                        <label for="column" class="form-label mb-0">Spalte:</label>
                                        <input type="text" class="form-control rounded <?php if ($hasErrors && isset($errorMessages['column'])) echo 'is-invalid'; ?>"
                                               id="column" name="column" placeholder="Namen eingeben..."
                                               value="<?php if (isset($oldPost['column'])) echo $oldPost['column']; elseif (!$showCreate) echo esc($activeColumn->name); ?>"/>
                                        <?= InvalidFeedback::render($errorMessages, 'column') ?>
                                    </div>
                                    <!-- Beschreibung -->
                                    <div class="form-group has-validation mb-4">
                                        <label for="description" class="form-label mb-0">Spaltenbeschreibung:</label>
                                            <textarea class="form-control <?php if ($hasErrors && isset($errorMessages['description'])) echo 'is-invalid'; ?>"
                                                      rows="3" id="description" name="description" placeholder="Spaltenbeschreibung..."><?php if (isset($oldPost['description'])) echo $oldPost['description']; elseif (!$showCreate) echo esc($activeColumn->description); ?></textarea>
                                        <?= InvalidFeedback::render($errorMessages, 'description') ?>
                                    </div>
                                </fieldset>
                                <div class="row">
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
                                            <button class="btn btn-warning mb-2" type="button">
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
