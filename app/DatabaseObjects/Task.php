<?php
namespace App\DatabaseObjects;

use DateTime;

class Task
{
    public int $id = 0;
    public int $userId = 0;
    public int $typeId = 0;
    public int $columnId = 0;
    public int $sortId = 0;
    public string $task = '';
    public string $notes = '';
    public bool $useReminder = FALSE;
    public bool $isDone = FALSE;
    public bool $isDeleted = FALSE;
    public DateTime $createDate;
    public DateTime $remindDate;

    public function __set(string $name, mixed $value): void
    {
        switch ($name)
        {
            case 'personenid':
                $this->userId = (int)$value;
                break;
            case 'taskartenid':
                $this->typeId = (int)$value;
                break;
            case 'spaltenid':
                $this->columnId = (int)$value;
                break;
            case 'sortid':
                $this->sortId = (int)$value;
                break;
            case 'tasks':
                $this->task = (string)$value;
                break;
            case 'notizen':
                $this->notes = (string)$value;
                break;
            case 'erinnerung':
                $this->useReminder = (bool)$value;
                break;
            case 'erledigt':
                $this->isDone = (bool)$value;
                break;
            case 'geloescht':
                $this->isDeleted = (bool)$value;
                break;
            case 'erstelldatum':
                $this->createDate = DateTime::createFromFormat('Y-m-d', (string)$value);
                break;
            case 'erinnerungsdatum':
                $this->remindDate = DateTime::createFromFormat('Y-m-d H:i:s', (string)$value);
                break;
        }
    }

    public static function fromArray(array $a): Task
    {
        $result = new Task();
        foreach ($a as $key => $value)
            $result->{$key} = $value;
        return $result;
    }
}
