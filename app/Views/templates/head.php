<?php
/* @var $title string */
/* @var $styles array[] */
/* @var $scripts array[] */
?>

<!DOCTYPE html>
<html lang="de" class="h-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php if (isset($title)): ?>
    <title><?= esc($title) ?></title>
    <?php endif; ?>
    <!--<link rel="stylesheet" href="https://unpkg.com/bootstrap@5.3.2/dist/css/bootstrap.min.css"/>-->
    <link rel="stylesheet" href="<?= base_url('Bootstrap.theme.min.css') ?>"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css"/>
    <link rel="stylesheet" type="text/css" href="<?= base_url('Style.css') ?>">
    <script
            src="https://unpkg.com/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js">
    </script>

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
