<?php

namespace App\Controllers;
use App\DatabaseObjects\DisplayTask;
use App\DatabaseObjects\Task;
use App\Models\TasksModel;
use CodeIgniter\HTTP\RedirectResponse;
use Config\Services;
use DateTime;

class Uebung5 extends BaseController
{
    public function getIndex(): RedirectResponse
    {
        $model = new TasksModel();
        $firstBoard = $model->getAllBoards()[0]->id;
        return redirect()->to(base_url("uebung5/board/$firstBoard"));
    }

    public function getBoard(int $boardId): void
    {
        $model = new TasksModel();
        $activeBoard = $model->getBoard($boardId);

        $data = [
            'tasks' => $model->getDisplayTasksFromBoard($boardId),
            'boards' => $model->getAllBoards(),
            'activeBoard' => $activeBoard,
            'boardsURL' => base_url('uebung5/board'),
            'taskCreateURL' => base_url("uebung5/create/$boardId"),
            'taskEditURL' => base_url('uebung5/edit'),
            'taskDeleteURL' => base_url('uebung5/delete'),
        ];

        echo view('templates/head', ['title' => $activeBoard->name]);
        echo view('templates/menu');
        echo view('templates/task_cards_test', $data);
        echo view('templates/footer');
    }

    public function getCreate(int $boardId): void
    {
        $model = new TasksModel();
        $dataCreate = [
            'users' => $model->getAllUsers(),
            'columns' => $model->getColsFromBoard($boardId),
            'submitURL' => base_url("uebung5/docreate/$boardId"),
            'abortURL' => base_url("uebung5/board/$boardId")
        ];
        $dataHeader = [
            'title' => 'Task erstellen',
            'styles' => [
                ['link' => 'https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.min.css']
            ],
            'scripts' => [
                ['src' => 'https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.3/jquery.min.js'],
                ['src' => 'https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.full.min.js']
            ]
        ];
        echo view('templates/head', $dataHeader);
        echo view('templates/menu');
        echo view('templates/task_create_test', $dataCreate);
        echo view('templates/footer');
    }

    public function getEdit(int $taskId): void
    {
        $model = new TasksModel();
        $board = $model->getBoardFromTask($taskId);
        $boardId = $board->id;
        $dataCreate = [
            'users' => $model->getAllUsers(),
            'columns' => $model->getColsFromBoard($boardId),
            'activeTask' => $model->getTask($taskId),
            'submitURL' => base_url("uebung5/doedit/$taskId"),
            'abortURL' => base_url("uebung5/board/$boardId")
        ];
        $dataHeader = [
            'title' => 'Task bearbeiten',
            'styles' => [
                ['link' => 'https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.min.css']
            ],
            'scripts' => [
                ['src' => 'https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.3/jquery.min.js'],
                ['src' => 'https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.full.min.js']
            ]
        ];
        echo view('templates/head', $dataHeader);
        echo view('templates/menu');
        echo view('templates/task_create_test', $dataCreate);
        echo view('templates/footer');
    }

    public function getDelete(int $taskId): void
    {
        $model = new TasksModel();
        $board = $model->getBoardFromTask($taskId);
        $boardId = $board->id;
        $dataCreate = [
            'users' => $model->getAllUsers(),
            'columns' => $model->getColsFromBoard($boardId),
            'activeTask' => $model->getTask($taskId),
            'isDelete' => TRUE,
            'submitURL' => base_url("uebung5/dodelete/$taskId"),
            'abortURL' => base_url("uebung5/board/$boardId")
        ];
        $dataHeader = [
            'title' => 'Task löschen',
            'styles' => [
                ['link' => 'https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.min.css']
            ],
            'scripts' => [
                ['src' => 'https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.3/jquery.min.js'],
                ['src' => 'https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.full.min.js']
            ]
        ];
        echo view('templates/head', $dataHeader);
        echo view('templates/menu');
        echo view('templates/task_create_test', $dataCreate);
        echo view('templates/footer');
    }

    public function postDoCreate(int $boardId): RedirectResponse
    {
        $validation = Services::validation();
        $validation->setRuleGroup('taskCreate');
        if (!$validation->withRequest($this->request)->run())
            return redirect()->to(base_url("uebung5/board/$boardId"));

        $validData = $validation->getValidated();
        $task = new Task();
        $task->userId = (int)$validData['personid'];
        $task->typeId = 1;
        $task->columnId = (int)$validData['columnid'];
        $task->task = $validData['task'];
        $task->notes = $validData['notes'] ?? '';
        $task->createDate = new DateTime();
        $task->remindDate = DateTime::createFromFormat('d.m.Y H:i', $validData['reminderdate'] . ' ' . $validData['remindertime']);
        $task->useReminder = isset($validData['reminderuse']) && $validData['reminderuse'];

        $model = new TasksModel();
        $model->insertTask($task);
        return redirect()->to(base_url("uebung5/board/$boardId"));
    }

    public function postDoEdit(int $taskId): RedirectResponse
    {
        $model = new TasksModel();
        $board = $model->getBoardFromTask($taskId);
        $boardId = $board->id;

        $validation = Services::validation();
        $validation->setRuleGroup('taskEdit');
        if (!$validation->withRequest($this->request)->run())
            return redirect()->to(base_url("uebung5/board/$boardId"));

        $validData = $validation->getValidated();

        $task = $model->getTask($taskId);
        $task->userId = $validData['personid'];
        $task->columnId = $validData['columnid'];
        $task->task = $validData['task'];
        $task->notes = $validData['notes'] ?? '';
        $task->remindDate = DateTime::createFromFormat('d.m.Y H:i', $validData['reminderdate'] . ' ' . $validData['remindertime']);
        $task->useReminder = isset($validData['reminderuse']) && $validData['reminderuse'];

        $model->editTask($task);
        return redirect()->to(base_url("uebung5/board/$boardId"));
    }

    public function postDoDelete(int $taskId): RedirectResponse
    {
        $model = new TasksModel();
        $board = $model->getBoardFromTask($taskId);
        $boardId = $board->id;

        $model = new TasksModel();
        $model->removeTask($taskId);
        return redirect()->to(base_url("uebung5/board/$boardId"));
    }
}
