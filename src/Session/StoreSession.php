<?php

namespace Casperlaitw\LaravelSSO\Session;

use Casperlaitw\SSO\Server\GlobalSession;
use Eastwest\Json\Json;

/**
 * Class StoreSession
 *
 * @package Casperlaitw\LaravelSSO\Session
 */
class StoreSession extends GlobalSession
{
    private $userKey = 'user';

    /**
     * Save user to session
     *
     * @param $value
     */
    public function setUserSession($value)
    {
        if (is_array($value) && is_object($value)) {
            $value = Json::encode($value);
        }
        $this->put($this->userKey, $value);
    }

    /**
     * Get user data
     *
     * @return array|mixed|\StdClass|null
     */
    public function getUserSession()
    {
        if ($user = $this->get($this->userKey)) {
            if (is_string($user)) {
                return Json::decode($user, true);
            }

            return $user;
        }

        return null;
    }

    /**
     * Clear user session
     */
    public function clearUserSession()
    {
        $this->forget($this->userKey);
    }

    /**
     * Remove session from key
     *
     * @param $key
     */
    public function forget($key)
    {
        unset($_SESSION[$key]);
    }

    /**
     * @param $key
     *
     * @return mixed|null
     */
    public function get($key)
    {
        return $_SESSION[$key] ?? null;
    }

    /**
     * Put value to session
     * @param $key
     * @param $value
     */
    public function put($key, $value)
    {
        $_SESSION[$key] = $value;
    }
}
