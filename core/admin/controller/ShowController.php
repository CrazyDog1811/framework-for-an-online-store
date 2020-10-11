<?php


namespace core\admin\controller;


class ShowController extends BaseAdmin
{

    protected function inputData() {

      $this->execBase();

      $this->createTableData();

      $this->createData(['fields' => ['content']]);

      exit();
    }

    protected function outputData() {

    }

}