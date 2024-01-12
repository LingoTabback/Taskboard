<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="robots" content="noindex">

    <title><?= lang('Errors.whoops') ?></title>

    <style>
        <?= preg_replace('#[\r\n\t ]+#', ' ', file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'debug.css')) ?>
    </style>
    <style>
        h1.headline2 { margin-top: 2%; font-size: 5rem; }
        .meme { width: 75%; height: auto; margin-top: 5%; }
    </style>
</head>
<body>

    <div class="container text-center">
        <img class="meme" src="<?=base_url('MajorSkill.gif')?>" alt="major skill issue">
        <h1 class="headline2"><?= lang('Errors.whoops') ?></h1>
        <p class="lead"><?= lang('Errors.weHitASnag') ?></p>
    </div>

</body>
</html>
