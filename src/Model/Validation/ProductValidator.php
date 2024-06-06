<?php

declare(strict_types=1);

namespace App\Model\Validation;

use App\Model\Traits\RuleNotBlankTrait;
use App\Model\Traits\SetFieldTrait;
use Cake\Validation\Validator;

class ProductValidator extends Validator
{
    use SetFieldTrait;
    use RuleNotBlankTrait;

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

        // $this->add('quantity', [
        //         'valid' => [
        //             'rule' => ['naturalNumber', true], // 자연수 검증
        //             'message' => 'Please enter a valid quantity'
        //         ]
        // ]);

        // $this->add('origin', [
        //     'notEmpty' => [
        //         'rule' => 'notBlank',
        //         'message' => 'Origin is required'
        //     ]
        // ]);

        // $this->add('email', [
        //     'validFormat' => [
        //         'rule' => ['email'],
        //         'message' => 'Please enter a valid email address'
        //     ]
        // ]);

        // $this->add('password', [
        //     'notEmpty' => [
        //         'rule' => ['notBlank'],
        //         'message' => 'Password is required'
        //     ],
        //     'minLength' => [
        //         'rule' => ['minLength', 6],
        //         'message' => 'Password must be at least 6 characters long'
        //     ]
        // ]);
    }

    public function addMoreRules()
    {
        $this->field('name')->rules();

        // 기존에 추가된 'name' 필드에 새로운 규칙 추가
        $this->add('name', [
            'minLength' => [
                'rule' => ['minLength', 3],
                'message' => 'Product name must be at least 3 characters long'
            ]
        ]);

        // 기존에 추가된 'quantity' 필드에 새로운 규칙 추가
        $this->add('price', [
            'notEmpty' => [
                'rule' => 'notBlank',
                'message' => 'Price is required'
            ]
        ]);
    }
}