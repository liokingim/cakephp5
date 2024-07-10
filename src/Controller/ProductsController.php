<?php
declare(strict_types=1);

namespace App\Controller;

use App\Controller\AppController;
use App\Model\Validation\CustomValidator;
use App\Model\Validation\ProductValidator;
use App\Utility\RedisStatus;
use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\Http\Client;
use Cake\Http\Exception\NotFoundException;
use Cake\Log\Log;
use KubAT\PhpSimple\HtmlDomParser;
use Symfony\Component\HttpFoundation\Response;

class ProductsController extends AppController
{
    private $Products;
    private $ProductionDates;

    public function initialize(): void
    {
        parent::initialize();

        // 인증 미들웨어 설정
        $this->loadComponent('Authentication.Authentication');

        // 특정 액션에 대해 인증 비활성화
        $this->Authentication->allowUnauthenticated(['index', 'add', 'get', 'addProductionDate', 'registProductionDate']);

        // 권한 미들웨어 설정
        $this->loadComponent('Authorization.Authorization');

        // 특정 액션에 대해 권한 비활성화
        $this->Authorization->skipAuthorization(['index', 'add', 'get','addProductionDate', 'registProductionDate']);

        $this->Products = $this->fetchTable('Products');
        $this->ProductionDates = $this->fetchTable('ProductionDates');
        // $this->loadComponent('Paginator');
    }

    public function index()
    {
        // $this->autoRender = false;

        // $str = '<a><b>Hello!</b></a>';

        // $dom = HtmlDomParser::str_get_html($str);

        // // $dom = HtmlDomParser::file_get_html($file_name);
        // $elems = $dom->find('b', 0)->plaintext;
        // // var_dump($dom->plaintext);

        // var_dump($elems);


// 원본 HTML 문자열
$htmlString = '<html><body><p class="example">Hello, World!</p><div class="example">Another Element</div></body></html>';

// DOMDocument 객체 생성
$dom = new DOMDocument('1.0', 'UTF-8');

// HTML을 로드 (에러 무시)
@$dom->loadHTML($htmlString);

// DOMXPath 객체 생성
$xpath = new DOMXPath($dom);

// 특정 클래스 이름을 가진 모든 요소 찾기 (예: class가 'example'인 요소)
$elements = $xpath->query('//*[@class="example"]');

foreach ($elements as $element) {
    // 요소의 값 변경
    $element->nodeValue = 'New Content for class example';

    // 요소의 클래스 속성 변경
    $element->setAttribute('class', 'new-class');

    // 요소의 스타일 속성 추가
    $element->setAttribute('style', 'color: red; font-size: 20px;');

    // 요소의 ID 속성 변경
    $element->setAttribute('id', 'new-id');
}

// DOM을 문자열로 변환
$newHtmlString = $dom->saveHTML();

// 변경된 HTML 출력
echo $newHtmlString;



        $products = $this->Products->find('all');
        $this->set(compact('products'));
    }

    public function add()
    {
        if ($this->request->is('post')) {
            $data = $this->request->getData();

            $validator = new ProductValidator();

            // 전체 데이터 배열을 설정
            $validator->set($data);

            // 추가적인 유효성 검사 규칙 추가
            $validator->addMoreRules();

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

    public function addProductionDate($productId = null)
    {
        if (!$productId || !$this->Products->exists(['id' => $productId])) {
            $this->Flash->error(__('Invalid product.'));
            return $this->redirect(['action' => 'index']);
        }

        $productionDate = $this->ProductionDates->newEmptyEntity();
        $latestProductionDate = $this->ProductionDates->find()
            ->where(['product_id' => $productId])
            ->order(['created' => 'DESC'])
            ->first();

        if ($this->request->is('post')) {
            $productionDate = $this->ProductionDates->patchEntity($productionDate, $this->request->getData());
            $productionDate->product_id = $productId;

            if ($this->ProductionDates->save($productionDate)) {
                $this->Flash->success(__('The production date has been saved.'));
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The production date could not be saved. Please, try again.'));
        }

        $product = $this->Products->get($productId);
        $this->set(compact('productionDate', 'product', 'latestProductionDate'));
    }

    public function registProductionDate()
    {
        if ($this->request->is('post')) {
            $data = $this->request->getData();

            $productionDate = $this->ProductionDates->newEmptyEntity();
            $productionDate = $this->ProductionDates->patchEntity($productionDate, $data);

            if ($this->ProductionDates->save($productionDate)) {
                $response = [
                    'status' => 'success',
                    'message' => 'The production date has been saved.'
                ];
            } else {
                $response = [
                    'status' => 'error',
                    'message' => 'The production date could not be saved. Please, try again.',
                    'errors' => $productionDate->getErrors()
                ];
            }

            $this->set([
                'response' => $response,
                '_serialize' => 'response'
            ]);

            return $this->redirect(['action' => 'index']);
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

    public function checkRedis()
    {
        $isRedisRunning = RedisStatus::isRedisRunning();
        $this->set(compact('isRedisRunning'));
    }
}