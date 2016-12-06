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
            // Сия нелепая конструкция нужна для удобства создания standalone-файла
            #{config}#
            if (file_exists(DIR_MAINECOON."config.php"))
            {
                require_once DIR_MAINECOON."config.php"; // here $config
                $this->loaded = true;
            }
            else
            {
                $config = $this->loadDefault(); // Загружаем предустановки
                // Фла loaded не устанавливается, чтобы показать страницу с настройками
            }
            #{/config}#
            //$this->data = $config;
        }

        $this->parseConfig($config);

/*        echo "<pre>";
        print_r($this->data);
        echo "</pre>";*/

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


    /**
     * Отдает конфигурацию "по-умолчанию".
     *
     * @return array
     */
    private function loadDefault()
    {
        return array(
            /**
             *      Тестирование окружения.
             *      Если скрипт настроен и используется многократно, лучше отключить для большей производительности
             */
            'test_requirements' => true,


            /**
             *      Тестирование окружения при ajax-запросе.
             *      По-умолчанию отключено.
             *      Если скрипт настроен и используется многократно, лучше отключить для большей производительности
             */
            'test_requirements_on_ajax' => false,



            /**
             *   Язык интерфейса по-умолчанию
             */
            'language' => 'ru',


            /**
             *   Имя временного каталога
             */
            'temp_dir' => 'mainecoon_temp',

            /**
             *   Пытаться создать каталог, если не существует
             */
            'temp_dir_create' => true,

            /**
             *   Очищать временный каталог от файлов перед работой
             */
            'temp_dir_clear_before' => true, // Очищать за собой временный каталог

            /**
             *   Имя временного файла с общей информацией
             */
            'temp_filename' => 'mainecoon.temp',

            /**
             *   Имя временного файла-флага
             */
            'temp_flag' => 'mainecoon.flag',


            /**
             *   Сохранять конфигурацию в файл .mainecoon
             */
            'config_write' => false,


            'exclude' => array(
                'path' => array(
                    '.git',
                    '.idea',
                ),
                'ext'  => array('jpg', 'jpeg', 'png', 'bmp', 'gif', 'mp4', 'mp3', 'ogg'),
                'mime' => array(),
                'size' => array(
                    'more' => 1048576, // байт
                    'less' => 0,
                ),
            ),

            /**
             *   Список проверяемых и сохраняемых параметров файлов
             */
            'check' => array(
                'md5' => true,
                'mimetype' => true,
                'size' => true,
                'rights' => true,
                'mtime' => true,
                'atime' => true,
                'ctime' => true,
            ),
        );
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