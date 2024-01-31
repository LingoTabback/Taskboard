<?php
namespace App\DatabaseObjects;

use DateTime;

class DisplayColumn extends Column
{
    public string $boardName = '';

    public function __set(string $name, mixed $value): void
    {
        switch ($name)
        {
            case 'board':
                $this->boardName = (string)$value;
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
