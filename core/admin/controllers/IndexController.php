<?php

namespace core\admin\controllers;

use core\base\controllers\BaseController;
use core\admin\models\Model;

class IndexController extends BaseController{

    protected function inputData()
    {
        $db = Model::instance();

        $table = 'table';
        $color = ['red', 'blue', 'white'];
        $result = $db->get($table, [
            'fields' => ['id', 'name', 'color', 'car'],
            'where' => ['id' => '1,2,3', 'name' => 'Masha', 'color' => $color, 'car' => 'vw'],
            'operand' => ['IN', '%LIKE', 'NOT IN', '<>'],
            'condition' => ['AND', 'OR'],
            'order' => ['id', 'name', 'car'],
            'order_direction' => ['DESC', 'ASC', 'DESC'],
            'limit' => '1'
        ]);

        exit('admin panel');
    }

}