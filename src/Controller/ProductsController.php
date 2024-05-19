<?php
declare(strict_types=1);

namespace App\Controller;

use App\Controller\AppController;
use App\Model\Validation\ProductValidator;
use Cake\Http\Client;
use Cake\Http\Exception\NotFoundException;
use Cake\Log\Log;

class ProductsController extends AppController
{
    public function initialize(): void
    {
        parent::initialize();

        // 인증 미들웨어 설정
        $this->loadComponent('Authentication.Authentication');

        // 특정 액션에 대해 인증 비활성화
        $this->Authentication->allowUnauthenticated(['index', 'add', 'get']);

        // 권한 미들웨어 설정
        $this->loadComponent('Authorization.Authorization');

        // 특정 액션에 대해 권한 비활성화
        $this->Authorization->skipAuthorization(['index', 'add', 'get']);
    }

    public function index()
    {
        $products = $this->Products->find('all');
        $this->set(compact('products'));
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

    public function get($id = null)
    {
        Log::write("info", __CLASS__ . " : " . $this->request->getParam('action') . " start");

        if (!$id) {
            throw new NotFoundException(__('Invalid product ID'));
        }

        $client = new Client();
        $response = $client->get("http://localhost/cakephp-app/api/products/get/{$id}");

        Log::write("info", "getStringBody: ".$response->getStringBody());

        if ($response->getStatusCode() != 200) {
            throw new NotFoundException(__('Product not found'));
        }

        $product = $response->getJson()["product"];

        $this->set(compact('product'));

        Log::write("info", __CLASS__ . " : " . $this->request->getParam('action') . " end");
    }
}