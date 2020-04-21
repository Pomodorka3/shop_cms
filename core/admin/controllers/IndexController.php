<?php

namespace core\admin\controllers;

use core\base\controllers\BaseController;
use core\admin\models\Model;

class IndexController extends BaseController{

    protected function inputData()
    {
        $db = Model::instance();

        $table = 'table';
        /* $result = $db->get($table, [
            'fields' => ['id', 'name'],
            'where' => ['id' => 1, 'name' => 'Masha'],
            'operand' => ['<>', '='],
            'condition' => ['AND'],
            'order' => ['id', 'name'],
            'order_direction' => ['ASC', 'DESC'],
            'limit' => '1'
        ]); */

        exit('admin panel');
    }

}