<?php


namespace core\base\settings;


use core\base\controller\Singleton;

class Settings {

    use Singleton;

    private $routes = [
       'admin' => [ //доступ к админ. панели
          'alias' => 'admin', //параметр для входа
           'path' => 'core/admin/controller/', // путь
           'hrUrl' => false, //human readable url(человекочитаемый url)
           'routes' => [

           ],
       ],
        'settings' => [ // настройки сайта
           'path' => 'core/base/settings/'
        ],
        'plugins' => [
            'path' => 'core/plugins/',
            'hrUrl' => false,
            'dir' => false
        ],
        'user' => [ // настройки для пользовательской части сайта
            'path' => 'core/user/controller/',
            'hrUrl' => true,
            'routes' => [

            ]
        ],
        'default' => [ // раздел по умолчанию(умолчание в строке запроса)
            'controller' => 'IndexController', // контроллер по умолчанию
            'inputMethod' => 'inputData', // метод, который вызовется по умолчанию
            'outputMethod' => 'outputData' // метод, который по умолчанию отвечает за вывод данных в пользовательскую часть
        ],
    ];

    private $templateArr = [
        'text' => ['name', 'phone', 'address'],
        'textarea' => ['content', 'keywords']
    ];

    static public function get($property) { // метод для получения свойств класса singleton
        return self::instance()->$property;
    }

    public function clueProperties($class) {
        $baseProperties = [

        ];

        foreach ($this as $name => $item) {
            $property = $class::get($name);
            if(is_array($property) && is_array($item)) {
               $baseProperties[$name] = $this->arrayMergeRecursive($this->$name, $property);
               continue;
            }
            if (!$property) $baseProperties[$name] = $this->$name;
        }
        return $baseProperties;
    }

    public function arrayMergeRecursive() { // метод для склеивания массивов
        $arrays = func_get_args(); // запись в переменную аргументов(массивов) с метода(явно не указывая аргументы)
        $base = array_shift($arrays); // запись в переменную первого массива из содержащихся в $arrays

        foreach ($arrays as $array) {
            foreach ($array as $key => $value) {
                if (is_array($value) && is_array($base[$key])) {
                    $base[$key] = $this->arrayMergeRecursive($base[$key], $value);
                } else {
                    if (is_int($key)) { // если $key цифра
                        if (!in_array($value, $base)) { // если в массиве $base не содержиться значение $value
                            array_push($base, $value); // то добавим
                        }
                        continue;
                    }
                    $base[$key] = $value;
                }
            }
        }
        return $base;
    }

}