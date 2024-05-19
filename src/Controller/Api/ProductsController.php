<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Controller\AppController;
use Cake\Event\EventInterface;
use Cake\Http\Exception\NotFoundException;
use Cake\Log\Log;
use Cake\View\JsonView;

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

    public function viewClasses(): array
    {
        return [JsonView::class];
    }

    public function beforeRender(EventInterface $event)
    {
        Log::write("info", __CLASS__ . " : " . __FUNCTION__." start");
        parent::beforeRender($event);

        if ($this->request->is('ajax')) {
            $this->viewBuilder()->setClassName('Ajax');
        }
        Log::write("info", __CLASS__ . " : " . __FUNCTION__. " end");
    }

    public function add()
    {
        Log::write("info", __CLASS__." : ".$this->request->getParam('action') . " start");

        $this->request->allowMethod(['post']);
        $product = $this->Products->newEmptyEntity();
        $product = $this->Products->patchEntity($product, $this->request->getData());

        if ($product->hasErrors()) {
            $this->set([
                'errors' => $product->getErrors(),
                '_serialize' => ['errors']
            ]);
            $this->viewBuilder()->setOption('serialize', true);
            $this->response = $this->response->withStatus(400);
            return;
        }

        if ($this->Products->save($product)) {
            $this->set([
                'message' => 'Product has been saved',
                'product' => $product,
                '_serialize' => ['message', 'product']
            ]);
            $this->viewBuilder()->setOption('serialize', true);
        } else {
            $this->set([
                'message' => 'Unable to save product',
                '_serialize' => ['message']
            ]);
            $this->viewBuilder()->setOption('serialize', true);
            $this->response = $this->response->withStatus(500);
        }

        Log::write("info", __CLASS__ . " : " . $this->request->getParam('action') . " end");
    }

    public function get($id = null)
    {
        Log::write("info", __CLASS__ . " : " . $this->request->getParam('action') . " start");
        $this->request->allowMethod(['get']);

        if (!$id) {
            throw new NotFoundException(__('Invalid product ID'));
        }

        $product = $this->Products->findById($id)->first();

        if (!$product) {
            throw new NotFoundException(__('Product not found'));
        }

        Log::write("info", "product: " . $product);

        $this->set([
            'product' => $product,
            '_serialize' => ['product']
        ]);

        $this->viewBuilder()->setClassName("Json")->setOption('serialize', ['product']);

        Log::write("info", __CLASS__ . " : " . $this->request->getParam('action') . " end");
    }

}