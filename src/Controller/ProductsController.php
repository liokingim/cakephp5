<?php
declare(strict_types=1);

namespace App\Controller;

use App\Controller\AppController;
use App\Model\Validation\ProductValidator;
use Cake\Http\Client;

class ProductsController extends AppController
{
    public function initialize(): void
    {
        parent::initialize();

        // 인증 미들웨어 설정
        $this->loadComponent('Authentication.Authentication');

        // 특정 액션에 대해 인증 비활성화
        $this->Authentication->allowUnauthenticated(['add']);

        // 권한 미들웨어 설정
        $this->loadComponent('Authorization.Authorization');

        // 특정 액션에 대해 권한 비활성화
        $this->Authorization->skipAuthorization(['add']);
    }

    public function add()
    {
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            $validator = new ProductValidator();
            $errors = $validator->validate($data);

            if (!empty($errors)) {
                // 검증 실패 시 처리
                $this->Flash->error('Validation failed');
                foreach ($errors as $field => $messages) {
                    foreach ($messages as $message) {
                        $this->Flash->error($field . ': ' . $message);
                    }
                }
                return;
            }

            // 검증 성공 시 API 서버로 데이터 전송
            $http = new Client();
            $response = $http->post('http://localhost/api/products/add', $data);

            if ($response->isOk()) {
                $this->Flash->success('Product has been saved.');
            } else {
                $this->Flash->error('Failed to save product to API server.');
            }
        }
    }
}