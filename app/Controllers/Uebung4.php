<?php

namespace App\Controllers;

class Uebung4 extends BaseController
{
    public function getviewGruppennummer(): void
    {
        var_dump(21);
    }

    public function getIndex(): void
    {
        echo view('templates/head');
        echo view('templates/menu');
        echo view('templates/footer');
    }
}
