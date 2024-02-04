<?php

namespace App\Controllers;
use App\DatabaseObjects\Board;
use App\Models\TasksModel;
use CodeIgniter\HTTP\RedirectResponse;
use Config\Services;

class Boards extends BaseController
{
    private string $thisURL = 'boards';

    public function getIndex(): void
    {
        $model = new TasksModel();

        $data = [
            'boards' => $model->getAllBoards(),
            'createURL' => base_url("$this->thisURL/create"),
            'editURL' => base_url("$this->thisURL/edit"),
            'deleteURL' => base_url("$this->thisURL/delete")
        ];

        echo view('templates/head', ['title' => 'Boards']);
        echo view('templates/menu', ['activeIndex' => 1]);
        echo view('templates/board_list', $data);
        echo view('templates/footer');
    }

    public function getCreate(): void
    {
        helper('form');

        $dataCreate = [
            'submitURL' => base_url("$this->thisURL/docreate"),
            'abortURL' => base_url("$this->thisURL"),
            'errorMessages' => esc(validation_errors())
        ];
        $oldInput = session('_ci_old_input');
        if (isset($oldInput['post']))
            $dataCreate['oldPost'] = esc($oldInput['post']);

        echo view('templates/head', ['title' => 'Board erstellen']);
        echo view('templates/menu', ['activeIndex' => 2]);
        echo view('templates/board_create', $dataCreate);
        echo view('templates/footer');
    }

    public function getEdit(int $boardId): void
    {
        helper('form');

        $model = new TasksModel();
        $dataCreate = [
            'activeBoard' => $model->getBoard($boardId),
            'submitURL' => base_url("$this->thisURL/doedit/$boardId"),
            'abortURL' => base_url("$this->thisURL"),
            'errorMessages' => esc(validation_errors())
        ];
        $oldInput = session('_ci_old_input');
        if (isset($oldInput['post']))
            $dataCreate['oldPost'] = esc($oldInput['post']);

        echo view('templates/head', ['title' => 'Board bearbeiten']);
        echo view('templates/menu', ['activeIndex' => 2]);
        echo view('templates/board_create', $dataCreate);
        echo view('templates/footer');
    }

    public function getDelete(int $boardId): void
    {
        $model = new TasksModel();
        $dataCreate = [
            'activeBoard' => $model->getBoard($boardId),
            'isDelete' => TRUE,
            'submitURL' => base_url("$this->thisURL/dodelete/$boardId"),
            'abortURL' => base_url("$this->thisURL")
        ];
        echo view('templates/head', ['title' => 'Board löschen']);
        echo view('templates/menu', ['activeIndex' => 2]);
        echo view('templates/board_create', $dataCreate);
        echo view('templates/footer');
    }

    public function postDoCreate(): RedirectResponse
    {
        $validation = Services::validation();
        $validation->setRuleGroup('boardCreateAndEdit');
        if (!$validation->withRequest($this->request)->run())
            return redirect()->back()->withInput();

        $validData = $validation->getValidated();
        $board = new Board();
        $board->name = $validData['board'];

        $model = new TasksModel();
        $model->insertBoard($board);
        return redirect()->to(base_url("$this->thisURL"));
    }

    public function postDoEdit(int $boardId): RedirectResponse
    {
        $model = new TasksModel();

        $validation = Services::validation();
        $validation->setRuleGroup('boardCreateAndEdit');
        if (!$validation->withRequest($this->request)->run())
            return redirect()->back()->withInput();

        $validData = $validation->getValidated();
        $board = $model->getBoard($boardId);
        $board->name = $validData['board'];

        $model->editBoard($board);
        return redirect()->to(base_url("$this->thisURL"));
    }

    public function postDoDelete(int $boardId): RedirectResponse
    {
        $model = new TasksModel();

        $model->removeBoard($boardId);
        return redirect()->to(base_url("$this->thisURL"));
    }

}
