<?php

defined('VG_ACCESS') or die('access denied');

const TEMPLATE='templates/default/'; // путь к шаблонам
const ADMIN_TEMPLATE='core/admin/view/'; // шаблоны для админ.панели

//константы безопасности
const COOKIE_VERSION ='1.0.0'; // версия куки-файлов(при смене версии куки-файлов пользователи будут вынуждены перелогиниться)
const CRYPT_KEY = ''; // ключ шифрования для куки-файлов
const COOKIE_TIME = 60; // для админа будем ограничивать время бездействия (60), если админ ничего не делал на сайте 60 минут, заставит залогиниться по новой
const BLOCK_TIME = 3; // заблокирует пользователя на это время (3), если он ери раза неверно ввёл пароль

// постраничная навигация
const QTY = 8; // количество товаров на странице
const QTY_LINKS = 3; // количество ссылок на странице(левее и правее активной)

const ADMIN_CSS_JS = [
    'styles' => [],
    'scripts' => []
]; // пути к стилям и скриптам админ.панели

const USER_CSS_JS = [
    'styles' => [],
    'scripts' => []
]; // пути к стилям и скриптам пользовательской части

use core\base\exceptions\RouteException; // импортируем класс

function autoLoadMainClasses($class_name) {
    $class_name = str_replace('\\', '/', $class_name);
    if (!@include_once $class_name . '.php') {
        throw new RouteException('invalid connection file name: ' . $class_name);
    }
}

spl_autoload_register('autoLoadMainClasses');