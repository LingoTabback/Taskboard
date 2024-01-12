<?php
namespace App\DatabaseObjects;

class TaskType
{
    public int $id = 0;
    public string $name = '';
    public string $icon = '';

    public function __set(string $name, mixed $value): void
    {
        switch ($name)
        {
            case 'taskart':
                $this->name = (string)$value;
                break;
            case 'taskartenicon':
                $this->icon = (string)$value;
                break;
        }
    }

    public static function fromArray(array $a): TaskType
    {
        $result = new TaskType();
        foreach ($a as $key => $value)
            $result->{$key} = $value;
        return $result;
    }
}
