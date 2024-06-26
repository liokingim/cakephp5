<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\CookieManager;
use Cake\Event\EventInterface;
use Cake\Log\Log;

class SessionController extends AppController
{
    protected $cookieManager;
    protected $event;

    public function initialize(): void
    {
        parent::initialize();
        // $this->loadComponent('Cookies', []);
        Log::debug(__CLASS__ . " : ". __METHOD__ . " start");
        $this->loadComponent('Cookie', [$this]);
        $this->cookieManager = new CookieManager($this);

        // $this->cookieManager = new CookieManager($this->request, $this->response);
        Log::debug(__CLASS__ . " : " . __METHOD__ . " end");
    }

    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
        $this->event = $event;
        Log::debug(__CLASS__ . " : " . __METHOD__ . " start");
        // $event->stopPropagation();

        $this->getCookie();
        $this->setCookie();
        Log::debug(__CLASS__ . " : " . __METHOD__ . " end");
    }

    public function getCookie()
    {
        Log::debug(__CLASS__ . " : " . __METHOD__ . " start " . __LINE__);
        $this->event->stopPropagation();

        return $this->redirect("https://www.daum.net");

        echo "<pre>getCookie start";
        // var_dump($token);
        echo "</pre>";

        // $this->Cookies->write('mycookie', "afafadafaf");
        // $this->cookieManager->setCookie('mycookie.id', "testid");
        // echo "<pre>setCookie1 start";
        // // var_dump($token);
        // echo "</pre>";
        // $this->cookieManager->setCookie('mycookie.sss', "12334");
        // echo "<pre>setCookie2 start";
        // // var_dump($token);
        // echo "</pre>";
        // $this->cookieManager->setCookie('mycookie.ev', "afafadafaf");

        // $this->cookieManager->write('mycookie1', "teststests");
        // $this->cookieManager->write('mycookie.id', "testid");
        // $this->cookieManager->write('mycookie.aa', "12334");
        // $this->cookieManager->write('mycookie.ev', "afafadafaf");

        // $this->Cookie->write('Cookie1', "teststests1");
        // $this->Cookie->write('Cookie2', "teststests2");
        // $this->Cookie->write('Cookie1.id', "testid1234");
        // $this->Cookie->write('Cookie1.aa', "12334");
        // $this->Cookie->write('Cookie1.ev', "afafadafaf");

        // echo "<pre>Cookie->read start ";
        // var_dump($this->Cookie->read('Cookie2'));
        // echo "-- </pre>";

        $this->Cookie->delete('Cookie1');
        // $this->Cookie->destory();

        // $this->response = $this->cookieManager->write('token_view', 'afafafdfgsdgsdg');

        Log::debug(__CLASS__ . " : " . __METHOD__ . " end");
    }


    public function setCookie()
    {
        Log::debug(__CLASS__ . " : " . __METHOD__ . " start " . __LINE__);

        Log::debug(__CLASS__ . " : isStopped " . $this->event->isStopped());

        if ($this->event->isStopped()) {
            return false;
        }

        Log::debug(__CLASS__ . " : " . __METHOD__ . " end");
    }
}