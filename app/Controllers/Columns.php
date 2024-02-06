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

        $session = session();
        $session->set('jump_back_url', "$this->thisURL/board/$boardId");
        $session->close();

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

        $session = session();
        $dataCreate = [
            'submitURL' => base_url("$this->thisURL/docreate/$boardId"),
            'abortURL' => base_url($session->has('jump_back_url') ? $session->get('jump_back_url') : "$this->thisURL/board/$boardId"),
            'errorMessages' => esc(validation_errors())
        ];
        $oldInput = $session->get('_ci_old_input');
        $session->close();
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
        $session = session();
        $dataCreate = [
            'activeColumn' => $model->getColumn($columnId),
            'submitURL' => base_url("$this->thisURL/doedit/$columnId"),
            'abortURL' => base_url($session->has('jump_back_url') ? $session->get('jump_back_url') : "$this->thisURL/board/$boardId"),
            'errorMessages' => esc(validation_errors())
        ];
        $oldInput = $session->get('_ci_old_input');
        $session->close();
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
        $session = session();
        $dataCreate = [
            'activeColumn' => $model->getColumn($columnId),
            'isDelete' => TRUE,
            'submitURL' => base_url("$this->thisURL/dodelete/$columnId"),
            'abortURL' => base_url($session->has('jump_back_url') ? $session->get('jump_back_url') : "$this->thisURL/board/$boardId")
        ];
        $session->close();
        echo view('templates/head', ['title' => 'Spalte löschen']);
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
        $session = session();
        return redirect()->to(base_url($session->has('jump_back_url') ? $session->get('jump_back_url') : "$this->thisURL/board/$boardId"));
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
        $session = session();
        return redirect()->to(base_url($session->has('jump_back_url') ? $session->get('jump_back_url') : "$this->thisURL/board/$boardId"));
    }

    public function postDoDelete(int $columnId): RedirectResponse
    {
        $model = new TasksModel();
        $board = $model->getBoardFromColumn($columnId);
        $boardId = $board->id;

        $model->removeColumn($columnId);
        $session = session();
        return redirect()->to(base_url($session->has('jump_back_url') ? $session->get('jump_back_url') : "$this->thisURL/board/$boardId"));
    }
}
