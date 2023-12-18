<?php

namespace App\Controllers;

class viewTemplate extends BaseController
{
    public function getconstruct(): void
    {
        echo view('templates/head');
        echo view('templates/menu');
        echo view('templates/footer');
    }
}
