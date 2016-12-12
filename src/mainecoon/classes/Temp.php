<?php

namespace Mainecoon;

class Temp
{
    private $config;

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
        $this->config = Config::getInstance();
    }


    public function clean($exclude = array())
    {
        foreach ($exclude as &$item)
        {
            $item = DIR_TEMP.$item;
        }

        $temp_files = glob(DIR_TEMP.'*');

        foreach ($temp_files as $file)
        {
            if (!in_array($file, $exclude))
            {
                unlink($file);
            }
        }
    }

    public function write($data = '', $file = '')
    {
        if (!$file)
        {
            $file = $this->config->get('temp.file');
        }

        $file = DIR_TEMP.$file;

        file_put_contents($file, serialize($data));
    }



    public function load($file = '')
    {
        if (!$file)
        {
            $file = $this->config->get('temp.file');
        }

        $file = DIR_TEMP.$file;

        if (file_exists($file))
        {
            $content = file_get_contents($file);
            return unserialize($content);
        }

        return false;
    }

}