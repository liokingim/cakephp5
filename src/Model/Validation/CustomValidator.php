<?php

declare(strict_types=1);

namespace App\Model\Validation;

use Cake\Validation\Validator;

class CustomValidator extends Validator
{
    public function __construct()
    {
        parent::__construct();

        $this->setProvider('custom', new CustomValidation());

        // $this->setProvider('custom', 'App\Validation\Rules');
        $this->setProvider('customRules', new Rules());

        $this->add('birth_year', [
            'validFormat' => [
                'rule' => ['custom', '/^[0-9]+$/'],
                'message' => 'The birth year must be a valid number.'
            ],
            'validDate' => [
                'rule' => 'validateSeparatedDate',
                'provider' => 'custom',
                'message' => 'The provided date is invalid.'
            ]
        ]);

        $this->add('birthYear', 'validNumber', [
            'rule' => 'validNumber',
            'provider' => 'customRules',
            'message' => 'The birth year must be a valid number.'
        ]);

        $this->add('birth_month', [
            'validFormat' => [
                'rule' => ['custom', '/^[0-9]+$/'],
                'message' => 'The birth month must be a valid number.'
            ]
        ]);

        $this->add('birth_day', [
            'validFormat' => [
                'rule' => ['custom', '/^[0-9]+$/'],
                'message' => 'The birth day must be a valid number.'
            ]
        ]);

        $this->add('email', 'validEmail', [
            'rule' => 'validateEmail',
            'provider' => 'customRules',
            'message' => 'The email must be a valid email address.'
        ]);
    }
}