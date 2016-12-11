<?php

namespace Mainecoon;


class Config
{
    private $data = array();
    private $exclude_rules = array(
        'exclude.path',
        'exclude.ext',
        'exclude.mime'
    );

    public $loaded = false;

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
        $config = array();

        if (file_exists(BASEPATH.".mainecoon"))
        {
            $config = $this->load();
        }

        if (!$config)
        {
            // В STANDALONE здесь будет готовый массив, но мы все равно покажем страницу настроек.
            // В режиме обычного приложения мы подразумеваем, что имеем config.php, ибо он там лежит по-умолчанию.

            #{config}#
            require_once DIR_MAINECOON."config.php"; // $config здесь
            $this->loaded = true;
            #{/config}#
        }

        $this->parseConfig($config);

        return $this;
    }



    public function get($key, $default = null)
    {
        if ($this->has($key))
        {
            return $this->data[$key];
        }
        else
        {
            $data = array();

            $key .= '.';

            foreach ($this->data as $config_key => $config_value)
            {
                if (strpos($config_key, $key) === 0)
                {
                    $config_key = str_replace($key, '', $config_key);

                    $data[$config_key] = $config_value;
                }
            }

            if (!empty($data))
            {
                return $data;
            }

            return $default;
        }

    }



    public function set($key, $value)
    {
        $old = null;

        if ($this->has($key))
        {
            $old = $this->data[$key];
        }

        $this->data[$key] = $value;

        return $old;
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



    /**
     * Read configuration from .mainecoon
     * File in JSON format
     *
     * Читает конфигурацию из файла .mainecoon
     * Файл в формате JSON
     * @return string
     */
    private function load()
    {
        $config = file_get_contents(BASEPATH.'.mainecoon');

        if ($config === false)
        {
            //TODO: для SLI
            //echo "Cann't read '.mainecoon'.";
            //exit(EXIT_BAD_CONFIG);
            return false;
        }

        $config = json_decode($config, true);

        if (json_last_error() != JSON_ERROR_NONE)
        {
            //TODO: для SLI
            //echo "Cann't load config from '.mainecoon'. Error in JSON: ".json_last_error_msg();
            //exit(EXIT_BAD_CONFIG);
            return false;
        }

        $this->loaded = true;
        return $config;
    }

    public function save($data, $where = 'cookie')
    {
        if ($where == 'cookie')
        {
            $this->saveToCookie($data);
        }
        elseif ($where == 'file')
        {
            $this->saveToFile($data);
        }
    }


    private function saveToFile($data)
    {
        return file_put_contents('.mainecoon', json_encode($data, JSON_UNESCAPED_UNICODE));
    }

    private function saveToCookie($data)
    {
        $cookie = Cookie::getInstance();

        foreach ($data as $key => $value)
        {
            $cookie->set($key, $value);
        }
    }

    private function loadFromCookie()
    {
        $default = $this->loadDefault();
        $config = array();

        foreach ($default as $key => $value)
        {

        }
    }

    private function parseConfig($array = array(), $prefix = '')
    {
        //TODO: выключать чтение прав, если работа в Windows
        foreach($array as $key => $value)
        {
            if ($prefix)
            {
                $key = $prefix.'.'.$key;
            }

            if (is_array($value) && !in_array($key, $this->exclude_rules))
            {
                $this->parseConfig($value, $key);
            }
            else
            {
                $this->data[$key] = $value;
            }
        }
    }
}