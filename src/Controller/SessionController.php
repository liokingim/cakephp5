<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\CookieManager;
use Cake\Log\Log;

class SessionController extends AppController
{
    protected $cookieManager;

    public function initialize(): void
    {
        parent::initialize();
        // $this->loadComponent('Cookies', []);
        Log::debug(__CLASS__ . " : ". __METHOD__ . " start");
        // $this->loadComponent('Cookie');
        $this->cookieManager = new CookieManager($this);
        // $this->cookieManager = new CookieManager($this->request, $this->response);
        Log::debug(__CLASS__ . " : " . __METHOD__ . " end");
    }

    public function getCookie(): void
    {
        Log::debug(__CLASS__ . " : " . __METHOD__ . " start");
        echo "<pre>setCookie start";
        // var_dump($token);
        echo "</pre>";

        // $this->Cookies->write('mycookie', "afafadafaf");
        $this->cookieManager->setCookie('mycookie', "afafadafaf");
        echo "<pre>setCookie1 start";
        // var_dump($token);
        echo "</pre>";
        $this->cookieManager->setCookie('mycookie1', "afafadafaf");
        echo "<pre>setCookie2 start";
        // var_dump($token);
        echo "</pre>";
        $this->cookieManager->setCookie('mycookie2', "afafadafaf");

        // $this->response = $this->cookieManager->write('token_view', 'afafafdfgsdgsdg');

        echo "<pre>setCookie end ";
        // var_dump($token);
        echo "</pre>";
        Log::debug(__CLASS__ . " : " . __METHOD__ . " end");
    }
}