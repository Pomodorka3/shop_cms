<?php

namespace core\user\controllers;

use core\base\controllers\BaseController;

class IndexController extends BaseController{

    protected $name;

    // protected function hello()
    // {
    //     $template = $this->render(false, ['name' => 'Car']);
    //     $this->page = $template;
    //     $this->getPage();
    // }

    protected function inputData()
    {
        $name = 'Masha';
        $content = $this->render('', compact('name'));
        $header = $this->render(TEMPLATE.'header');
        $footer = $this->render(TEMPLATE.'footer');
        
        return compact('header', 'content', 'footer');
    }

    protected function outputData()
    {
        $vars = func_get_arg(0);
        return $vars;
    }
}