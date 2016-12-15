<?php

class Language
{
    public $language = 'en';

    private $strings = array();

    public function t($string)
    {
        if($this->has($string)) return $this->strings[$string];

        return $string;
    }

    public function p($string)
    {
        if($this->has($string))
        {
            echo $this->strings[$string];
        }
        else
        {
            echo $string;
        }
    }

    public function has($string)
    {
        return isset($this->strings[$string]);
    }

    public function setLanguage($language = '')
    {
        if ($language) $this->language = $language;
    }

    public function loadLanguage($language = '')
    {
        if (!$language) $language = $this->language;

        $file = DIR_LANG.$language.'.php';

        if (file_exists($file))
        {
            $strings = require_once($file); // $strings here
            $this->parseLanguage($strings);
        }
    }

    private function parseLanguage($array = array(), $prefix = '')
    {
        foreach($array as $key => $value)
        {
            if ($prefix)
            {
                $key = $prefix.'.'.$key;
            }

            if (is_array($value))
            {
                $this->parseLanguage($value, $key);
            }
            else
            {
                $this->strings[$key] = $value;
            }
        }
    }
}