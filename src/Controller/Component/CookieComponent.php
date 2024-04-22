<?php

namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Http\Cookie\Cookie;
use Cake\I18n\DateTime;

class CookieComponent extends Component
{
    /**
    * Set a cookie.
    *
    * @param string $name Cookie name
    * @param mixed $value Cookie value
    * @param int $expires Expiry time in seconds
    */
    public function set($name, $value, $expires = 86400)
    {
        $cookie = new Cookie(
            $name,
            $value,
            null,
            '/',
            '',
            false, // Secure
            true, // HttpOnly
            null
        );

        $this->getController()->getResponse()->getCookieCollection()->add($cookie);
    }

    /**
    * Get a cookie value.
    *
    * @param string $name Cookie name
    * @return mixed
    */
    public function read($name)
    {
        return $this->getController()->getRequest()->getCookie($name);
    }

    /**
    * Delete a cookie.
    *
    * @param string $name Cookie name
    */
    public function delete($name)
    {
        $this->getController()->getResponse()->getCookieCollection()->remove($name);
    }
}