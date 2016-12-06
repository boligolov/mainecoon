<?php

$start = microtime(true);

// Определение правил для сборки - какой файл какой функцией обрабатывается
$includes_calls = array(
    'mainecoon/View.php' => 'renderView'
);

define('DIST', 'dist'.DIRECTORY_SEPARATOR);
define('SRC', DIST.'complex'.DIRECTORY_SEPARATOR);
define('STANDALONE', DIST.'standalone'.DIRECTORY_SEPARATOR);


define('STRIP_WHITESPACE', true);

// Функция загрузки PHP-файлов. Вырезает начальный тег в начале
// файла и namespace
function load($file, $clean = true)
{
    $data = file_get_contents($file);

    if ($clean)
    {
        $data = preg_replace('/<\?php/', '', $data, 1);
        $data = preg_replace('/namespace\s+.*/', '', $data, 1);
    }

    return $data;
}


// Специальная функция вставки для класса View
function renderView($file)
{
    $data = '';

    if (file_exists(SRC.$file))
    {
        $data = load(SRC.$file);
    }

    $replaces = array(

        'css' => SRC.'css/app.css',

        'js' => SRC.'js/mainecoon.js',

        'html1' => SRC.'html/html1.html',
        'html2' => SRC.'html/html2.html',
        'index' => SRC.'html/index.html',
        'footer' => SRC.'html/footer.html',

    );

    if (!empty($data))
    {
        foreach ($replaces as $key => $file)
        {
            //if (is_array())
            if (file_exists($file))
            {
                $file_data = file_get_contents($file);
                $data = preg_replace('/#\{'.$key.'\}#/s', $file_data, $data);
            }
        }
    }

    return $data;
}

/**
 * ==============================================================================
 */

// Загрузка файла как строки
$mainecoon = load('src/mainecoon.php', false);


// Вставка конфигураци
$data = load('src/config.php');
$mainecoon = preg_replace('/#\{config\}#(.*)#\{\/config\}#/s', $data, $mainecoon);


// Парсинг по тегам
$data = '';
preg_match('/#\{includes\}#(.*)#\{\/includes\}#/s', $mainecoon, $includes);
if ($includes)
{
    $includes = explode(';', $includes[1]);

    foreach ($includes as $include)
    {
        $include = str_replace('require_once', '', $include);
        $include = str_replace('"', '', $include);
        $include = trim($include);

        if (!empty($includes_calls[$include]))
        {
            // Вставка представления
            $data .= call_user_func($includes_calls[$include], $include);
        }
        else
        {
            if (!empty($include) && (file_exists(SRC.$include)))
            {
                $data .= load(SRC.$include);
            }
        }
    }
}
$mainecoon = preg_replace('/#\{includes\}#(.*)#\{\/includes\}#/s', $data, $mainecoon);


// Запись в единый файл
file_put_contents(STANDALONE.'mainecoon.php', $mainecoon);
if (STRIP_WHITESPACE)
{
    $data = php_strip_whitespace(STANDALONE.'mainecoon.php');
    if (!empty($data))
    {
        file_put_contents(STANDALONE.'mainecoon.php', $data);
    }
    else
    {
        echo "Something wrong in stripping spaces...\n";
    }

}

$end = microtime(true);
echo "Assembly made in ".($end - $start)." seconds.";
system('xcopy "D:\\Development\\Server\\domains\\mainecoon.ru\\dist\\standalone\\mainecoon.php" "D:\\Development\\Server\\domains\\flaxfactory.ru" /Y');

