<?php

namespace App\Controllers;
use App\DatabaseObjects\Column;
use App\Models\TasksModel;
use CodeIgniter\HTTP\RedirectResponse;
use Config\Services;

class Columns extends BaseController
{

    private string $thisURL = 'columns';

    public function getIndex(): RedirectResponse | null
    {
        $model = new TasksModel();

        $firstBoard = $model->getFirstBoard();
        if (!$firstBoard)
        {
            echo view('templates/head', ['title' => 'Keine Boards']);
            echo view('templates/menu', ['activeIndex' => 2]);
            echo view('templates/no_boards');
            echo view('templates/footer');
            return null;
        }

        $session = session();
        $firstBoardId = $session->get('last_brd') ?? $firstBoard->id;
        $session->close();
        return redirect()->to(base_url("$this->thisURL/board/$firstBoardId"));
    }

    public function getBoard(int $boardId): RedirectResponse | null
    {
        $model = new TasksModel();
        $activeBoard = $model->getBoard($boardId);
        $session = session();
        if (!$activeBoard) {
            $session->remove('last_brd');
            $session->close();
            return redirect()->to(base_url($this->thisURL));
        }
        $session->set('jump_back_url', "$this->thisURL/board/$boardId");
        $session->set('last_brd', $boardId);
        $session->close();

        $data = [
            'columns' => $model->getDisplayColsFromBoard($boardId),
            'boards' => $model->getAllBoards(),
            'activeBoard' => $activeBoard,
            'boardsURL' => base_url("$this->thisURL/board"),
            'tasksURL' => base_url('tasks/board'),
            'columnsURL' => base_url("$this->thisURL/board"),
            'columnCreateURL' => base_url("$this->thisURL/create/$boardId"),
            'columnEditURL' => base_url("$this->thisURL/edit"),
            'columnDeleteURL' => base_url("$this->thisURL/delete"),
            'boardCreateURL' => base_url('boards/create'),
            'columnMoveURL' => base_url("$this->thisURL/domove")
        ];

        echo view('templates/head', ['title' => $activeBoard->name]);
        echo view('templates/menu', ['activeIndex' => 2]);
        echo view('templates/column_list', $data);
        echo view('templates/footer');
        return null;
    }

    public function getCreate(int $boardId): void
    {
        helper('form');

        $session = session();
        $dataCreate = [
            'submitURL' => base_url("$this->thisURL/docreate/$boardId"),
            'abortURL' => base_url($session->get('jump_back_url') ?? "$this->thisURL/board/$boardId"),
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
            'abortURL' => base_url($session->get('jump_back_url') ?? "$this->thisURL/board/$boardId"),
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
            'abortURL' => base_url($session->get('jump_back_url') ?? "$this->thisURL/board/$boardId")
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

        $model = new TasksModel();
        $model->insertColumn($column);
        $session = session();
        return redirect()->to(base_url($session->get('jump_back_url') ?? "$this->thisURL/board/$boardId"));
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

        $model->editColumn($column);
        $session = session();
        return redirect()->to(base_url($session->get('jump_back_url') ?? "$this->thisURL/board/$boardId"));
    }

    public function postDoDelete(int $columnId): RedirectResponse
    {
        $model = new TasksModel();
        $board = $model->getBoardFromColumn($columnId);
        $boardId = $board->id;

        $model->removeColumn($columnId);
        $session = session();
        return redirect()->to(base_url($session->get('jump_back_url') ?? "$this->thisURL/board/$boardId"));
    }

    public function postDoMove(): void
    {
        $validation = Services::validation();
        $validation->setRuleGroup('columnMove');
        if (!$validation->withRequest($this->request)->run())
        {
            echo 'failure';
            return;
        }

        $validData = $validation->getValidated();
        $model = new TasksModel();
        if ($model->moveColumn((int)$validData['colid'], (int)$validData['siblingid'], (int)$validData['targetbrd']))
            echo 'success';
        else
            echo 'failure';
    }
}
