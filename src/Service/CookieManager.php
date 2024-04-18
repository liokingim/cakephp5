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
        $options = [
            'expires' => $expires,
            'path' => $path,
            'domain' => $this->request->domain(),
            'secure' => $secure,
            'httponly' => $httpOnly,
            'samesite' => $sameSite
        ];

        var_dump("<br>------key-------<br>");
        var_export($key);
        var_dump("<br>-------------<br>");

        $cookieData = $this->request->getCookieParams();

        var_dump("<br>------cookieData-------<br>");
        var_export($cookieData);
        var_dump("<br>-------------<br>");

        if (!is_array($key)) {
            $key = [$key => $value];
        }

        var_dump("<br>------key2-------<br>");
        var_export($key);
        var_dump("<br>-------------<br>");

        $cookieCollection = new CookieCollection();

        foreach ($key as $name => $value) {
            $values = [];

            echo "<br>-------------foreach-start-------------<br>";
            var_dump("<br>------name------<br>");
            var_export($name);
            var_dump("<br>-------------<br>");

            $names = [$name];

            // 키 분리 처리
            if (strpos($name, '.') !== false) {
                // 점을 기준으로 키 분리
                $names = explode('.', $name, 2);
            }

            var_dump("<br>------names------<br>");
            var_export($names);
            var_dump("<br>-------------<br>");

            $firstName = $names[0];
            $isMultiValue = (is_array($value)) || count($names) > 1;

            var_dump("<br>------firstName------<br>");
            var_export($firstName);
            var_dump("<br>-------------<br>");
            var_dump("<br>------isMultiValue------<br>");
            var_export($isMultiValue);
            var_dump("<br>-------------<br>");

            if (!isset($cookieData[$firstName]) && $isMultiValue) {
                $values[$firstName] = [];
            }

            var_dump("<br>------values1------<br>");
            var_export($values);
            var_dump("<br>-------------<br>");
            echo "<br>". count($names)."<br>";

            if (count($names) > 1) {
echo "<br>-11111-<br>";
                $values[$firstName][$names[1]] = json_encode($value);
            } else {
                echo "<br>-222222-<br>";
                $values[$firstName] = json_encode($value);
            }

            var_dump("<br>------values2222------<br>");
            var_export($values);
            var_dump("<br>-------------<br>");

            echo "<br>-------------foreach-end-------------<br>";

            $cookie = Cookie::create($firstName, json_encode($value), $options);

            $cookieCollection = $cookieCollection->add($cookie);
            // $this->response->withCookie(Cookie::create($firstName, json_encode($value), $options));
        }

        return $this->response->withCookieCollection($cookieCollection);
        // $cookieCollection = new CookieCollection();

        // foreach ($values as $key => $value) {
        //     // 배열 값을 JSON 문자열로 변환
        //     $encodedValue = json_encode($value);

        //     // 쿠키 컬렉션에 쿠키 추가
        //     $cookie = Cookie::create($key, $encodedValue, $options);

        //     $cookieCollection = $cookieCollection->add($cookie);
        // }

        // return $this->response->withCookieCollection($cookieCollection);
        // $options = [
        //     'expires' => $expires,
        //     'path' => $path,
        //     'domain' => $this->request->domain(),
        //     'secure' => $secure,
        //     'httponly' => $httpOnly,
        //     'samesite' => $sameSite
        // ];

        // return $this->response->withCookie(Cookie::create($key, $value, $options));
    }

    /**
     * 쿠키에서 값을 읽습니다.
     */
    public function read(string $name)
    {
        // 쿠키에서 전체 데이터 불러오기
        $cookies = $this->request->getCookieCollection();

        if (!$cookies) {
            $cookieData = []; // 쿠키가 비어있는 경우, 새 배열을 할당
        } else {
            if ($cookies->has($name)) {
                $cookie = $cookies->get($name)->getValue();
                $cookieData = json_decode($cookie, true); // JSON 디코딩
            }
        }

        // $cookies = $this->request->getCookie($name);
        // var_dump($cookies);

var_dump($_COOKIE);
var_dump($cookie);
var_dump($cookieData);

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