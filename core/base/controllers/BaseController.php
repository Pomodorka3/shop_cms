<?php

namespace core\base\controllers;

use core\base\exceptions\RouteException;

abstract class BaseController{

    protected $page;
    protected $errors;

    protected $controller;
    protected $inputMethod;
    protected $outputMethod;
    protected $parameters;

    public function route(){
        $controller = str_replace('/', '\\', $this->controller);

        try {
            $object = new \ReflectionMethod($controller, 'request');

            $args = [
                'parameters' => $this->parameters,
                'inputMethod' => $this->inputMethod,
                'outputMethod' => $this->outputMethod,
            ];
    
            $object->invoke(new $controller, $args);   
        } catch (\ReflectionException $e) {
            throw new RouteException($e->getMessage());
        }
    }

    public function request($args){
        $args->parameters = $args['parameters'];
        
        $inputData = $args['inputData'];
        $outputData = $args['outputData'];

        $this->$inputData();
        $this->page = $this->$outputData();
    }
}