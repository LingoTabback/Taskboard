<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="robots" content="noindex">

    <title><?= lang('Errors.whoops') ?></title>

    <style>
        <?= preg_replace('#[\r\n\t ]+#', ' ', file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'debug.css')) ?>
    </style>
</head>
<body>

    <div class="container text-center">
        <img src="<?=base_url('MajorSkill.gif')?>" alt="major skill issue" style="width: 75%; height: auto; margin-top: 5%;">
        <h1 style="margin-top: 2%; font-size: 5rem;"><?= lang('Errors.whoops') ?></h1>
        <p class="lead"><?= lang('Errors.weHitASnag') ?></p>
    </div>

</body>
</html>
