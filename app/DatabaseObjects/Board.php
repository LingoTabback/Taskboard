<?php
namespace App\DatabaseObjects;

class Board
{
    public int $id = 0;
    public string $name = '';

    public function __set(string $name, mixed $value): void
    {
        if ($name == 'board')
            $this->name = (string)$value;
    }

    public static function fromArray(array $a): Board
    {
        $result = new Board();
        foreach ($a as $key => $value)
            $result->{$key} = $value;
        return $result;
    }
}
