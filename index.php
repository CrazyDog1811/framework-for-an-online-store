<?php

define('VG_ACCESS', true);//определяем константу безопасности

header('Context-Type:text/html;charset=utf-8');//отправляем браузеру пользователя заголовки с типом контента и кодировками
session_start();//стартуем сессию

require_once 'config.php';//подключаем файл(файл для быстрого развёртывания сайта на хостинге)
require_once 'core/base/settings/internal_settings.php';//более фундаментальные настройки(пути к шаблонам, настройки безопасности сайта

use core\base\exceptions\RouteException; // импортируем класс
use core\base\controller\RouteController;


try{
    RouteController::instance() -> route();
} catch (RouteException $e) {
    exit($e->getMessage());
}



