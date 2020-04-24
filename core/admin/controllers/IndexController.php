<?php

namespace core\admin\controllers;

use core\base\controllers\BaseController;
use core\admin\models\Model;

class IndexController extends BaseController{

    protected function inputData()
    {
        $db = Model::instance();

        $table = 'articles';
        $color = ['red', 'blue', 'white'];

        // $files['gallery_img'] = ['asd.jpg', 'mom.png', 'dad.jpeg'];
        $files['gallery_img'] = ['asd.jpg', 'ooooo.jpg'];
        $files['img'] = ['main.jpg'];

        // $result = $db->add($table, [
        //     'fields' => ['name' => 'Olga', 'content' => 'hello'],
        //     // 'except' => ['name'],
        //     'files' => $files
        // ]);
        $result = $db->edit($table, [
            'fields' => ['name' => 'asd'],
            'files' => $files,
            'where' => ['id' => 1]
        ]);
        /* $result = $db->get($table, [
            'fields' => ['id', 'name', 'color', 'car'],
            'where' => ['id' => '1', 'name' => 'Masha'],
            // 'operand' => ['IN', '%LIKE'],
            // 'condition' => ['AND', 'OR'],
            'order' => ['id'],
            'order_direction' => ['DESC'],
            'limit' => '1',
            'join' => [
                [
                    'table' => 'join_table1',
                    'fields' => ['id as j_id, name as j_name'],
                    'join_type' => 'left',
                    'where' => ['name' => 'sasha'],
                    'operand' => ['='],
                    'condition' => ['OR'],
                    'on' => [
                        'table' => 'teachers',
                        'fields' => ['id', 'parent_id']
                    ],
                    'group_condition' => 'AND'
                ],
                'join_table2' => [
                    'table' => 'join_table2',
                    'fields' => ['id as j2_id, name as j2_name'],
                    'join_type' => 'right',
                    'where' => ['name' => 'sasha'],
                    'operand' => ['='],
                    'condition' => ['OR'],
                    'on' => ['id1', 'parent_id2']
                ]
            ]
        ]); */

        exit('admin panel');
    }

}