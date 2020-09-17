<?php


namespace core\base\controller;


use core\base\exceptions\RouteException;
use core\base\settings\Settings;

abstract class BaseController {

    use \core\base\controller\BaseMethods; // подключить trait

    protected $page;
    protected $errors;

    protected $controller;
    protected $inputMethod; // свойство, хранящее метод, собирающий данные из БД
    protected $outputMethod; // свойство, хранящее метод, подключающий виды
    protected $parameters;

    protected $styles;
    protected $scripts;

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
             new RouteException($e->getMessage());
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
           $this->writeLog($this->errors);
       }

       $this->getPage();
    }

    protected function render($path = '', $parameters = []) {

        extract($parameters); // создать в области видимости символьную таблицу из массива $parametres
        if (!$path) {

            $class = new \ReflectionClass($this);

            $space = str_replace('\\', '/', $class->getNamespaceName() . '\\'); // Получить пространство имён класса объекта this

            $routes = Settings::get('routes');

            if ($space === $routes['user']['path']) {
                $template = TEMPLATE;
            } else {
                $template = ADMIN_TEMPLATE;
            }

            $path = $template . explode('controller', strtolower($class->getShortName()))[0]; // explode вернёт нулевой элемент массива от слова indexcontroller , разделённого по 'controller', т.е. 'index'
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

    protected  function init($admin = false) {

        if (!$admin) {
            if (USER_CSS_JS['styles']) {
                foreach (USER_CSS_JS['styles'] as $item) $this->styles = PATH . TEMPLATE . trim($item, '/');
            }

            if (USER_CSS_JS['scripts']) {
                foreach (USER_CSS_JS['scripts'] as $item) $this->scripts = PATH . TEMPLATE . trim($item, '/');
            }
        } else {
            if (ADMIN_CSS_JS['styles']) {
                foreach (USER_CSS_JS['styles'] as $item) $this->styles = PATH . ADMIN_TEMPLATE . trim($item, '/');
            }

            if (ADMIN_CSS_JS['scripts']) {
                foreach (USER_CSS_JS['scripts'] as $item) $this->scripts = PATH . ADMIN_TEMPLATE . trim($item, '/');
            }
        }
    }
}