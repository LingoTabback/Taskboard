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

        echo view('templates/head');
        echo view('templates/menu');

        $tasks = $model->getTasksFromBoard(1);
        $data = ['tasks' => $tasks];
        echo view('templates/task_cards_test', $data);

        echo view('templates/footer');

    }
}
