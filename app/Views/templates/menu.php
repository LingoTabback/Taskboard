<?php

/* @var $activeIndex int */
?>

<!-- Navigationsleiste -->
<header>
    <nav class="navbar navbar-expand-lg fixed-top bg-primary" data-bs-theme="dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?=base_url()?>">
                <img src="<?=base_url('WE_Logo.svg')?>" alt="Logo" width="150px">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarResponsive">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link <?php if ($activeIndex === 0) echo 'active'; ?>" href="<?=base_url('tasks')?>">Tasks</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php if ($activeIndex === 1) echo 'active'; ?>" href="<?=base_url('boards')?>">Boards</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php if ($activeIndex === 2) echo 'active'; ?>" href="<?=base_url('columns')?>">Spalten</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</header>
