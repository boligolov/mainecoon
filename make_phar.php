<?php

/**
 *  Собираем phar-файл ручками
 */

$phar = new Phar('dist/mainecoon.phar');

$phar->addEmptyDir('css');
$phar->addEmptyDir('js');
$phar->addEmptyDir('img');

$phar->addFile('src/mainecoon/css/app.css', 'css/app.css');

//$dir = 'src/mainecoon';
//$phar->buildFromDirectory($dir);