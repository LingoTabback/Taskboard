<?php
namespace App\DatabaseObjects;

class DisplayBoard extends Board
{
    public int $numColumns = 0;
    public int $numTasks = 0;

    public function __set(string $name, mixed $value): void
    {
        switch ($name)
        {
            case 'numcols':
                $this->numColumns = (int)$value;
                break;
            case 'numtasks':
                $this->numTasks = (int)$value;
                break;
            default:
                parent::__set($name, $value);
                break;
        }
    }

    public static function fromArray(array $a): DisplayBoard
    {
        $result = new DisplayBoard();
        foreach ($a as $key => $value)
            $result->{$key} = $value;
        return $result;
    }
}
