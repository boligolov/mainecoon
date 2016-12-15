<?php

class General
{
    private $container = array();


    public function addService($key, $service)
    {
        $this->container[$key] = $service;
    }

    public function issetService($key)
    {
        if (isset($this->container[$key])) return true;
        return false;
    }


    public function __get($key)
    {
        if ($this->issetService($key)) return $this->container[$key];
        return null;
    }



    public function __construct($data = array())
    {
        foreach ($data as $key => $object)
        {
            $this->container[$key] = $object;
        }
    }
}