<?php

namespace App\Model\Table;

use App\Model\Validation\CustomValidator;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class ProductionDatesTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('production_dates');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');
    }

    public function validationDefault(Validator $validator): Validator
    {
        // $validator
        //     ->integer('birth_year')
        //     ->requirePresence('birth_year', 'create')
        //     ->notEmptyString('birth_year', 'Birth year is required');

        // $validator
        //     ->integer('birth_month')
        //     ->requirePresence('birth_month', 'create')
        //     ->notEmptyString('birth_month', 'Birth month is required');

        // $validator
        //     ->integer('birth_day')
        //     ->requirePresence('birth_day', 'create')
        //     ->notEmptyString('birth_day', 'Birth day is required');

        $validator = new CustomValidator();

        return $validator;
    }
}
