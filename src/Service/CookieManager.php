<?php

namespace App\Service;

use Cake\Http\Cookie\Cookie;
use Cake\Http\Cookie\CookieCollection;
use Cake\Http\Response;
use Cake\Http\ServerRequest;

class CookieManager
{
    /**
     * @var ServerRequest
     */
    private $request;

    /**
     * @var Response
     */
    private $response;

    public function __construct(ServerRequest $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    /**
     * 쿠키에 값을 설정합니다.
     */
    public function write(string $name,
                            mixed $value,
                            mixed $expires = null,
                            string $path = '/',
                            bool $secure = false,
                            bool $httpOnly = true,
                            mixed $sameSite = null): Response
    {


        $options = [
            'expires' => $expires,
            'path' => $path,
            'domain' => $this->request->domain(),
            'secure' => $secure,
            'httponly' => $httpOnly,
            'samesite' => $sameSite
        ];

        return $this->response->withCookie(Cookie::create($name, $value, $options));
    }

    /**
     * 쿠키에서 값을 읽습니다.
     */
    public function read(string $name)
    {
        $cookie = '';

        $cookies = $this->request->getCookieCollection();

        if ($cookies->has($name)) {
            $cookie = $cookies->get($name)->getValue();
        }

        // $cookies = $this->request->getCookie($name);
        // var_dump($cookies);

        var_dump($_COOKIE);

var_dump($cookie);


        // return $cookies->get($name)->getValue();
        return;
    }

    /**
     * 쿠키에서 특정 키를 삭제합니다.
     */
    public function delete($name)
    {
        $this->response = $this->response->withExpiredCookie(new Cookie($name));
    }
}