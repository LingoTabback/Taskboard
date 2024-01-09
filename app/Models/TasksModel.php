<?php
namespace App\Models;
use CodeIgniter\Model;

class TasksModel extends Model
{
    public function getTasksFromBoard(int $boardId) : array
    {
        return $this->db->query("
                SELECT t.*, s.spalte, p.name, p.vorname, ta.taskart
                FROM tasks t JOIN spalten s ON t.spaltenid = s.id
                JOIN personen p ON t.personenid = p.id
                JOIN taskarten ta ON t.taskartenid = ta.id
                WHERE s.boardsid = $boardId
                ORDER BY t.tasks")
            ->getResultArray();
    }
}
