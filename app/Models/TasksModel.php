<?php
namespace App\Models;
use CodeIgniter\Model;

class TasksModel extends Model
{
    public function getTasksFromBoard(int $boardId) : array
    {
        return $this->db->query("SELECT t.* FROM tasks t JOIN spalten s ON t.spaltenid = s.id WHERE s.boardsid = $boardId")->getResultArray();
    }
}
