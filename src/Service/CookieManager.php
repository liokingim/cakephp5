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

    public function __construct(Controller $controller)
    {
        $this->request = $controller->getRequest();
        $this->response = $controller->getResponse();
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
                            array $pOptions = [])
    {
        if (empty($this->_values)) {
            $this->read();
        }

        if (strpos($this->request->getEnv('HTTP_HOST'), ':') !== false) {
            $domain = preg_replace('/:\d+$', '', $this->request->getEnv('HTTP_HOST'));
        } else {
            $domain = $this->request->getEnv('HTTP_HOST');
        }

        $options = [
            'expires' => isset($pOptions['expires']) ? $pOptions['expires'] : null,
            'path' => isset($pOptions['path']) ? $pOptions['path'] : "/",
            'domain' => isset($pOptions['domain']) ? $pOptions['domain'] : $domain,
            'secure' => isset($pOptions['secure']) ? $pOptions['secure'] : false,
            'hostonly' => isset($pOptions['hostonly']) ? $pOptions['hostonly'] : false,
            'httponly' => isset($pOptions['httponly']) ? $pOptions['httponly'] : false,
            'samesite' => isset($pOptions['samesite']) ? $pOptions['samesite'] : null,
        ];

        if (!is_array($key)) {
            $key = [$key => $value];
        }

        foreach ($key as $name => $value) {

            $names = [$name];

            if (strpos($name, '.') !== false) {
                $names = explode('.', $name, 2);
            }

            $firstName = $names[0];
            $isMultiValue = (is_array($value)) || count($names) > 1;

            if (!isset($this->_values[$this->name][$firstName]) && $isMultiValue) {
                $this->_values[$this->name][$firstName] = [];
            }

            if (count($names) > 1) {
                $this->_values[$this->name][$firstName] = Hash::insert($this->_values[$this->name][$firstName], $names[1], $value);
            } else {
                $this->_values[$this->name][$firstName] = $value;
            }

            $cookie = Cookie::create($firstName, $this->getValue($this->_values[$this->name][$firstName]), $options);

            $this->response = $this->response->withAddedHeader('Set-Cookie', $cookie->toHeaderValue());
            $this->controller->setResponse($this->response);
        }
    }

    protected function getValue(mixed $value) {
        if (is_array($value)) {
            $value = json_encode($value);
        }

        return $value;
    }

    public function setCookie(string $name, string $value): void
    {
        if (strpos($this->request->getEnv('HTTP_HOST'), ':') !== false) {
            $domain = preg_replace('/:\d+$', '', $this->request->getEnv('HTTP_HOST'));
        } else {
            $domain = $this->request->getEnv('HTTP_HOST');
        }

        $options = [
            'expires' => isset($pOptions['expires']) ? $pOptions['expires'] : null,
            'path' => isset($pOptions['path']) ? $pOptions['path'] : "/",
            'domain' => isset($pOptions['domain']) ? $pOptions['domain'] : $domain,
            'secure' => isset($pOptions['secure']) ? $pOptions['secure'] : false,
            'hostonly' => isset($pOptions['hostonly']) ? $pOptions['hostonly'] : false,
            'httponly' => isset($pOptions['httponly']) ? $pOptions['httponly'] : false,
            'samesite' => isset($pOptions['samesite']) ? $pOptions['samesite'] : null,
        ];

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
            $this->_values = $this->controller->getRequest()->getCookieParams();
            return $this->_values;
        }

        return $this->controller->getRequest()->getCookie($key);
    }

    /**
     * 쿠키에서 특정 키를 삭제합니다.
     */
    public function delete($key)
    {
        $this->response = $this->response->withExpiredCookie(new Cookie($key));
    }
}