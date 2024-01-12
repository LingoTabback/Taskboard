<?php
namespace App\DatabaseObjects;

class Column
{
    public int $id = 0;
    public int $boradId = 0;
    public int $sortId = 0;
    public string $name = '';
    public string $description = '';

    public function __set(string $name, mixed $value): void
    {
        switch ($name)
        {
            case 'boardsid':
                $this->boradId = (int)$value;
                break;
            case 'sortid':
                $this->sortId = (int)$value;
                break;
            case 'spalte':
                $this->name = (string)$value;
                break;
            case 'spaltenbeschreibung':
                $this->description = (string)$value;
                break;
        }
    }

    public static function fromArray(array $a): Column
    {
        $result = new Column();
        foreach ($a as $key => $value)
            $result->{$key} = $value;
        return $result;
    }
}
