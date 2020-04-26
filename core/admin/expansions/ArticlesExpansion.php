<?php

namespace core\admin\expansions;

use core\base\controllers\Singleton;
use core\admin\controllers\BaseAdmin;

class ArticlesExpansion extends BaseAdmin
{
    use Singleton;

    public function expansion($args = [])
    {
        var_dump($args->data);
    }
}
