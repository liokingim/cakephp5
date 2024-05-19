<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

class ProductsTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('products');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->notEmptyString('name', 'Product name is required')
            ->numeric('price', 'Please enter a valid price')
            ->greaterThanOrEqual('price', 0, 'Price must be non-negative')
            ->naturalNumber('quantity', 'Please enter a valid quantity')
            ->notEmptyString('origin', 'Origin is required')
            ->email('email', false, 'Please enter a valid email address')
            ->notEmptyString('password', 'Password is required')
            ->minLength('password', 6, 'Password must be at least 6 characters long');

        return $validator;
    }
}