<?php


namespace core\base\settings;

use core\base\settings\Settings;


class ShopSettings {

    static private $_instance;
    private $baseSettings;

    private $routes = [
        'plugins' => [
            'dir' => false,
            'routes' => [

            ]
        ],
    ];

    private $templateArr = [
        'text' => ['price', 'short'],
        'textarea' => ['goods_content']
    ];

    private function __construct() {}

    private function __clone() {}

    static public function get($property) { // метод для получения свойств класса singleton
        return self::instance()->$property;
    }

    static public function instance() {
        if(self::$_instance instanceof self) {
            return self::$_instance;
        }
        self::$_instance = new self();
        self::$_instance->baseSettings = Settings::instance(); // в свойство данного класса(baseSettings) кладём ссылку на объект класса Settings
        $baseProperties = self::$_instance->baseSettings->clueProperties(get_class()); // к настройкам класса Settings добавляем настройки данного класса(get_class())
        self::$_instance->setProperty($baseProperties);

        return self::$_instance;
    }

    protected function setProperty($properties) {
        if ($properties) {
            foreach ($properties as $name => $property) {
                $this->$name = $property;
            }
        }
    }
}