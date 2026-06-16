<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Validation\StrictRules\CreditCardRules;
use CodeIgniter\Validation\StrictRules\FileRules;
use CodeIgniter\Validation\StrictRules\FormatRules;
use CodeIgniter\Validation\StrictRules\Rules;

class Validation extends BaseConfig
{
    // Setup
    public array $ruleSets = [
        Rules::class,
        FormatRules::class,
        FileRules::class,
        CreditCardRules::class,
    ];

    public array $templates = [
        'list'   => 'CodeIgniter\Validation\Views\list',
        'single' => 'CodeIgniter\Validation\Views\single',
    ];

 
    public array $formUserRules = [
        'nama'  => 'required',
        'email' => 'required|valid_email',
    ];

    public array $formUserRules_errors = [
        'nama' => [
            'required' => 'Nama wajib diisi.',
        ],
        'email' => [
            'required'    => 'Email tidak boleh kosong.',
            'valid_email' => 'Format penulisan email salah.',
        ],
    ];
}