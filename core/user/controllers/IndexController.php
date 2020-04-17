<?php

namespace core\user\controllers;

use core\base\controllers\BaseController;

class IndexController extends BaseController{
    protected function hello()
    {
        $template = $this->render(false, ['name' => 'Car']);
        $this->page = $template;
        $this->getPage();
    }
}