<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\CookieManager;

class SessionController extends AppController
{
    protected $cookieManager;

    public function initialize(): void
    {
        parent::initialize();

        $this->loadComponent('Cookie');
        $this->cookieManager = new CookieManager($this->request, $this->response);
    }

    public function getCookie(): void
    {
        echo "<pre>getCookie start";
        // var_dump($token);
        echo "</pre>";

        $this->response = $this->cookieManager->write('token_view', 'afafafdfgsdgsdg');

        echo "<pre>getCookie end ";
        // var_dump($token);
        echo "</pre>";
    }
}