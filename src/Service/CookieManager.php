<?php

namespace App\Service;

use Cake\Http\Cookie\Cookie;
use Cake\Http\Cookie\CookieCollection;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\Utility\Hash;

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

    public $name = '';

    protected $_values = [];

    public function __construct(ServerRequest $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    /**
     * 쿠키에 값을 설정합니다.
     *
     * @param string $key 쿠키 키 (예: 'member' 또는 'member.id')
     * @return Response
     */
    public function write(array|string $key,
                            array|string|float|int|bool $value = null,
                            mixed $expires = null,
                            string $path = '/',
                            bool $secure = false,
                            bool $httpOnly = true,
                            mixed $sameSite = null)
    {
        if (empty($this->_values[$this->name])) {
            $this->read();
        }

        $options = [
            'expires' => $expires,
            'path' => $path,
            'domain' => $this->request->domain(),
            'secure' => $secure,
            'httponly' => $httpOnly,
            'samesite' => $sameSite
        ];

        if (!is_array($key)) {
            $key = [$key => $value];
        }

        $cookieCollection = new CookieCollection();

        foreach ($key as $name => $value) {

            $names = [$name];

            if (strpos($name, '.') !== false) {
                $names = explode('.', $name, 2);
            }

            $firstName = $names[0];
            $isMultiValue = (is_array($value)) || count($names) > 1;

            if (!isset($this->_values[$firstName]) && $isMultiValue) {
                $this->_values[$firstName] = [];
            }

            if (count($names) > 1) {
                if (is_array($this->_values[$firstName]) && $isMultiValue) {
                    $this->_values[$firstName] =
                        Hash::insert($this->_values[$firstName], $names[1], $value);
                } else if (!is_array($this->_values[$firstName]) && $isMultiValue) {
                    $this->_values[$firstName] =
                        Hash::insert([$names[1] => $value], $names[1], $value);
                } else {
                    $this->_values[$firstName] = $value;
                }
            } else {
                $this->_values[$firstName] = $value;
            }

            $cookie = Cookie::create($firstName, json_encode($this->_values[$firstName]), $options);

            $cookieCollection = $cookieCollection->add($cookie);
        }

        return $this->response->withCookieCollection($cookieCollection);
    }

    /**
     * 쿠키에서 값을 읽습니다.
     */
    public function read(string $key = null)
    {
        if ($key === null) {
            $this->_values = $this->request->getCookieParams();
            return $this->_values;
        }

        return $this->request->getCookie($key);
    }

    /**
     * 쿠키에서 특정 키를 삭제합니다.
     */
    public function delete($key)
    {
        $this->response = $this->response->withExpiredCookie(new Cookie($key));
    }
}