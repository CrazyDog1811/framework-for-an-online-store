<?php


namespace core\admin\controller;


use core\base\controller\BaseController;
use core\admin\model\Model;

class IndexController extends BaseController
{

    protected function InputData()
    {

        $db = Model::instance();

        $table = 'teachers';

        $files['gallery_img'] = ["red''.jpg", 'white.png', 'black.jpg'];
        $files['img'] = 'main_img.jpg';

        $_POST['name'] = 'Brunkevich';

        $res = $db->add($table, [
          //  'fields' => ['name' => 'Dazdraperma Kozlyakova', 'content' => 'Hello forever'],

        ]);

        exit('id = ' . $res['id'] . ' name = ' . $res['name']);
    }
}