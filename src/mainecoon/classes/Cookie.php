<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 21.11.2016
 * Time: 17:26
 */

namespace Mainecoon;


class Cookie
{
    private $data = array();
    private $expire;
    private $remove;
    private $domain;

    public $language;


    private static $instance;

    public static function getInstance()
    {
        if (self::$instance === null)
        {
            self::$instance = new self;
        }
        return self::$instance;
    }

    public function __construct()
    {
        $request = Request::getInstance();

        $this->data = $_COOKIE;
        $this->expire = time() + (60 * 60 * 24); // 24 часа
        $this->remove = time() - (60 * 60 * 24); // 24 часа
        $this->domain = $request->domain;
    }




    public function get($key, $default = null)
    {
        if (isset($this->data['key']))
            return $this->data[$key];
        else
            return $default;
    }



    public function set($key, $value)
    {
        $old = null;

        if (isset($this->data['key']))
        {
            $old = $this->data[$key];
        }

        $this->data[$key] = $value;
        setcookie($key, serialize($value), $this->expire, '/', $this->domain, false, true);

        return $old;
    }



    public function remove($key)
    {
        if (isset($this->data['key']))
        {
            unset($this->data[$key]);
        }

        setcookie($key, '', $this->remove, '/', $this->domain, false, true);
    }



    public function has($key)
    {
        return isset($this->data[$key]);
    }



    public function __get($key)
    {
        $this->get($key);
    }



    public function __set($key, $value)
    {
        return $this->set($key, $value);
    }
}