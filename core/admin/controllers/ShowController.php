<?php

namespace core\admin\controllers;

class ShowController extends BaseAdmin{

    protected function inputData(Type $var = null)
    {
        $this->execBase();
        $this->createTableData();
        $this->createData();
        exit();
    }

    protected function outputData(Type $var = null)
    {
        # code...
    }
}