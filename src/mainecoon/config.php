<?php

$config = array(

    /**
     *      Отладка
     */
    'debug' => true,



    'test' => array(
        /**
         *      Тестирование окружения.
         *      Если скрипт настроен и используется многократно, лучше отключить для большей производительности
         */
        'requirements' => true,

        /**
         *      Тестирование окружения при ajax-запросе.
         *      По-умолчанию отключено.
         *      Если скрипт настроен и используется многократно, лучше отключить для большей производительности
         */
        'on_ajax' => false,
    ),


    /**
     *   Параметр отпередляет, следует ли подгружать необходимые файлы или все находится в одном файле
     */
    'standalone' => true,

    /**
     *   Язык интерфейса по-умолчанию
     */
    'language' => 'ru',



    'temp' => array(
        /**
         *   Имя временного каталога
         */
        'dir' => 'mainecoon_temp',

        /**
         *   Пытаться создать каталог, если не существует
         */
        'create' => true,

        /**
         *   Очищать временный каталог от файлов перед работой
         */
        'clean' => true,

        /**
         *   Имя временного файла с общей информацией
         */
        'file' => 'mainecoon.temp',

        /**
         *   Имя временного файла с внешним снимком
         */
        'uploaded' => 'mainecoon.upload',

        /**
         *   Имя временного файла с текущим снимком
         */
        'snapshot' => 'mainecoon.snapshot'
    ),


    /**
     *   Сохранять конфигурацию в файл .mainecoon
     */
    'config_write' => false,

    'scan' => array(
        'mode' => 'exclude' // exclude - исключать по расширению, include - включать только указанные расширения
    ),

    'exclude' => array(
        'path' => array(
            'mainecoon',
            'mainecoon_temp',
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

    'include' => array(
        'ext' => 'php html js tpl'
    ),

    /**
     *   Список проверяемых и сохраняемых параметров файлов
     */
    'check' => array(
        'size' => true,
        'mime' => false,
        'ctime' => false,
        'mtime' => false,
        'atime' => false,
        'ext'   => true,
        'md5' => true,
        'rights' => true,
    ),


);