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
                FROM tasks t JOIN spalten s ON t.spaltenid = s.id
                WHERE s.boardsid = ?', [$boardId])
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
                WHERE s.boardsid = ?
                ORDER BY t.sortid', [$boardId])
            ->getCustomResultObject(DisplayTask::class);
    }

    /**
     * @return TaskType[]
     */
    public function getAllTaskTypes(): array
    {
        return $this->db->query('SELECT * FROM taskarten')->getCustomResultObject(TaskType::class);
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
        $result = $this->db->query('SELECT * FROM boards WHERE id = ?', [$boardId])->getRowArray(0);
        return $result ? Board::fromArray($result) : null;
    }

    public function getBoardFromTask(int $taskId): Board | null
    {
        $result = $this->db->query('
                SELECT b.*
                FROM (SELECT spaltenid FROM tasks WHERE id = ?) AS t
                JOIN spalten s ON s.id = t.spaltenid
                JOIN boards b ON b.id = s.boardsid', [$taskId])->getRowArray(0);
        return $result ? Board::fromArray($result) : null;
    }

    public function getBoardFromColumn(int $columnId): Board | null
    {
        $result = $this->db->query('
                SELECT b.* FROM spalten s JOIN boards b on b.id = s.boardsid WHERE s.id = ?', [$columnId])->getRowArray(0);
        return $result ? Board::fromArray($result) : null;
    }

    /**
     * @return DisplayBoard[]
     */
    public function getAllDisplayBoards(): array
    {
        return $this->db->query('
                SELECT b.*, COALESCE(nc.numcols, 0) AS numcols, COALESCE(nc.numtasks, 0) AS numtasks
                FROM
                    boards b
                    LEFT JOIN (
                        SELECT DISTINCT c.boardsid, COUNT(c.boardsid) OVER(PARTITION BY c.boardsid) AS numcols, SUM(c.numtasks) OVER(PARTITION BY c.boardsid) AS numtasks
                        FROM (SELECT DISTINCT s.id, s.boardsid, COUNT(t.id) OVER(PARTITION BY s.id) AS numtasks FROM spalten s LEFT JOIN tasks t ON s.id = t.spaltenid) c
                    ) nc ON b.id = nc.boardsid')
            ->getCustomResultObject(DisplayBoard::class);
    }

    public function getColumn(int $columnId): Column | null
    {
        $result = $this->db->query('SELECT * FROM spalten WHERE id = ?', [$columnId])->getRowArray(0);
        return $result ? Column::fromArray($result) : null;
    }

    /**
     * @return Column[]
     */
    public function getColsFromBoard(int $boardId): array
    {
        return $this->db->query('SELECT * FROM spalten WHERE boardsid = ?', [$boardId])
            ->getCustomResultObject(Column::class);
    }

    /**
     * @return DisplayColumn[]
     */
    public function getDisplayColsFromBoard(int $boardId): array
    {
        return $this->db->query('SELECT c.*, b.board FROM spalten c JOIN boards b on b.id = c.boardsid WHERE boardsid = ?', [$boardId])
            ->getCustomResultObject(DisplayColumn::class);
    }

    /**
     * @return User[]
     */
    public function getAllUsers(): array
    {
        return $this->db->query('SELECT * FROM personen')->getCustomResultObject(User::class);
    }

    public function getTask(int $taskId): Task | null
    {
        $result = $this->db->query('SELECT * FROM tasks WHERE id = ?', [$taskId])->getRowArray(0);
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
        } catch (DatabaseException $e)
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
        } catch (DatabaseException $e)
        {
            return FALSE;
        }
    }

    public function editTask(Task $task): bool
    {
        $reminderDateString = $task->remindDate->format('Y-m-d H:i:s');
        $useReminder = (int)$task->useReminder;
        $isDone = (int)$task->isDone;
        $isDeleted = (int)$task->isDeleted;

        return $this->db->query('
                UPDATE tasks
                SET personenid = ?,
                    taskartenid = ?,
                    spaltenid = ?,
                    tasks = ?,
                    notizen = ?,
                    erinnerungsdatum = ?,
                    erinnerung = ?,
                    erledigt = ?,
                    geloescht = ?
                WHERE id = ?',
            [$task->userId, $task->typeId, $task->columnId, $task->task, $task->notes, $reminderDateString, $useReminder, $isDone, $isDeleted, $task->id]);
    }

    public function removeTask(int $taskId): bool
    {
        return $this->db->query('DELETE FROM tasks WHERE id = ?', [$taskId]);
    }

    public function insertColumn(Column $column): bool
    {
        return $this->db->query('INSERT INTO spalten (boardsid, sortid, spalte, spaltenbeschreibung) VALUES (?, ?, ?, ?)',
            [$column->boradId, $column->sortId, $column->name, $column->description]);
    }

    public function editColumn(Column $column): bool
    {
        return $this->db->query('UPDATE spalten SET sortid = ?, spalte = ?, spaltenbeschreibung = ? WHERE id = ?',
            [$column->sortId, $column->name, $column->description, $column->id]);
    }

    public function removeColumn(int $columnId): bool
    {
        try {
            return $this->db->query('DELETE FROM spalten WHERE id = ?', [$columnId]);
        } catch (DatabaseException $e) {
            return FALSE;
        }
    }

    public function insertBoard(Board $board): bool
    {
        return $this->db->query('INSERT INTO boards (board) VALUES (?)', [$board->name]);
    }

    public function editBoard(Board $board): bool
    {
        return $this->db->query('UPDATE boards SET board = ? WHERE id = ?', [$board->name, $board->id]);
    }

    public function removeBoard(int $boardId): bool
    {
        try {
            return $this->db->query('DELETE FROM boards WHERE id = ?', [$boardId]);
        } catch (DatabaseException $e) {
            return FALSE;
        }
    }
}
