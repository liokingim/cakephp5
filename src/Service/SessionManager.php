<?php

namespace App\Service;

use Cake\Http\Session;

class SessionManager
{
    /**
     * CakePHP Session 객체.
     *
     * @var \Cake\Http\Session
     */
    private $session;

    /**
     * 생성자에서 CakePHP Session 객체를 주입받습니다.
     *
     * @param \Cake\Http\Session $session
     */
    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    /**
     * 세션에 값을 저장합니다.
     *
     * @param string $key 키
     * @param mixed $value 값
     */
    public function write(string $key, $value): void
    {
        $this->session->write($key, $value);
    }

    /**
     * 세션에서 값을 읽습니다.
     *
     * @param string $key 키
     * @return mixed
     */
    public function read(string $key)
    {
        return $this->session->read($key);
    }

    /**
     * 세션에서 특정 키를 삭제합니다.
     *
     * @param string $key 키
     */
    public function delete(string $key): void
    {
        $this->session->delete($key);
    }

    /**
     * 세션을 완전히 파괴합니다.
     */
    public function destroy(): void
    {
        $this->session->destroy();
    }
}
