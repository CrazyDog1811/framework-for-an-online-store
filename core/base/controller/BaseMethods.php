<?php


namespace core\base\controller;


trait BaseMethods {

    protected function clearStr($str) {

        if (is_array($str)) {
            foreach ($str as $key => $item) $str[$key] = trim(strip_tags($item)); // почистить значение от пробелов и тэгов
            return $str;
        } else {
            return trim(strip_tags($str));
        }

    }

    protected function clearNum($num) {
        return $num * 1;
    }

    protected function isPost() {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    protected function isAjax() {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest'; //существует ли и равен ли он
    }

    protected function redirect($http = false, $code = false) {
       if ($code) {
            $codes = ['301' => 'HTTP/1.1 301 Move Permanently'];
            if ($codes[$code]) header($codes[$code]); // отправить заголовок
       }
       if ($http) $redirect = $http;
            else $redirect = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : PATH;

            header("location: $redirect");

            exit;
    }

    protected function writeLog($message, $file = 'log.txt', $event = 'Fault') {

        $dateTime = new \DateTime();

        $str = $event . ': ' . $dateTime->format('d-m-Y G:i:s') . ' - ' . $message . '\r\n';

        file_put_contents('log/' . $file, $str, FILE_APPEND); // записать в файл по пути: 1аргумент, текст: 2аргумент, не перезаписать, а дозаписать: 3аргумент
    }

}