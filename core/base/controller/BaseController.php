<?php


namespace core\base\controller;


use core\base\exceptions\RouteException;

abstract class BaseController {

    protected $page;
    protected $errors;

    protected $controller;
    protected $inputMethod; // свойство, хранящее метод, собирающий данные из БД
    protected $outputMethod; // свойство, хранящее метод, подключающий виды
    protected $parameters;

    public function route() {
        $controller = str_replace('/', '\\', $this->controller);

        try {

            $object = new \ReflectionMethod($controller, 'request'); // с помощью объекта класса ReflectionMethod находим метод 'request' класса, прописанного в $controller

            $args = [
                'parameters' => $this->parameters,
                'inputMethod' => $this->inputMethod,
                'outputMethod' => $this->outputMethod
            ];

            // вызвать у объекта класса ReflectionMethod его метод invoke(содержащий найденый метод 'request')
            $object->invoke(new $controller, $args);

        } catch (\ReflectionException $e) {
            throw new RouteException($e->getMessage());
        }

    }

    public function request($args) {

       $this->parameters = $args['parameters'];

       $inputData = $args['inputMethod'];
       $outputData = $args['outputMethod'];

       $data = $this->$inputData();

       if (method_exists($this, $outputData)) { //если в объекте существует метод outputData

           $page = $this->outputData($data);
           if ($page) $this->page = $page;

       } elseif ($data) {
           $this->page = $data;
       }



       if ($this->errors) {
           $this->writeLog();
       }

       $this->getPage();
    }

    protected function render($path = '', $parameters = []) {

        extract($parameters); // создать в области видимости символьную таблицу из массива $parametres
        if (!$path) {
            $path = TEMPLATE . explode('controller', strtolower((new \ReflectionClass($this))->getShortName()))[0]; // explode вернёт нулевой элемент массива от слова indexcontroller , разделённого по 'controller', т.е. 'index'
        }

        ob_start(); // открыть буфер обмена

        if (!@include_once $path . '.php') {
            throw new RouteException('template ' . $path . ' is absence');
        }
        return ob_get_clean(); // вернуть файл, после чего закрыть буфер обмена
    }

    protected function getPage() {
        if (is_array($this->page)) {
            foreach ($this->page as $block) echo $block;
        } else {
            echo $this->page;
        }
        exit();
    }
}