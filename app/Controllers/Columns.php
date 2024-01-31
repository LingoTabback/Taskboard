<?php

namespace App\Controllers;
use App\DatabaseObjects\Column;
use App\Models\TasksModel;
use CodeIgniter\HTTP\RedirectResponse;
use Config\Services;

class Columns extends BaseController
{

    private string $thisURL = 'columns';

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

        $data = [
            'columns' => $model->getDisplayColsFromBoard($boardId),
            'boards' => $model->getAllBoards(),
            'activeBoard' => $activeBoard,
            'columnsURL' => base_url("$this->thisURL/board"),
            'columnCreateURL' => base_url("$this->thisURL/create/$boardId"),
            'columnEditURL' => base_url("$this->thisURL/edit"),
            'columnDeleteURL' => base_url("$this->thisURL/delete"),
        ];

        echo view('templates/head', ['title' => $activeBoard->name]);
        echo view('templates/menu', ['activeIndex' => 2]);
        echo view('templates/column_list', $data);
        echo view('templates/footer');
    }

    public function getCreate(int $boardId): void
    {
        helper('form');

        $dataCreate = [
            'submitURL' => base_url("$this->thisURL/docreate/$boardId"),
            'abortURL' => base_url("$this->thisURL/board/$boardId"),
            'errorMessages' => esc(validation_errors())
        ];
        $oldInput = session('_ci_old_input');
        if (isset($oldInput['post']))
            $dataCreate['oldPost'] = esc($oldInput['post']);

        echo view('templates/head', ['title' => 'Spalte erstellen']);
        echo view('templates/menu', ['activeIndex' => 2]);
        echo view('templates/column_create', $dataCreate);
        echo view('templates/footer');
    }

    public function getEdit(int $columnId): void
    {
        helper('form');

        $model = new TasksModel();
        $board = $model->getBoardFromColumn($columnId);
        $boardId = $board->id;
        $dataCreate = [
            'activeColumn' => $model->getColumn($columnId),
            'submitURL' => base_url("$this->thisURL/doedit/$columnId"),
            'abortURL' => base_url("$this->thisURL/board/$boardId"),
            'errorMessages' => esc(validation_errors())
        ];
        $oldInput = session('_ci_old_input');
        if (isset($oldInput['post']))
            $dataCreate['oldPost'] = esc($oldInput['post']);

        echo view('templates/head', ['title' => 'Spalte bearbeiten']);
        echo view('templates/menu', ['activeIndex' => 2]);
        echo view('templates/column_create', $dataCreate);
        echo view('templates/footer');
    }

    public function getDelete(int $columnId): void
    {
        $model = new TasksModel();
        $board = $model->getBoardFromColumn($columnId);
        $boardId = $board->id;
        $dataCreate = [
            'activeColumn' => $model->getColumn($columnId),
            'isDelete' => TRUE,
            'submitURL' => base_url("$this->thisURL/dodelete/$columnId"),
            'abortURL' => base_url("$this->thisURL/board/$boardId")
        ];
        echo view('templates/head', ['title' => 'Spalte bearbeiten']);
        echo view('templates/menu', ['activeIndex' => 2]);
        echo view('templates/column_create', $dataCreate);
        echo view('templates/footer');
    }

    public function postDoCreate(int $boardId): RedirectResponse
    {
        $validation = Services::validation();
        $validation->setRuleGroup('columnCreateAndEdit');
        if (!$validation->withRequest($this->request)->run())
            return redirect()->back()->withInput();

        $validData = $validation->getValidated();
        $column = new Column();
        $column->boradId = $boardId;
        $column->name = $validData['column'];
        $column->description = $validData['description'] ?? '';
        if (isset($validData['sortid']))
            $column->sortId = (int)$validData['sortid'];

        $model = new TasksModel();
        $model->insertColumn($column);
        return redirect()->to(base_url("$this->thisURL/board/$boardId"));
    }

    public function postDoEdit(int $columnId): RedirectResponse
    {
        $model = new TasksModel();
        $board = $model->getBoardFromColumn($columnId);
        $boardId = $board->id;

        $validation = Services::validation();
        $validation->setRuleGroup('columnCreateAndEdit');
        if (!$validation->withRequest($this->request)->run())
            return redirect()->back()->withInput();

        $validData = $validation->getValidated();

        $column = $model->getColumn($columnId);
        $column->boradId = $boardId;
        $column->name = $validData['column'];
        $column->description = $validData['description'] ?? '';
        if (isset($validData['sortid']))
            $column->sortId = (int)$validData['sortid'];

        $model->editColumn($column);
        return redirect()->to(base_url("$this->thisURL/board/$boardId"));
    }

    public function postDoDelete(int $columnId): RedirectResponse
    {
        $model = new TasksModel();
        $board = $model->getBoardFromColumn($columnId);
        $boardId = $board->id;

        $model->removeColumn($columnId);
        return redirect()->to(base_url("$this->thisURL/board/$boardId"));
    }
}
