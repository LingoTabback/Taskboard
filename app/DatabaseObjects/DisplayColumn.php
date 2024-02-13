<?php
namespace App\DatabaseObjects;

use DateTime;

class DisplayColumn extends Column
{
    public int $numTasks = 0;

    public function __set(string $name, mixed $value): void
    {
        switch ($name)
        {
            case 'numtasks':
                $this->numTasks = (int)$value;
                break;
            default:
                parent::__set($name, $value);
                break;
        }
    }

    public static function fromArray(array $a): DisplayColumn
    {
        $result = new DisplayColumn();
        foreach ($a as $key => $value)
            $result->{$key} = $value;
        return $result;
    }
}
