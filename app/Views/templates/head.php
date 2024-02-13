<?php
/* @var $title string */
/* @var $styles array[] */
/* @var $scripts array[] */
?>

<!DOCTYPE html>
<html lang="de" class="h-100" data-theme="dark" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="dark">
    <?php if (isset($title)): ?>
    <title><?= esc($title) ?></title>
    <?php endif; ?>
    <link rel="stylesheet" href="<?= base_url('Bootstrap.theme.min.css') ?>"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css"/>
    <link rel="stylesheet" type="text/css" href="<?= base_url('Style.css') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dragula/3.7.3/dragula.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.2/Sortable.min.js"></script>

    <?php
    if (isset($styles))
    {
        foreach ($styles as $style)
        {?>
            <link rel="stylesheet" href="<?= esc($style['link']) ?>"/>
        <?php }
    }?>

    <?php
    if (isset($scripts))
    {
        foreach ($scripts as $script)
        {?>
            <script src="<?= esc($script['src']) ?>"></script>
        <?php }
    }?>
</head>
<body class="d-flex flex-column h-100">
