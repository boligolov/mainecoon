<?php

$start = microtime(true);

define('MAINECOON', 'Mainecoon');
define('VERSION', '0.1');
define('HOME_LINK', 'https://github.com/boligolov/mainecoon');

#{standalone}#
/**
 *   Тип приложения
 */
define('STANDALONE', false);
#{/standalone}#

//TODO: определять, запущены ли мы Web или SLI
/**
 * Коды выхода для SLI
 */
define('EXIT_OK',           0);
define('EXIT_NO_CONFIG',    1);
define('EXIT_BAD_CONFIG',   2);
define('EXIT_TEMPLATE_NOT_FOUND',   3);


/**
 * Пути и основные настройки
 */
define('DS',             DIRECTORY_SEPARATOR);
define('DIR_NAME',       'mainecoon'.DS);
define('BASEPATH',       dirname(__FILE__).DS);
define('DIR_MAINECOON',  BASEPATH.DIR_NAME);
define('DIR_CLASSES',    DIR_MAINECOON.'classes'.DS);
define('DIR_VIEW',       DIR_MAINECOON.'view'.DS);
define('DIR_LANG',       DIR_MAINECOON.'languages'.DS);
define('DIR_JS',         DIR_MAINECOON.'js'.DS);
define('WEB_JS',         '\\'.DIR_NAME.'js'.DS);
define('DIR_CSS',        DIR_MAINECOON.'css'.DS);
define('WEB_CSS',        '\\'.DIR_NAME.'css'.DS);


/**
 * Подключение классов, без автозагрузчика, ибо не надо
 */
#{includes}#
function autoLoader($class)
{
    $file = DIR_CLASSES.ucfirst($class).'.php';

    if (is_file($file))
    {
        require_once($file);
        return true;
    }

    return false;
}

spl_autoload_register('autoLoader');
spl_autoload_extensions('.php');
#{/includes}#


$Config = new Config();

define('DIR_TEMP', BASEPATH.$Config->get('temp.dir').DS);

$Language = new Language();
$Language->setLanguage($Config->get('language'));
$Language->loadLanguage();

$Request = new Request();

$Cookie = new Cookie();

$View = new View(array(
    'config' => $Config,
    'lang'   => $Language,
));

$Temp = new Temp(array(
    'config' => $Config,
    'lang'   => $Language,
));

$mainecoon = new Mainecoon(array(
    'config' => $Config,
    'lang' => $Language,
    'request' => $Request,
    'cookie' => $Cookie,
    'view' => $View,
    'temp' => $Temp,
));

if ($Config->loaded)
{
    $mainecoon->disableSettingsPage();
}

$mainecoon->loadState();

if (!$mainecoon->test())
    $mainecoon->view->page('error', array('errors' => $mainecoon->errors));



$mainecoon->route();



$end = microtime(true);
//exit(EXIT_OK);
exit();

