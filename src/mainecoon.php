<?php

namespace Mainecoon;

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
define('DIR_JS',         DIR_MAINECOON.'js'.DS);
define('WEB_JS',         '\\'.DIR_NAME.'js'.DS);
define('DIR_CSS',        DIR_MAINECOON.'css'.DS);
define('WEB_CSS',        '\\'.DIR_NAME.'css'.DS);


/**
 * Подключение классов, без автозагрузчика, ибо не надо
 */
#{includes}#
require_once DIR_CLASSES."Config.php";
require_once DIR_CLASSES."Request.php";
require_once DIR_CLASSES."Cookie.php";
require_once DIR_CLASSES."Mainecoon.php";
require_once DIR_CLASSES."Lang.php";
require_once DIR_CLASSES."Functions.php";
require_once DIR_CLASSES."Heartbeat.php";
require_once DIR_CLASSES."View.php";
#{/includes}#

$Сonfig = Config::getInstance();
$Request = Request::getInstance();
$Cookie = Cookie::getInstance();
$View = View::getInstance();
Lang::setLanguage($Сonfig->language);


$mainecoon = new Mainecoon();
$mainecoon->config = $Сonfig;
$mainecoon->request = $Request;
$mainecoon->cookie = $Cookie;
$mainecoon->view = $View;
if ($Сonfig->loaded) $mainecoon->display_settings_page = false;

$mainecoon->test();
$mainecoon->route();



$end = microtime(true);
//exit(EXIT_OK);
exit();

