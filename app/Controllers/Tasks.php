<?php

namespace App\Controllers;
use App\DatabaseObjects\Task;
use App\Models\TasksModel;
use CodeIgniter\HTTP\RedirectResponse;
use Config\Services;
use DateTime;

class Tasks extends BaseController
{

    private string $thisURL = 'tasks';

    public function getIndex(): RedirectResponse
    {
        $model = new TasksModel();
        $firstBoard = $model->getAllBoards()[0]->id;
        return redirect()->to(base_url("$this->thisURL/board/$firstBoard"));
    }

    public function getBoard(int $boardId): void
    {
        $model = new TasksModel();
        $activeBoard = $model->getBoard($boardId);

        $session = session();
        $session->set('jump_back_url', "$this->thisURL/board/$boardId");
        $session->close();

        $data = [
            'columns' => $model->getColsFromBoard($boardId),
            'tasks' => $model->getDisplayTasksFromBoard($boardId),
            'boards' => $model->getAllBoards(),
            'activeBoard' => $activeBoard,
            'boardsURL' => base_url("$this->thisURL/board"),
            'taskCreateURL' => base_url("$this->thisURL/create/$boardId"),
            'taskEditURL' => base_url("$this->thisURL/edit"),
            'taskDeleteURL' => base_url("$this->thisURL/delete"),
            'columnCreateURL' => base_url("columns/create/$boardId"),
            'boardCreateURL' => base_url('boards/create'),
            'taskMoveURL' => base_url("$this->thisURL/domove"),
        ];

        echo view('templates/head', ['title' => $activeBoard->name]);
        echo view('templates/menu', ['activeIndex' => 0]);
        echo view('templates/task_cards', $data);
        echo view('templates/footer');
    }

    public function getCreate(int $boardId, int $columnId = 0): void
    {
        helper('form');

        $model = new TasksModel();
        $session = session();
        $dataCreate = [
            'taskTypes' => $model->getAllTaskTypes(),
            'users' => $model->getAllUsers(),
            'columns' => $model->getColsFromBoard($boardId),
            'selectedColId' => $columnId,
            'submitURL' => base_url("$this->thisURL/docreate/$boardId"),
            'abortURL' => base_url($session->has('jump_back_url') ? $session->get('jump_back_url') : "$this->thisURL/board/$boardId"),
            'errorMessages' => esc(validation_errors())
        ];
        $oldInput = $session->get('_ci_old_input');
        $session->close();
        if (isset($oldInput['post']))
            $dataCreate['oldPost'] = esc($oldInput['post']);

        echo view('templates/head', ['title' => 'Task erstellen']);
        echo view('templates/menu', ['activeIndex' => 0]);
        echo view('templates/task_create', $dataCreate);
        echo view('templates/footer');
    }

    public function getEdit(int $taskId): void
    {
        helper('form');

        $model = new TasksModel();
        $board = $model->getBoardFromTask($taskId);
        $boardId = $board->id;
        $session = session();
        $dataCreate = [
            'taskTypes' => $model->getAllTaskTypes(),
            'users' => $model->getAllUsers(),
            'columns' => $model->getColsFromBoard($boardId),
            'activeTask' => $model->getTask($taskId),
            'submitURL' => base_url("$this->thisURL/doedit/$taskId"),
            'abortURL' => base_url($session->has('jump_back_url') ? $session->get('jump_back_url') : "$this->thisURL/board/$boardId"),
            'errorMessages' => esc(validation_errors())
        ];
        $oldInput = $session->get('_ci_old_input');
        $session->close();
        if (isset($oldInput['post']))
            $dataCreate['oldPost'] = esc($oldInput['post']);

        echo view('templates/head', ['title' => 'Task bearbeiten']);
        echo view('templates/menu', ['activeIndex' => 0]);
        echo view('templates/task_create', $dataCreate);
        echo view('templates/footer');
    }

    public function getDelete(int $taskId): void
    {
        $model = new TasksModel();
        $board = $model->getBoardFromTask($taskId);
        $boardId = $board->id;
        $session = session();
        $dataCreate = [
            'taskTypes' => $model->getAllTaskTypes(),
            'users' => $model->getAllUsers(),
            'columns' => $model->getColsFromBoard($boardId),
            'activeTask' => $model->getTask($taskId),
            'isDelete' => TRUE,
            'submitURL' => base_url("$this->thisURL/dodelete/$taskId"),
            'abortURL' => base_url($session->has('jump_back_url') ? $session->get('jump_back_url') : "$this->thisURL/board/$boardId")
        ];
        $session->close();

        echo view('templates/head', ['title' => 'Task löschen']);
        echo view('templates/menu', ['activeIndex' => 0]);
        echo view('templates/task_create', $dataCreate);
        echo view('templates/footer');
    }

    public function postDoCreate(int $boardId): RedirectResponse
    {
        $validation = Services::validation();
        $validation->setRuleGroup('taskCreateAndEdit');
        if (!$validation->withRequest($this->request)->run())
            return redirect()->back()->withInput();

        $validData = $validation->getValidated();
        $task = new Task();
        $task->userId = (int)$validData['personid'];
        $task->typeId = (int)$validData['typeid'];
        $task->columnId = (int)$validData['columnid'];
        $task->task = $validData['task'];
        $task->notes = $validData['notes'] ?? '';
        $task->createDate = new DateTime();
        $task->remindDate = DateTime::createFromFormat('Y-m-d H:i', $validData['reminderdate'] . ' ' . $validData['remindertime']);
        $task->useReminder = isset($validData['reminderuse']) && $validData['reminderuse'];

        $model = new TasksModel();
        $model->insertTask($task);
        $session = session();
        return redirect()->to(base_url($session->has('jump_back_url') ? $session->get('jump_back_url') : "$this->thisURL/board/$boardId"));
    }

    public function postDoEdit(int $taskId): RedirectResponse
    {
        $model = new TasksModel();
        $board = $model->getBoardFromTask($taskId);
        $boardId = $board->id;

        $validation = Services::validation();
        $validation->setRuleGroup('taskCreateAndEdit');
        if (!$validation->withRequest($this->request)->run())
            return redirect()->back()->withInput();

        $validData = $validation->getValidated();

        $task = $model->getTask($taskId);
        $task->userId = $validData['personid'];
        $task->typeId = (int)$validData['typeid'];
        $task->columnId = $validData['columnid'];
        $task->task = $validData['task'];
        $task->notes = $validData['notes'] ?? '';
        $task->remindDate =  DateTime::createFromFormat('Y-m-d H:i', $validData['reminderdate'] . ' ' . $validData['remindertime']);
        $task->useReminder = isset($validData['reminderuse']) && $validData['reminderuse'];

        $model->editTask($task);
        $session = session();
        return redirect()->to(base_url($session->has('jump_back_url') ? $session->get('jump_back_url') : "$this->thisURL/board/$boardId"));
    }

    public function postDoDelete(int $taskId): RedirectResponse
    {
        $model = new TasksModel();
        $board = $model->getBoardFromTask($taskId);
        $boardId = $board->id;

        $model->removeTask($taskId);
        $session = session();
        return redirect()->to(base_url($session->has('jump_back_url') ? $session->get('jump_back_url') : "$this->thisURL/board/$boardId"));
    }

    public function postDoMove(): void
    {
        $validation = Services::validation();
        $validation->setRuleGroup('taskMove');
        if (!$validation->withRequest($this->request)->run())
        {
            echo 'failure';
            return;
        }

        $validData = $validation->getValidated();
        $model = new TasksModel();
        if ($model->moveTask((int)$validData['taskid'], (int)$validData['siblingid'], (int)$validData['targetcol']))
            echo 'success';
        else
            echo 'failure';
    }
}
