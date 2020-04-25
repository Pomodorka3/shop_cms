<?php
namespace core\base\settings;

use core\base\controllers\Singleton;

class Settings{

    use Singleton;

    private $routes = [
        'admin' => [
            'alias' => 'admin',
            'path' => 'core/admin/controllers/',
            'hrUrl' => false,
            'routes' => [

            ]
        ],
        'settings' => [
            'path' => 'core/base/settings/'
        ],
        'plugins' => [
            'path' => 'core/plugins/',
            'hrUrl' => false,
            'dir' => false
        ],
        'user' => [
            'path' => 'core/user/controllers/',
            'hrUrl' => true,
            'routes' => [
                'site' => 'index/hello'
            ]
        ],
        'default' => [
            'controller' => 'IndexController',
            'inputMethod' => 'inputData',
            'outputMethod' => 'outputData'
        ]
    ];

    private $defaultTable = 'articles';
    private $projectTables = 'articles';

    private $templateArr = [
        'text' => ['name', 'adress', 'phone'],
        'textarea' => ['content', 'keywords']
    ];

    static public function get($property){
        return self::instance()->$property;
    }

    public function clueProperties($class){
        $baseProperties = [];
        foreach ($this as $name => $item) {
            $property = $class::get($name);
            
            
            if (is_array($property) && is_array($item)) {
                $baseProperties[$name] = $this->arrayMergeRecursive($this->$name, $property);
                continue;
            }

            if (!$property) {
                $baseProperties = $this->$name;
            }
        }

        return $baseProperties;
    }

    public function arrayMergeRecursive(){
        $arrays = func_get_args();

        $base = array_shift($arrays);

        foreach ($arrays as $array) {
            foreach ($array as $key => $value) {
                if (is_array($value) && is_array($base[$key])) {
                    $base[$key] = $this->arrayMergeRecursive($base[$key], $value);
                } else {
                    if (is_int($key)) {
                        if (!in_array($value, $base)) {
                            array_push($base, $value);
                            continue;
                        }
                    }
                    $base[$key] = $value;
                }
            }
        }
        return $base;
    }
}