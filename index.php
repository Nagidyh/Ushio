<?php
/**
 * 定义目录
 */
define('_ROOT', dirname(__FILE__) . DIRECTORY_SEPARATOR);
define('_SYS_PATH', _ROOT . 'Ushio' . DIRECTORY_SEPARATOR);
define('_APP', _ROOT . 'app' . DIRECTORY_SEPARATOR);

/**
 * 启动框架
 */
require _SYS_PATH.'Ushio.php';
$_config = require _SYS_PATH.'config.php';


$app = new Ushio();
$app->run();