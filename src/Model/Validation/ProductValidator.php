<?php

declare(strict_types=1);

namespace App\Model\Validation;

use Cake\Validation\Validator;

class ProductValidator extends Validator
{
    public function __construct()
    {
        parent::__construct();

        $this->add('name', [
                'notEmpty' => [
                    'rule' => ['notBlank'], // 사용 가능한 rule
                    'message' => 'Product name is required'
            ]
        ]);

        $this->add('price', [
                'valid' => [
                    'rule' => ['notBlank', 'numeric'],
                    'message' => 'Please enter a valid price'
                ]
        ]);

        $this->add('quantity', [
                'valid' => [
                    'rule' => ['naturalNumber', true], // 자연수 검증
                    'message' => 'Please enter a valid quantity'
                ]
        ]);


        $this->add('origin', [
            'notEmpty' => [
                'rule' => 'notBlank',
                'message' => 'Origin is required'
            ]
        ]);

        $this->add('email', [
            'validFormat' => [
                'rule' => ['email'],
                'message' => 'Please enter a valid email address'
            ]
        ]);

        $this->add('password', [
            'notEmpty' => [
                'rule' => ['notBlank'],
                'message' => 'Password is required'
            ],
            'minLength' => [
                'rule' => ['minLength', 6],
                'message' => 'Password must be at least 6 characters long'
            ]
        ]);
    }
}