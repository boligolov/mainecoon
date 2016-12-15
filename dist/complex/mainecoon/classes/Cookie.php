<?php

class Cookie
{
    private $data = array();
    private $expire;
    private $remove;
    private $domain;

    public $language;


    public function __construct()
    {
        $this->data = $_COOKIE;
        $this->expire = time() + (60 * 60 * 24); // 24 часа
        $this->remove = time() - (60 * 60 * 24); // 24 часа
        //$this->domain = $request->domain;
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