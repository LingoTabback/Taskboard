<?php

namespace App\Cells;

class InvalidFeedback
{
    public static function render(array $errors, string $name): string
    {
        if (!isset($errors) || !isset($errors[$name]))
            return '';

        return "<div class=\"invalid-feedback\">$errors[$name]</div>";
    }
}
