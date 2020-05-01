<?php

namespace core\admin\controllers;

use core\base\settings\Settings;

class AddController extends BaseAdmin
{
    
    protected function inputData()
    {
        if(isset($this->userId)) $this->execBase();
        $this->createTableData();
        $this->createForeignData();
        $this->createMenuPosition();
        $this->createRadio();
        $this->createOutputData();
        $this->model->showForeignKeys($this->table);
    }

    private function createForeignProperty($arr, $rootItems)
    {
        if (in_array($this->table, $rootItems['tables'])) {
            $this->foreignData[$arr['COLUMN_NAME']][0]['id'] = 0;
            $this->foreignData[$arr['COLUMN_NAME']][0]['name'] = $rootItems['name'];
        }
        $columns = $this->model->showColumns($arr['REFERENCED_TABLE_NAME']);
        $name = '';
        $where = '';
        $operand = '';
        if (!empty($columns['name'])) {
            $name = 'name';
        } else {
            foreach ($columns as $key => $value) {
                if (strpos($key, 'name') !== false) {
                    $name = $key.' as name';
                }
            }
            if (empty($name)) {
                $name = $columns['id_row'].' as name';
            }
        }
        if (!empty($this->data)) {
            if($arr['REFERENCED_TABLE_NAME'] === $this->table) {
                $where[$this->columns['id_row']] = $this->data[$this->columns['id_row']];
                $operand[] = '<>';
            }
        }
        $foreign = $this->model->get($arr['REFERENCED_TABLE_NAME'], [
            'fields' => [$arr['REFERENCED_COLUMN_NAME'].' as id', $name],
            'where' => $where,
            'operand' => $operand
        ]);
        if (!empty($foreign)) {
            if (!empty($this->foreignData[$arr['COLUMN_NAME']])) {
                foreach ($foreign as $value) {
                    $this->foreignData[$arr['COLUMN_NAME']][] = $value;
                }
            } else {
                $this->foreignData[$arr['COLUMN_NAME']][] = $foreign;
            }
        }
    }

    protected function createForeignData($settings = false)
    {
        if (!$settings) $settings = Settings::instance();
        $rootItems = $settings::get('rootItems');
        $keys = $this->model->showForeignKeys($this->table);
        if (!empty($keys)) {
            foreach ($keys as $item) {
                $this->createForeignProperty($item, $rootItems);
            }
        } elseif (!empty($this->columns['parent_id'])) {
            $arr['COLUMN_NAME'] = 'parent_id';
            $arr['REFERENCED_COLUMN_NAME'] = $this->columns['table_name'];
            $arr['REFERENCED_TABLE_NAME'] = $this->table;
            $this->createForeignProperty($arr, $rootItems);
        }
        return;
    }

    protected function createMenuPosition($settings = false){
        $where = '';
        if(!empty($this->columns['menu_position'])){
            if (!$settings) $settings = Settings::instance();
            $rootItems = $settings::get('rootItems');
            if (!empty($this->columns['parent_id'])) {
                if (in_array($this->table, $rootItems['tables'])) {
                    $where = 'parent_id IS NULL OR parent_id = 0';
                } else {
                    $parent = $this->model->showForeignKeys($this->table, 'parent_id')[0];
                    if (isset($parent)) {
                        if ($this->table === $parent['REFERENCED_TABLE_NAME']) {
                            $where = 'parent_id IS NULL OR parent_id = 0';
                        } else {
                            $columns = $this->model->showColumns($parent['REFERENCED_TABLE_NAME']);
                            if (array_key_exists('parent_id', $columns) && !empty($columns['parent_id'])) {
                                $order[] = 'parent_id';
                            } else {
                                $order[] = $parent['REFERENCED_COLUMN_NAME'];
                            }
                            $id = $this->model->get($parent['REFERENCED_TABLE_NAME'], [
                                'fields' => $parent['REFERENCED_COLUMN_NAME'],
                                'order' => $order,
                                'limit' => '1'
                            ]);
                            if (isset($id)) {
                                $where = ['parent_id' => $id];
                            }
                        }
                    } else {
                        $where = 'parent_id IS NULL OR parent_id = 0';
                    }
                }
            }
            $menu_pos = $this->model->get($this->table, [
                'fields' => ['COUNT(*) as count'],
                'where' => $where,
                'no_concat' => true
            ])[0]['count'] + 1;
            for ($i=1; $i <= $menu_pos ; $i++) { 
                $this->foreignData['menu_position'][$i-1]['id'] = $i;
                $this->foreignData['menu_position'][$i-1]['name'] = $i;
            }
        }
        return;
    }
}
