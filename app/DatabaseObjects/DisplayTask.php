<?php
namespace App\DatabaseObjects;

use DateTime;

class DisplayTask extends Task
{
    public string $columnName = '';
    public string $taskTypeName = '';
    public string $userFirstName = '';
    public string $userLastName = '';

    public function __set(string $name, mixed $value): void
    {
        switch ($name)
        {
            case 'spalte':
                $this->columnName = (string)$value;
                break;
            case 'vorname':
                $this->userFirstName = (string)$value;
                break;
            case 'name':
                $this->userLastName = (string)$value;
                break;
            case 'taskart':
                $this->taskTypeName = (string)$value;
                break;
            default:
                parent::__set($name, $value);
                break;
        }
    }

    public static function fromArray(array $a): DisplayTask
    {
        $result = new DisplayTask();
        foreach ($a as $key => $value)
            $result->{$key} = $value;
        return $result;
    }
}
