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

        $data = [
            'memberId' => '1234567890',
            'token' => '1234567890'
        ];

        // 쿠키 쓰기
        $this->response = $this->cookieManager->write('memberTokenDetail', $data);
        $this->response = $this->cookieManager->write('memberTokenDetail.id', 'test1234');

        // // 쿠키 읽기
        $memberId = $this->cookieManager->read('memberTokenDetail');
        var_dump($memberId);


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
