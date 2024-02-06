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

        $session = session();
        $session->set('jump_back_url', $this->thisURL);
        $session->close();

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

        $session = session();
        $dataCreate = [
            'submitURL' => base_url("$this->thisURL/docreate"),
            'abortURL' => base_url($session->has('jump_back_url') ? $session->get('jump_back_url') : $this->thisURL),
            'errorMessages' => esc(validation_errors())
        ];
        $oldInput = $session->get('_ci_old_input');
        $session->close();
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
        $session = session();
        $dataCreate = [
            'activeBoard' => $model->getBoard($boardId),
            'submitURL' => base_url("$this->thisURL/doedit/$boardId"),
            'abortURL' => base_url($session->has('jump_back_url') ? $session->get('jump_back_url') : $this->thisURL),
            'errorMessages' => esc(validation_errors())
        ];
        $oldInput = $session->get('_ci_old_input');
        $session->close();
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
        $session = session();
        $dataCreate = [
            'activeBoard' => $model->getBoard($boardId),
            'isDelete' => TRUE,
            'submitURL' => base_url("$this->thisURL/dodelete/$boardId"),
            'abortURL' => base_url($session->has('jump_back_url') ? $session->get('jump_back_url') : $this->thisURL)
        ];
        $session->close();
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
        $session = session();
        return redirect()->to(base_url($session->has('jump_back_url') ? $session->get('jump_back_url') : $this->thisURL));
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
        $session = session();
        return redirect()->to(base_url($session->has('jump_back_url') ? $session->get('jump_back_url') : $this->thisURL));
    }

    public function postDoDelete(int $boardId): RedirectResponse
    {
        $model = new TasksModel();

        $model->removeBoard($boardId);
        $session = session();
        return redirect()->to(base_url($session->has('jump_back_url') ? $session->get('jump_back_url') : $this->thisURL));
    }

}
