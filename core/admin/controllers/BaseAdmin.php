<?php

namespace core\admin\controllers;

use core\base\controllers\BaseController;
use core\admin\models\Model;
use core\base\settings\Settings;

abstract class BaseAdmin extends BaseController{

    protected $model;

    protected $table;
    protected $columns;
    protected $data;

    protected $menu;
    protected $title;

    protected function inputData()
    {
        $this->init(true);
        $this->title = 'TITLE';

        if (!$this->model) {
            $this->model = Model::instance();
        }

        if (!$this->menu) {
            $this->menu = Settings::get('projectTables');
        }

        $this->sendNoCacheHeaders();
    }

    protected function outputData()
    {
        # code...
    }

    protected function sendNoCacheHeaders()
    {
        header('Last-Modified: '.gmdate("D, d m Y H:i:s").' GMT');
        header('Cache-Control: no-cache, must-revalidate');
        header('Cache-Control: max-age=0');
        header('Cache-Control: post-check=0, pre-check=0'); 
    }

    protected function execBase(Type $var = null)
    {
        self::inputData();
    }

    protected function createTableData()
    {
        if (!isset($this->table)) {
            if (!empty($this->parameters)) {
                $this->table = array_keys($this->parameters)[0];
            } else {
                $this->table = Settings::get('defaultTable');
            }
        }
        $this->columns = $this->model->showColumns($this->table);
        if (empty($this->columns)) {
            throw new RouteException('Не найдены поля в таблице - '.$this->table, 2);
        }
    }

    protected function createData($arr = [], $add = true)
    {
        $fields = [];
        $order = [];
        $order_direction = [];

        if ($add) {
            if (empty($this->columns['id_row'])) {
                return $this->data = [];
            }
            $fields[] = $this->columns['id_row'].' as id';
            if (!empty($this->columns['name'])) {
                $fileds['name'] = 'name';
            }
            if (!empty($this->columns['img'])) {
                $fileds['img'] = 'name';
            }
            if (count($fileds) < 3) {
                foreach ($this->columns as $key => $value) {
                    if (empty($fileds['name']) && strpos($key, 'name') !== false) {
                        $fileds['name'] = $key.' as name';
                    }
                    if (empty($fileds['img']) && strpos($key, 'img') === 0) {
                        $fileds['img'] = $key.' as img';
                    }
                }
            }
            if (!empty($arr['fields'])) {
                $fields = Settings::instance()->arrayMergeRecursive($fields, $arr['fields']);
            }
            if (!empty($this->columns['parent_id'])) {
                if (!in_array('parent_id', $fields)) {
                    $fields[] = 'parent_id';
                    $order[] = 'parent_id';
                }
            }
            if (!empty($this->columns['menu'])) {
                $order[] = 'menu_position';
            } elseif (!empty($this->columns['date'])) {
                $order[] = 'date';
                if (isset($order)) {
                    $order_direction = ['ASC', 'DESC'];
                } else {
                    $order_direction = ['DESC'];
                }
            }
            if (!empty($arr['order'])) {
                $order = Settings::instance()->arrayMergeRecursive($order, $arr['order']);
            }
            if (!empty($arr['order_direction'])) {
                $order_direction = Settings::instance()->arrayMergeRecursive($order_direction, $arr['order_direction']);
            }
        } else {
            if (empty($arr)) {
                $this->data = [];
            }
            $fields = $arr['fields'];
            $order = $arr['order'];
            $order_direction = $arr['order_direction'];
        }
        $this->data = $this->model->get($this->table, [
            'fields' => $fields,
            'order' => $order,
            'order_direction' => $order_direction
        ]);
        exit;
    }
}