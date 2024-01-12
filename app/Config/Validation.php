<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Validation\StrictRules\CreditCardRules;
use CodeIgniter\Validation\StrictRules\FileRules;
use CodeIgniter\Validation\StrictRules\FormatRules;
use CodeIgniter\Validation\StrictRules\Rules;

class Validation extends BaseConfig
{
    // --------------------------------------------------------------------
    // Setup
    // --------------------------------------------------------------------

    /**
     * Stores the classes that contain the
     * rules that are available.
     *
     * @var string[]
     */
    public array $ruleSets = [
        Rules::class,
        FormatRules::class,
        FileRules::class,
        CreditCardRules::class,
    ];

    /**
     * Specifies the views that are used to display the
     * errors.
     *
     * @var array<string, string>
     */
    public array $templates = [
        'list'   => 'CodeIgniter\Validation\Views\list',
        'single' => 'CodeIgniter\Validation\Views\single',
    ];

    // --------------------------------------------------------------------
    // Rules
    // --------------------------------------------------------------------

    public array $taskCreate = [
        'task' => 'required|string|min_length[1]|max_length[255]',
        'columnid' => 'required|is_natural_no_zero',
        'personid' => 'required|is_natural_no_zero',
        'reminderdate' => 'required|valid_date[d.m.Y]',
        'remindertime' => 'required|valid_date[H:i]',
        'reminderuse' => 'permit_empty|in_list[0,1]',
        'notes' => 'permit_empty|string'
    ];

    public array $taskEdit = [
        'task' => 'required|string|min_length[1]|max_length[255]',
        'columnid' => 'required|is_natural_no_zero',
        'personid' => 'required|is_natural_no_zero',
        'reminderdate' => 'required|valid_date[d.m.Y]',
        'remindertime' => 'required|valid_date[H:i]',
        'reminderuse' => 'permit_empty|in_list[0,1]',
        'notes' => 'permit_empty|string'
    ];

    public array $taskDelete = [
    ];
}
