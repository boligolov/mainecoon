<?php

namespace Mainecoon;

class Lang {

    public static $language;

    // TODO: Сделать подгрузку языков из отдельных файлов
    private static $strings = [
        'ru' => [
            'php/outdated' => 'Устаревшая версия PHP. Для корректной работы требуется PHP версии 5.3 и выше',

            'config_file/not_exists' => 'Файл конфигурации не существует. Создайте его или отключите в настройках необходимость его чтения',
            'config_file/cannot_read' => 'Файл конфигурации пуст или не читается',
            'config_file/json' => 'Ошибка в файле конфигурации: ',

            'temp/not_exists' => 'Временный каталог не существует. Создание временного каталога отключено в настройках.',
            'temp/cannot_create' => 'Не удается создать временный каталог.',
            'temp/cannot_write' => 'Временный каталог не доступен для записи',

            'meinecoon/strange_post' => 'Непонятный запрос',
            'meinecoon/ready' => 'Meinecoon готов к работе',

            'debug/temp_created' => 'Временный каталог создан',
            'debug/temp_writable' => 'Временный каталог доступен для записи',
        ],
        'en' => [
            'php/outdated' => 'Outdated version of PHP. To work correctly, it requires PHP version 5.3 and above.',

            'config_file/not_exists' => 'Файл конфигурации не существует. Создайте его или отключите в настройках необходимость его чтения.',
            'config_file/cannot_read' => 'Файл конфигурации пуст или не читается',
            'config_file/json' => 'Ошибка в файле конфигурации: ',

            'temp/cannot_create' => 'Не удается создать временный каталог.',

            'meinecoon/ready' => 'Meinecoon is ready for work',

            'debug/temp_created',
        ],
    ];

    public static function t($string, $lang = '')
    {
        if(!$lang) $lang = self::$language;

        if(!empty(self::$strings[$lang][$string])) return self::$strings[$lang][$string];

        return $string;
    }

    public static function p($string, $lang = '')
    {
        if(!$lang) $lang = self::$language;

        if(!empty(self::$strings[$lang][$string]))
        {
            echo self::$strings[$lang][$string];
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
}