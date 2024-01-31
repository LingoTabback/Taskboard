<?php
namespace App\Models;
use App\DatabaseObjects\Board;
use App\DatabaseObjects\Column;
use App\DatabaseObjects\DisplayColumn;
use App\DatabaseObjects\DisplayTask;
use App\DatabaseObjects\Task;
use App\DatabaseObjects\TaskType;
use App\DatabaseObjects\User;
use CodeIgniter\Database\Exceptions\DatabaseException;
use CodeIgniter\Model;

class TasksModel extends Model
{
    /**
     * @return Task[]
     */
    public function getTasksFromBoard(int $boardId): array
    {
        return $this->db->query("
                SELECT t.*
                FROM tasks t JOIN spalten s ON t.spaltenid = s.id
                WHERE s.boardsid = $boardId")
            ->getCustomResultObject(Task::class);
    }

    /**
     * @return DisplayTask[]
     */
    public function getDisplayTasksFromBoard(int $boardId): array
    {
        return $this->db->query("
                SELECT t.*, s.spalte, p.name, p.vorname, ta.taskart
                FROM tasks t JOIN spalten s ON t.spaltenid = s.id
                JOIN personen p ON t.personenid = p.id
                JOIN taskarten ta ON t.taskartenid = ta.id
                WHERE s.boardsid = $boardId
                ORDER BY t.tasks")
            ->getCustomResultObject(DisplayTask::class);
    }

    /**
     * @return Board[]
     */
    public function getAllBoards(): array
    {
        return $this->db->query('SELECT * FROM boards')->getCustomResultObject(Board::class);
    }

    public function getBoard(int $boardId): Board | null
    {
        $result = $this->db->query("SELECT * FROM boards WHERE id = $boardId")->getRowArray(0);
        return $result ? Board::fromArray($result) : null;
    }

    public function getBoardFromTask(int $taskId): Board | null
    {
        $result = $this->db->query("
                SELECT b.*
                FROM (SELECT spaltenid FROM tasks WHERE id = $taskId) AS t
                JOIN spalten s ON s.id = t.spaltenid
                JOIN boards b ON b.id = s.boardsid")->getRowArray(0);
        return $result ? Board::fromArray($result) : null;
    }

    public function getBoardFromColumn(int $columnId): Board | null
    {
        $result = $this->db->query("
                SELECT b.* FROM spalten s JOIN boards b on b.id = s.boardsid WHERE s.id = $columnId")->getRowArray(0);
        return $result ? Board::fromArray($result) : null;
    }

    public function getColumn(int $columnId): Column | null
    {
        $result = $this->db->query("SELECT * FROM spalten WHERE id = $columnId")->getRowArray(0);
        return $result ? Column::fromArray($result) : null;
    }

    /**
     * @return Column[]
     */
    public function getColsFromBoard(int $boardId): array
    {
        return $this->db->query("SELECT * FROM spalten WHERE boardsid = $boardId")->getCustomResultObject(Column::class);
    }

    /**
     * @return DisplayColumn[]
     */
    public function getDisplayColsFromBoard(int $boardId): array
    {
        return $this->db->query("SELECT c.*, b.board FROM spalten c JOIN boards b on b.id = c.boardsid WHERE boardsid = $boardId")->getCustomResultObject(DisplayColumn::class);
    }

    /**
     * @return User[]
     */
    public function getAllUsers(): array
    {
        return $this->db->query("SELECT * FROM personen")->getCustomResultObject(User::class);
    }

    public function getTask(int $taskId): Task | null
    {
        $result = $this->db->query("SELECT * FROM tasks WHERE id = $taskId")->getRowArray(0);
        return $result ? Task::fromArray($result) : null;
    }

    public function insertTask(Task $task): bool
    {
        $creationDateString = $task->createDate->format('Y-m-d');
        $reminderDateString = $task->remindDate->format('Y-m-d H:i:s');
        $useReminder = (int)$task->useReminder;
        $isDone = (int)$task->isDone;
        $isDeleted = (int)$task->isDeleted;
        return $this->db->query("
                INSERT INTO tasks (id, personenid, taskartenid, spaltenid, sortid, tasks, erstelldatum, erinnerungsdatum, erinnerung, notizen, erledigt, geloescht)
                VALUES (NULL,
                        $task->userId,
                        $task->typeId,
                        $task->columnId,
                        $task->sortId,
                        '$task->task',
                        '$creationDateString',
                        '$reminderDateString',
                        $useReminder,
                        '$task->notes',
                        $isDone,
                        $isDeleted)");
    }

    public function editTask(Task $task): bool
    {
        $reminderDateString = $task->remindDate->format('Y-m-d H:i:s');
        $useReminder = (int)$task->useReminder;
        $isDone = (int)$task->isDone;
        $isDeleted = (int)$task->isDeleted;

        return $this->db->query("
                UPDATE tasks
                SET personenid = $task->userId,
                    taskartenid = $task->typeId,
                    spaltenid = $task->columnId,
                    sortid = $task->sortId,
                    tasks = '$task->task',
                    notizen = '$task->notes',
                    erinnerungsdatum = '$reminderDateString',
                    erinnerung = $useReminder,
                    erledigt = $isDone,
                    geloescht = $isDeleted
                WHERE id = $task->id");
    }

    public function removeTask(int $taskId): bool
    {
        return $this->db->query("DELETE FROM tasks WHERE id = $taskId");
    }

    public function insertColumn(Column $column): bool
    {
        return $this->db->query("
                INSERT INTO spalten (boardsid, sortid, spalte, spaltenbeschreibung)
                VALUES ($column->boradId, $column->sortId,' $column->name', '$column->description')");
    }

    public function editColumn(Column $column): bool
    {
        return $this->db->query("
                UPDATE spalten
                SET sortid = $column->sortId,
                    spalte = '$column->name',
                    spaltenbeschreibung = '$column->description'
                WHERE id = $column->id");
    }

    public function removeColumn(int $columnId): bool
    {
        try
        {
            return $this->db->query("DELETE FROM spalten WHERE id = $columnId");
        } catch (DatabaseException $e)
        {
            return FALSE;
        }
    }
}
