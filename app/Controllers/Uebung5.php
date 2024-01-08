<?php

namespace App\Controllers;
use App\Models\TasksModel;

class Uebung5 extends BaseController
{
    public function getviewGruppennummer(): void
    {
        var_dump(21);
    }

    public function getIndex(): void
    {
        $model = new TasksModel();
        $test = $model->getTasksFromBoard(1);
        var_dump($test);
    }
}
