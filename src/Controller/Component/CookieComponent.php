<?php

namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\Controller;
use Cake\Http\Cookie\Cookie;
use Cake\Http\Cookie\CookieCollection;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\I18n\Time;
use Cake\Utility\Hash;
use DateTime;

class CookieComponent extends Component
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

    public function initialize(array $config):void
    {
        parent::initialize($config);
        $this->controller = $this->getController();
        $this->request = $this->controller->getRequest();
        $this->response = $this->controller->getResponse();
    }

    /**
     * 쿠키에 값을 설정합니다.
     *
     * @param string $key 쿠키 키 (예: 'member' 또는 'member.id')
     * @return Response
     */
    public function write(
        array|string $key,
        array|string|float|int|bool $value = null,
        array $pOptions = []):void
    {
        if (empty($this->_values)) {
            $this->read();
        }

        // echo "<pre>write start ---";
        // var_dump($this->_values);
        // echo "</pre>";

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

            // echo "<pre>names start -";
            // var_dump($this->name);
            // var_dump($names);
            // echo "</pre>";

            if (!isset($this->_values[$this->name][$firstName]) && $isMultiValue) {
                $this->_values[$this->name][$firstName] = [];
            }

            // string(0) "" string(7) "Cookie1" array(1) { [0]=> string(7) "Cookie1" }
            // string(0) "" string(7) "Cookie1" array(2) { [0]=> string(7) "Cookie1" [1]=> string(2) "id" } array(0) { }

            // echo "<pre>firstName start -";
            // var_dump($firstName);
            // echo "</pre>";

            if (count($names) > 1) {
                if (array_key_exists($this->name, $this->_values)) {

                    // echo "<pre>array_key_exists1 start -";
                    // var_dump($this->_values[$this->name][$firstName]);
                    // echo "</pre>";

                    $this->_values[$this->name][$firstName] = Hash::insert($this->_values[$this->name][$firstName], $names[1], $value);

                    // echo "<pre>array_key_exists2 start -";
                    // var_dump($this->_values[$this->name][$firstName]);
                    // echo "</pre>";

                    $cookie = Cookie::create($firstName, $this->getValue($this->_values[$this->name][$firstName]), $options);
                } else {
                    $this->_values[$firstName] = $value;

                    // echo "<pre>!array_key_exists start -";
                    // var_dump($this->_values);
                    // echo "</pre>";
                    $cookie = Cookie::create($firstName, $this->getValue($this->_values[$firstName]), $options);
                }
            } else {
                $this->_values[$firstName] = $value;

                // echo "<pre>!count start -";
                // var_dump($this->_values);
                // echo "</pre>";
                $cookie = Cookie::create($firstName, $this->getValue($this->_values[$firstName]), $options);
            }

            $this->response = $this->response->withAddedHeader('Set-Cookie', $cookie->toHeaderValue());
            $this->controller->setResponse($this->response);
        }
    }

    protected function getValue(mixed $value)
    {
        if (is_array($value)) {
            $value = json_encode($value);
        }

        return $value;
    }

    protected function decodeValue(string $string)
    {
        $first = substr($string, 0, 1);
        if ($first === '{' || $first === '[') {
            $ret = json_decode($string, true);
            return ($ret !== null) ? $ret : $string;
        }

        return $string;
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

        // echo "<pre>setCookie3 start";
        // var_dump($cookie);
        // echo "</pre>";

        $response = $this->controller->getResponse()->withAddedHeader('Set-Cookie', $cookie);
        $this->controller->setResponse($response);
    }

    /**
     * 쿠키에서 값을 읽습니다.
     */
    public function read(string $key = null): mixed
    {
        $this->_values = $this->request->getCookieParams();

        if ($key === null) {
            echo "<pre>getCookieParams start";
            var_dump($this->request->getCookieParams());
            echo "</pre>";

            foreach ($this->_values as $key => $value) {
                $this->_values[$key] = $this->decodeValue($value);
            }

            return $this->_values;
        }

        if (strpos($key, '.') !== false) {
            $name = explode('.', $key, 2);
            $key = $name[0];
        }

        echo "<pre>key start ";
        var_dump($key);
        echo "</pre>";

        if (!isset($this->_values[$key])) {
            return null;
        }

        echo "<pre>request->getCookie start ";
        var_dump($this->request->getCookie($key));
        echo "</pre>";

        if (!empty($name[1])) {
            $this->_values[$key] = $this->decodeValue($this->request->getCookie($key));

            if (!is_array($this->_values[$key]) || !array_key_exists($name[1], $this->_values[$key])) {
                return null;
            }

            echo "<pre>this->_values start ";
            var_dump($this->_values[$key][$name[1]]);
            echo "</pre>";

            return $this->_values[$key][$name[1]];
        }

        return $this->decodeValue($this->request->getCookie($key));
    }

    /**
     * 쿠키에서 특정 키를 삭제합니다.
     */
    public function delete(string $key)
    {
        // echo "<pre>key delete start ";
        // var_dump($key);
        // echo "</pre>";

        if (strpos($this->request->getEnv('HTTP_HOST'), ':') !== false) {
            $domain = preg_replace('/:\d+$', '', $this->request->getEnv('HTTP_HOST'));
        } else {
            $domain = $this->request->getEnv('HTTP_HOST');
        }
        // echo "<pre>domain delete start ";
        // var_dump($domain);
        // echo "</pre>";
        $this->response = $this->response->withExpiredCookie(new Cookie($key, "", new DateTime('-1 day'), '/', $domain));
        $this->controller->setResponse($this->response);
    }

    /**
     * 쿠키 삭제
     */
    public function destory()
    {
        // 요청에서 모든 쿠키를 가져옵니다.
        $cookies = $this->request->getCookieCollection();

        // echo "<pre>cookies all delete start ";
        // var_dump($cookies);
        // echo "</pre>";

        // 모든 쿠키에 대해 반복 처리
        foreach ($cookies as $cookie) {
            // 쿠키의 만료 시간을 과거로 설정하여 만료 처리
            $expiredCookie = new Cookie(
                $cookie->getName(),
                '',
                new DateTime('-1 day'), // 만료 시간을 과거로 설정
                $cookie->getPath(),
                $cookie->getDomain(),
                $cookie->isSecure(),
                $cookie->isHttpOnly()
            );

            // 응답 객체에 만료된 쿠키를 추가
            $this->response = $this->response->withExpiredCookie($expiredCookie);
            $this->controller->setResponse($this->response);
        }
    }
}
