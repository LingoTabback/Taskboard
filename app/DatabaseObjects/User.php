<?php
namespace App\DatabaseObjects;

class User
{
    public int $id = 0;
    public string $firstName = '';
    public string $lastName = '';
    public string $email = '';
    public string $password = '';

    public function __set(string $name, mixed $value): void
    {
        switch ($name)
        {
            case 'vorname':
                $this->firstName = (string)$value;
                break;
            case 'name':
                $this->lastName = (string)$value;
                break;
            case 'passwort':
                $this->password = (string)$value;
                break;
        }
    }

    public static function fromArray(array $a): User
    {
        $result = new User();
        foreach ($a as $key => $value)
            $result->{$key} = $value;
        return $result;
    }
}
