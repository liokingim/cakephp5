<?php

namespace App\Service;

use Cake\Controller\Controller;
use Cake\Http\Cookie\Cookie;
use Cake\Http\Cookie\CookieCollection;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\I18n\Time;
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

    private $controller;

    public $name = '';

    protected $_values = [];

    protected $_defaultConfig = [
        'key' => null,
        'expires' => 0,
        'maxAge' => null,
        'path' => '',
        'domain' => '',
        'secure' => false,
        'httpOnly' => false,
        'sameSite' => ''
    ];


    public function __construct(Controller $controller)
    {
        // $this->request = $request;
        // $this->response = $response;
        $this->controller = $controller;
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
        $cookies = [];

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

            // $cookieCollection = $cookieCollection->add($cookie);
            $cookies[] = $cookie;
        }

        // return $this->response->withCookieCollection($cookieCollection);
        return $cookies;
    }

    public function getConfig($key = null, $default = null)
    {
        $config = $this->_defaultConfig;

        if ($key) {
            if (!array_key_exists($key, $config)) {
                return $default;
            }

            return $config[$key];
        }

        if ($default === true) {
            return (object)$config;
        }

        return (array)$config;
    }

    public function setCookie(string $name, string $value): void
    {
        $options = $this->getConfig();

        $cookie = $name . '=' . rawurlencode($value);
        if ($options['expires']) {
            $cookie .= '; Expires=' . (new Time($options['expires']))->format(DATE_COOKIE);
        }
        if ($options['maxAge'] !== null) {
            $cookie .= '; Max-Age=' . (int)$options['maxAge'];
        }
        if ($options['domain']) {
            $cookie .= '; Domain=' . strtolower($options['domain']);
        }
        if ($options['path']) {
            $cookie .= '; Path=' . $options['path'];
        }
        if ($options['secure'] === true) {
            $cookie .= '; Secure';
        }
        if ($options['httpOnly'] === true) {
            $cookie .= '; HttpOnly';
        }
        $sameSite = strtolower($options['sameSite']);
        if ($sameSite && in_array($sameSite, ['none', 'lax', 'strict'])) {
            $cookie .= '; SameSite=' . ucfirst($sameSite);
        }

        // $response = $this->getController()->getResponse()->withHeader('Set-Cookie', $cookie);
        // $this->getController()->setResponse($response);

        echo "<pre>setCookie3 start";
        var_dump($cookie);
        echo "</pre>";

        $response = $this->controller->getResponse()->withAddedHeader('Set-Cookie', $cookie);
        $this->controller->setResponse($response);
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