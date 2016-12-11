<?php

namespace Mainecoon;

class Lang {

    public static $language = 'en';

    // TODO: Сделать подгрузку языков из отдельных файлов
    private static $strings = array();

    public static function t($string)
    {
        if(!empty(self::$strings[$string])) return self::$strings[$string];

        return $string;
    }

    public static function p($string)
    {
        if(!empty(self::$strings[$string]))
        {
            echo self::$strings[$string];
        }
        else
        {
            echo $string;
        }
    }

    public static function setLanguage($language)
    {
        self::$language = $language;
    }

    public static function loadLanguage($language)
    {
        $file = DIR_LANG.$language.'.php';

        if (file_exists($file))
        {
            require_once $file; // $strings here
            self::parseLanguage($strings);
        }
    }

    private static function parseLanguage($array = array(), $prefix = '')
    {
        foreach($array as $key => $value)
        {
            if ($prefix)
            {
                $key = $prefix.'.'.$key;
            }

            if (is_array($value))
            {
                self::parseConfig($value, $key);
            }
            else
            {
                self::$strings[$key] = $value;
            }
        }
    }
}