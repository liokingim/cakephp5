<?php
declare(strict_types=1);

namespace App\Controller;

use App\Service\CookieManager;
use Cake\Http\Cookie\Cookie;

/**
 * Students Controller
 *
 */
class StudentsController extends AppController
{
    protected $cookieManager;

    public function initialize(): void
    {
        parent::initialize();

        $this->loadComponent('Cookie');
        $this->cookieManager = new CookieManager($this->request, $this->response);
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $this->Authorization->skipAuthorization();


        // // 멤버 데이터 배열
        $memberData = [
            'id' => '123',
            'name' => '山田',
            'number' => '0011223344',
            'time' => time()
        ];

        // 쿠키 생성 및 설정
        // $cookie = new Cookie(
        //     'memberTokenDetail',                // 쿠키 이름
        //     json_encode($memberData, JSON_UNESCAPED_UNICODE), // 멤버 데이터를 JSON 문자열로 인코딩
        //     // $memberData,
        //     new \DateTime('+1 week')  // 쿠키의 유효 기간 설정
        // );

        // // 쿠키를 응답에 추가
        // return $this->response->withCookie($cookie);

// var_dump($this->request->getCookie('memberTokenDetail'));

        // $options = [
        //     'expires' => null,
        //     'path' => '/',
        //     'domain' => $this->request->domain(),
        //     'secure' => false,
        //     'httponly' => true,
        //     'samesite' => null
        // ];

        // return $this->response->withCookie(Cookie::create('userId', $memberData, $options));

        // $data = [
        //     'memberId' => '1234567890',
        //     'token' => '1234567890'
        // ];

        // $this->Cookie->set('memberId', '1234567890');

        $token = $this->Cookie->read('csrfToken');

echo "<pre>token 1 ";
var_dump($token);
echo "</pre>";

        // 쿠키 쓰기
        // $this->response = $this->cookieManager->write('token', 'test1234');
        // $this->response = $this->cookieManager->write('memberTokenDetail', 'testafafafafa');
        // $this->response = $this->cookieManager->write('memberTokenDetail.id', '567890');
        // $this->response = $this->cookieManager->write(['memberTokenDetail' => '132344']);
        $this->response = $this->cookieManager->write(['memberTokenDetail' => 'testafafafafa', 'token' => 'test1234']);
        $this->response = $this->cookieManager->write([
        'memberTokenDetail.id' => 'test1234',
        'memberTokenDetail.token' => 'adfasdsdfgsdgf'
    ]);

        $memberTokenDetail = $this->Cookie->read('memberTokenDetail');
        echo "<pre> memberTokenDetail ";
        var_dump($memberTokenDetail);
        echo "</pre>";
        // $this->response = $this->cookieManager->write([
        //     'memberTokenDetail' => ['name' => 'test3234234', 'id' => 'tttttt'],
        // ]);

        $cookie = $this->cookieManager->read('memberTokenDetail.token');
        echo "<pre>memberTokenDetail.id ";
        var_dump($cookie);
        echo "</pre>";


        $token = $this->cookieManager->read('token');
        echo "<pre>token ";
        var_dump($token);
        echo "</pre>";
        // $this->response = $this->cookieManager->write('memberTokenDetail.id', 'test1234');
        // $this->response = $this->cookieManager->write('memberTokenDetail', json_encode($data));

        // $this->response->withCookieCollection($cookieCollection);

        // // 쿠키 읽기
        // $memberId = $this->cookieManager->read('memberTokenDetail');
        // var_dump($memberId);


        // $this->Cookie->write('Preference.color', 'blue');

        // $this->response = $this->response->withCookie(Cookie::create('Preference.color', 'test3'));
        // $this->response = $this->response->withCookie(new Cookie('Preference.color', 'test2'));


        // var_dump($this->response->getCookie('Preference.color'));

        // $session = $this->request->getAttribute('session');
        // $session->write('User.data', ['id' => 1, 'name' => 'John Doe']);

        // $userData = $session->read('User.data');

        // var_dump($userData);

        $query = $this->Students->find();
        $students = $this->paginate($query);

        $this->set(compact('students'));
    }

    /**
     * View method
     *
     * @param string|null $id Student id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $student = $this->Students->get($id, contain: []);
        $this->set(compact('student'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $student = $this->Students->newEmptyEntity();
        if ($this->request->is('post')) {
            $student = $this->Students->patchEntity($student, $this->request->getData());
            if ($this->Students->save($student)) {
                $this->Flash->success(__('The student has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The student could not be saved. Please, try again.'));
        }
        $this->set(compact('student'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Student id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $student = $this->Students->get($id, contain: []);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $student = $this->Students->patchEntity($student, $this->request->getData());
            if ($this->Students->save($student)) {
                $this->Flash->success(__('The student has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The student could not be saved. Please, try again.'));
        }
        $this->set(compact('student'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Student id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $student = $this->Students->get($id);
        if ($this->Students->delete($student)) {
            $this->Flash->success(__('The student has been deleted.'));
        } else {
            $this->Flash->error(__('The student could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
