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

        $color = ['red', 'white', 'black'];

        $res = $db->get($table, [
            'fields' => ['id', 'name'],
            'where' => ['name' => 'Masha', 'surname' => 'Smirnova', 'fio' => 'Sergeeva', 'car' => 'limuzin', 'color' => $color],
            'operand' => ['IN', 'LIKE%', '<>', '=', 'NOT IN'],
            'condition' => ['AND', 'OR'],
            'order' => ['id', 'name', 'content'],
            'order_direction' => ['ASC', 'DESC'],
            'limit' => ''
        ]);

        exit('I am admin panel');
    }
}