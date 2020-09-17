<?php


namespace core\base\controller;

use core\base\exceptions\RouteException;
use core\base\settings\Settings;

class RouteController extends BaseController {

    use Singleton;

    protected $routes; // свойство для принятия маршрутов

    private function __construct() {

        $address_str = $_SERVER['REQUEST_URI']; // сохраним в переменной строку запроса

        // если строка запроса заканчиается слэшем и это не корень сайта
        if (strrpos($address_str, '/') === strlen($address_str) - 1 && strrpos($address_str, '/') != 0) {
            $this->redirect(rtrim($address_str, '/'), 301 ); // перенаправляем по адресу без слэша в конце и возвращаем код ответа 301
        }

        $path = substr($_SERVER['PHP_SELF'], 0, strpos($_SERVER['PHP_SELF'], 'index.php'));

        if ($path === PATH) {
            $this->routes = Settings::get('routes');

            if (!$this->routes) throw new RouteException('Отсутствуют маршруты в базовых настройках', 1);

            $url = explode('/', substr($address_str, strlen(PATH))); // разделить адресную строку на массив строк, разделённых слэшем, начиная после слэша из константы PATH

            //если после корня в пути прописан алиас админа
            if ($url[0] && $url[0] === $this->routes['admin']['alias']) {

                array_shift($url); // удалить из $url нулевой элемент

                // если есть что либо в массиве и существует директория с таким именем.Если да, то это плагин
                if ($url[0] && is_dir($_SERVER['DOCUMENT_ROOT'] . PATH . $this->routes['plugins']['path'] . $url[0])) {

                    $plugin = array_shift($url); // присвоить первый элемент массива

                    //т.к. настройки плагинов будут содержаться в файлах, именованых с большой буквы, и с добавлением кэмелКейсом слова Settings
                    $pluginSettings = $this->routes['settings']['path'] . ucfirst($plugin) . 'Settings';

                    // существует ли файл с таким именем
                    if (file_exists($_SERVER['DOCUMENT_ROOT'] . PATH . $pluginSettings . '.php')) {

                        //т.к. namespace идут с обратными слэшами, то
                        $pluginSettings = str_replace('/', '\\', $pluginSettings);

                        $this->routes = $pluginSettings::get('routes');
                    }

                    $dir = $this->routes['plugins']['dir'] ? '/' . $this->routes['plugins']['dir'] . '/' : '/';
                    $dir = str_replace('//', '/', $dir); // если строка пришла с двумя слэшами

                    $this->controller = $this->routes['plugins']['path'] . $plugin . $dir;

                    $hrUrl = $this->routes['plugins']['hrUrl'];

                    $route = 'plugins';

                } else {

                    $this->controller = $this->routes['admin']['path'];

                    $hrUrl = $this->routes['admin']['hrUrl'];

                    $route = 'admin';


                }



            } else {


                $hrUrl = $this->routes['user']['hrUrl'];

                $this->controller = $this->routes['user']['path'];

                $route = 'user'; // для кого маршрут
            }

            $this->createRoute($route, $url);

            if ($url[1]) {
                $count = count($url);
                $key = '';

                if (!$hrUrl) {
                    $i = 1;
                } else {
                    $this->parameters['alias'] = $url[1];
                    $i = 2;
                }

                for (; $i < $count; $i++) {
                    if (!$key) {
                        $key = $url[$i];
                        $this->parameters[$key] = '';
                    } else {
                        $this->parameters[$key] = $url[$i];
                        $key = '';
                    }
                }
            }

        } else {
                throw new RouteException('Некорректная директория сайта', 1);
        }

    }

    private function createRoute($var, $arr) {

        $route = [];

        if (!empty($arr[0])) {
            // если в маршрутах существует алиас(псевдоним) с таким же именем, что и элемент массива из адресной строки
            if ($this->routes[$var]['routes'][$arr[0]]) {

                $route = explode('/', $this->routes[$var]['routes'][$arr[0]]); // то разобрать маршрут на массив строк

                $this->controller .= ucfirst($route[0] . 'Controller'); // собрать имя необходимого контроллера

            } else { // если в массиве нет такого элемента, но при этом массив не пуст

                $this->controller .= ucfirst($arr[0] . 'Controller');

            }
        } else {

            $this->controller .= $this->routes['default']['controller'];

        }

        $this->inputMethod = $route[1] ? $route[1] : $this->routes['default']['inputMethod'];
        $this->outputMethod = $route[2] ? $route[2] : $this->routes['default']['outputMethod'];

    }
}