<?php

namespace core\admin\controllers;

use core\base\controllers\BaseController;
use core\admin\models\Model;

class IndexController extends BaseController{

    protected function inputData()
    {
        $db = Model::instance();
        $result = $db->query("SELECT name1 FROM articles");

        exit('admin panel');
    }

}