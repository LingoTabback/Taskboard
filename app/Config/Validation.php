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

    public array $taskCreateAndEdit = [
        'task' => 'required|max_length[255]',
        'typeid' => 'required|is_natural_no_zero',
        'columnid' => 'required|is_natural_no_zero',
        'personid' => 'required|is_natural_no_zero',
        'reminderdate' => 'required|valid_date[Y-m-d]',
        'remindertime' => 'required|valid_date[H:i]',
        'reminderuse' => 'permit_empty|in_list[0,1]',
        'notes' => 'permit_empty'
    ];

    public array $taskCreateAndEdit_errors = [
        'task' => [
            'required' => 'Der Task muss ausgefüllt sein.',
            'max_length[255]' => 'Der Task darf höchstens 255 Zeichen lang sein.'
        ],
        'typeid' => [
            'required' => 'Eine Taskart muss angegeben sein.',
            'is_natural_no_zero' => 'Unbekannte Taskart.',
        ],
        'columnid' => [
            'required' => 'Eine Spalte muss angegeben sein.',
            'is_natural_no_zero' => 'Unbekannte Spalte.',
        ],
        'personid' => [
            'required' => 'Eine Kontakt muss angegeben sein.',
            'is_natural_no_zero' => 'Unbekannte Person.',
        ],
        'reminderdate' => [
            'required' => 'Ein Datum muss angegeben sein.',
            'valid_date' => 'Das Datum is ungültig.',
        ],
        'remindertime' => [
            'required' => 'Eine Uhrzeit muss angegeben sein.',
            'valid_date' => 'Die Uhrzeit is ungültig.',
        ],
        'reminderuse' => [
            'in_list' => 'Es ist doch nur ein Schalter, wie kann das denn schiefgehen?',
        ]
    ];

    public array $taskMove= [
        'taskid' => 'required|is_natural_no_zero',
        'siblingid' => 'required|integer',
        'targetcol' => 'required|is_natural_no_zero'
    ];

    public array $columnCreateAndEdit = [
        'column' => 'required|string|max_length[255]',
        'description' => 'required',
        'sortid' => 'permit_empty|integer'
    ];

    public array $columnCreateAndEdit_errors = [
        'column' => [
            'required' => 'Der Spaltenname muss ausgefüllt sein.',
            'max_length[255]' => 'Der Spaltenname darf höchstens 255 Zeichen lang sein.'
        ],
        'description' => [
            'required' => 'Die Beschreibung muss ausgefüllt sein.',
        ],
        'sortid' => [
            'integer' => 'Sort ID muss eine ganze Zahl sein.',
        ],
    ];

    public array $boardCreateAndEdit = [
        'board' => 'required|string|max_length[255]'
    ];

    public array $boardCreateAndEdit_errors = [
        'board' => [
            'required' => 'Der Boardname muss ausgefüllt sein.',
            'max_length[255]' => 'Der Boardname darf höchstens 255 Zeichen lang sein.'
        ],
    ];

}
