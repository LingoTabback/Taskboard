<?php
namespace App\Models;
use App\DatabaseObjects\Board;
use App\DatabaseObjects\Column;
use App\DatabaseObjects\DisplayBoard;
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
        return $this->db->query('
                SELECT t.*
                FROM tasks t JOIN spalten s ON t.spaltenid = s.id AND s.boardsid = ? AND t.geloescht = 0 AND s.geloescht = 0', [$boardId])
            ->getCustomResultObject(Task::class);
    }

    /**
     * @return DisplayTask[]
     */
    public function getDisplayTasksFromBoard(int $boardId): array
    {
        return $this->db->query('
                SELECT t.*, s.spalte, p.name, p.vorname, ta.taskart, ta.taskartenicon
                FROM tasks t JOIN spalten s ON t.spaltenid = s.id
                JOIN personen p ON t.personenid = p.id
                JOIN taskarten ta ON t.taskartenid = ta.id
                WHERE s.boardsid = ? AND t.geloescht = 0 AND s.geloescht = 0
                ORDER BY t.sortid', [$boardId])
            ->getCustomResultObject(DisplayTask::class);
    }

    /**
     * @return TaskType[]
     */
    public function getAllTaskTypes(): array
    {
        return $this->db->query('SELECT * FROM taskarten ORDER BY taskart')->getCustomResultObject(TaskType::class);
    }

    /**
     * @return Board[]
     */
    public function getAllBoards(): array
    {
        return $this->db->query('SELECT * FROM boards WHERE geloescht = 0 ORDER BY board')->getCustomResultObject(Board::class);
    }

    public function getFirstBoard(): Board | null
    {
        $result = $this->db->query('SELECT * FROM boards WHERE geloescht = 0 ORDER BY board LIMIT 1')->getRowArray(0);
        return $result ? Board::fromArray($result) : null;
    }

    public function getBoard(int $boardId): Board | null
    {
        $result = $this->db->query('SELECT * FROM boards WHERE id = ? AND geloescht = 0', [$boardId])->getRowArray(0);
        return $result ? Board::fromArray($result) : null;
    }

    public function getBoardFromTask(int $taskId): Board | null
    {
        $result = $this->db->query('
                SELECT b.*
                FROM (SELECT spaltenid FROM tasks WHERE id = ? AND geloescht = 0) AS t
                JOIN spalten s ON s.id = t.spaltenid AND s.geloescht = 0
                JOIN boards b ON b.id = s.boardsid AND b.geloescht = 0', [$taskId])->getRowArray(0);
        return $result ? Board::fromArray($result) : null;
    }

    public function getBoardFromColumn(int $columnId): Board | null
    {
        $result = $this->db->query('
                SELECT b.* FROM spalten s JOIN boards b on s.id = ? AND b.id = s.boardsid AND s.geloescht = 0 AND b.geloescht = 0', [$columnId])->getRowArray(0);
        return $result ? Board::fromArray($result) : null;
    }

    public function checkBoard(int $boardId): bool
    {
        return $this->db->query('SELECT EXISTS(SELECT 1 FROM boards WHERE id = ? AND geloescht = 0) AS res', [$boardId])->getRowArray(0)['res'] === '1';
    }

    /**
     * @return DisplayBoard[]
     */
    public function getAllDisplayBoards(): array
    {
        return $this->db->query('
                SELECT b.*, COALESCE(nc.numcols, 0) AS numcols, COALESCE(nc.numtasks, 0) AS numtasks
                FROM
                    (SELECT * FROM boards WHERE geloescht = 0) b
                        LEFT JOIN (
                        SELECT DISTINCT c.boardsid, COUNT(c.boardsid) OVER(PARTITION BY c.boardsid) AS numcols, SUM(c.numtasks) OVER(PARTITION BY c.boardsid) AS numtasks
                        FROM (SELECT DISTINCT s.id, s.boardsid, COUNT(t.id) OVER(PARTITION BY s.id) AS numtasks FROM spalten s LEFT JOIN tasks t ON s.id = t.spaltenid AND t.geloescht = 0
                              WHERE s.geloescht = 0) c
    ) nc ON b.id = nc.boardsid ORDER BY b.board')
            ->getCustomResultObject(DisplayBoard::class);
    }

    public function getColumn(int $columnId): Column | null
    {
        $result = $this->db->query('SELECT * FROM spalten WHERE id = ? AND geloescht = 0', [$columnId])->getRowArray(0);
        return $result ? Column::fromArray($result) : null;
    }

    /**
     * @return Column[]
     */
    public function getColsFromBoard(int $boardId): array
    {
        return $this->db->query('SELECT * FROM spalten WHERE boardsid = ? AND geloescht = 0 ORDER BY sortid', [$boardId])
            ->getCustomResultObject(Column::class);
    }

    /**
     * @return DisplayColumn[]
     */
    public function getDisplayColsFromBoard(int $boardId): array
    {
        return $this->db->query('
                SELECT DISTINCT s.*, COUNT(t.id) OVER(PARTITION BY s.id) AS numtasks
                FROM spalten s LEFT JOIN tasks t ON s.id = t.spaltenid AND t.geloescht = 0 WHERE boardsid = ? AND s.geloescht = 0 ORDER BY s.sortid', [$boardId])
            ->getCustomResultObject(DisplayColumn::class);
    }

    /**
     * @return User[]
     */
    public function getAllUsers(): array
    {
        return $this->db->query('SELECT * FROM personen ORDER BY name, vorname')->getCustomResultObject(User::class);
    }

    public function getTask(int $taskId): Task | null
    {
        $result = $this->db->query('SELECT * FROM tasks WHERE id = ? AND geloescht = 0', [$taskId])->getRowArray(0);
        return $result ? Task::fromArray($result) : null;
    }

    public function insertTask(Task $task): bool
    {
        $creationDateString = $task->createDate->format('Y-m-d');
        $reminderDateString = $task->remindDate->format('Y-m-d H:i:s');
        $useReminder = (int)$task->useReminder;

        try
        {
            $this->db->query('CALL create_task(?, ?, ?, ?, ?, ?, ?, ?)',
                [$task->userId, $task->typeId, $task->columnId, $task->task, $creationDateString, $reminderDateString, $useReminder, $task->notes]);
            return TRUE;
        } catch (DatabaseException)
        {
            return FALSE;
        }
    }

    public function moveTask(int $taskId, int $siblingId, int $targetColId): bool
    {
        try
        {
            $this->db->query('CALL move_task(?, ?, ?)', [$taskId, $siblingId, $targetColId]);
            return TRUE;
        } catch (DatabaseException)
        {
            return FALSE;
        }
    }

    public function editTask(Task $task): bool
    {
        $reminderDateString = $task->remindDate->format('Y-m-d H:i:s');
        $useReminder = (int)$task->useReminder;
        $isDone = (int)$task->isDone;

        return $this->db->query('
                UPDATE tasks
                SET personenid = ?,
                    taskartenid = ?,
                    spaltenid = ?,
                    tasks = ?,
                    notizen = ?,
                    erinnerungsdatum = ?,
                    erinnerung = ?,
                    erledigt = ?
                WHERE id = ? AND geloescht = 0',
            [$task->userId, $task->typeId, $task->columnId, $task->task, $task->notes, $reminderDateString, $useReminder, $isDone, $task->id]);
    }

    public function removeTask(int $taskId): bool
    {
        return $this->db->query('UPDATE tasks SET geloescht = TRUE WHERE id = ?', [$taskId]);
    }

    public function insertColumn(Column $column): bool
    {
        try {
            $this->db->query('CALL create_col(?, ?, ?)', [$column->boradId, $column->name, $column->description]);
            return TRUE;
        } catch (DatabaseException)
        {
            return FALSE;
        }
    }

    public function moveColumn(int $columnId, int $siblingId, int $targetBordId): bool
    {
        try
        {
            $this->db->query('CALL move_col(?, ?, ?)', [$columnId, $siblingId, $targetBordId]);
            return TRUE;
        } catch (DatabaseException)
        {
            return FALSE;
        }
    }

    public function editColumn(Column $column): bool
    {
        return $this->db->query('UPDATE spalten SET sortid = ?, spalte = ?, spaltenbeschreibung = ? WHERE id = ? AND geloescht = 0',
            [$column->sortId, $column->name, $column->description, $column->id]);
    }

    public function removeColumn(int $columnId): bool
    {
        try {
            return $this->db->query('UPDATE spalten SET geloescht = TRUE WHERE id = ?', [$columnId]);
        } catch (DatabaseException) {
            return FALSE;
        }
    }

    public function insertBoard(Board $board): bool
    {
        return $this->db->query('INSERT INTO boards (board) VALUES (?)', [$board->name]);
    }

    public function editBoard(Board $board): bool
    {
        return $this->db->query('UPDATE boards SET board = ? WHERE id = ? AND geloescht = 0', [$board->name, $board->id]);
    }

    public function removeBoard(int $boardId): bool
    {
        try {
            return $this->db->query('UPDATE boards SET geloescht = TRUE WHERE id = ?', [$boardId]);
        } catch (DatabaseException) {
            return FALSE;
        }
    }
}
