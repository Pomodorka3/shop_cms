<?php

namespace core\base\models;

use core\base\controllers\Singleton;
use core\base\exceptions\DbException;

class BaseModel extends BaseModelMethods{

    use Singleton;
    protected $db;

    private function __construct()
    {
        $this->db = @new \mysqli(HOST, USER, PASS, DB_NAME);
        if ($this->db->connect_error) {
            throw new DbException('Ошибка подключения к БД: '.$this->db->connect_errno.' '.$this->db->connect_error, 0);
        }
        $this->db->query('SET NAMES UTF8');
    }

    final public function query($query, $crud = 'r', $return_id = false)
    {
        $result = $this->db->query($query);

        //if error occurred
        if ($this->db->affected_rows === -1) {
            throw new DbException('Ошибка в SQL запросе: '.$query.' - '.$this->db->errno.' '.$this->db->error);
        }

        switch ($crud) {
            case 'r':
                if ($result->num_rows > 0) {
                    $res = [];
                    for ($i=0; $i < $result->num_rows; $i++) { 
                        $res[] = $result->fetch_assoc();
                    }
                    return $res;
                }
                return false;
                break;
            
            case 'c':
                if ($return_id) {
                    return $this->db->insert_id;
                }
                return true;
                break;

            default:
                return true;
                break;
        }
    }

    /*
        @param $table - db_table
        @param array $set
        'fields' => ['id', 'name'],
        'where' => ['id' => 1, 'name' => 'Masha'],
        'operand' => ['<>', '='],
        'condition' => ['AND'],
        'order' => ['id', 'name'],
        'order_direction' => ['ASC', 'DESC'],
        'limit' => '1'
    */
     
    final public function get($table, $set = [])
    {
        $fields = $this->createFields($set, $table);
        $order = $this->createOrder($set, $table);
        $where = $this->createWhere($set, $table);

        if (empty($where)) {
            $new_where = true;
        } else {
            $new_where = false;
        }

        $join_arr = $this->createJoin($set, $table, $new_where);
        $fields .= $join_arr['fields'];
        $join = $join_arr['join'];
        $where .= $join_arr['where'];
        $fields = rtrim($fields, ',');

        // $order = $this->createOrder($table, $set);

        $limit = array_key_exists('limit', $set) ? 'LIMIT '.$set['limit'] : '';

        $query = "SELECT $fields FROM $table $join $where $order $limit";
        // exit($query);
        return $this->query($query);
    }

    final public function add($table, $set = []){
        $set['fields'] = (array_key_exists('fields', $set) && is_array($set['fields']) && !empty($set['fields'])) ? $set['fields'] : $_POST;
        $set['files'] = (array_key_exists('files', $set) && is_array($set['files']) && !empty($set['files'])) ? $set['files'] : false;
        if (!isset($set['fields']) && !isset($set['files'])) {
            return false;
        }
        $set['return_id'] = array_key_exists('return_id', $set) ? true : false;
        $set['except'] = (array_key_exists('except', $set) && is_array($set['except']) && !empty($set['except'])) ? $set['except'] : [];
        
        $insert_arr = $this->createInsert($set['fields'], $set['files'], $set['except']);

        if (isset($insert_arr)) {
            $query = "INSERT INTO $table ({$insert_arr['fields']}) VALUES ({$insert_arr['values']})";
            return $this->query($query, 'c', $set['return_id']);
        } else {
            throw new DbException('Ошибка при генерации SQL запроса вставки в ДБ');
        }
        return false;
    }

    final public function edit($table, $set = []){
        $set['fields'] = (array_key_exists('fields', $set) && is_array($set['fields']) && !empty($set['fields'])) ? $set['fields'] : $_POST;
        $set['files'] = (array_key_exists('files', $set) && is_array($set['files']) && !empty($set['files'])) ? $set['files'] : false;
        if (!isset($set['fields']) && !isset($set['files'])) {
            return false;
        }
        
        $set['except'] = (array_key_exists('except', $set) && is_array($set['except']) && !empty($set['except'])) ? $set['except'] : [];
        $where = '';
        if (!isset($set['all_rows'])) {
            if (isset($set['where'])) {
                $where = $this->createWhere($set);
            } else {
                $columns = $this->showColumns($table);
                if (!isset($columns)) {
                    return false;
                }
                if (!empty($columns['id_row']) && array_key_exists($columns['id_row'], $set['fields'])) {
                    $where = 'WHERE '.$columns['id_row'].'='.$set['fields'][$columns['id_rows']];
                    unset($set['fields'][$columns['id_rows']]);
                }
            }
        }
        $update = $this->createUpdate($set['fields'], $set['files'], $set['except']);
        $query = "UPDATE $table SET $update $where";
        exit($query);
        return $this->query($query, 'u');
    }

    final public function delete($table, $set){
        $table = trim($table);
        $where = '';
        $where = $this->createWhere($set, $table);
        $columns = $this->showColumns($table);
        if(!isset($columns)) return false;
        if(array_key_exists('fields', $set) && is_array($set['fields']) && !empty($set['fields'])) {
            $fields = [];
            if (!empty($columns['id_row'])) {
                foreach ($set['fields'] as $key => $field) {
                    if ($field === $columns['id_row']) {
                        unset($set['fields'][$key]);
                    } else {
                        $fields[$field] = $columns[$field]['Default'];
                    }
                }
                $update = $this->createUpdate($fields, false, false);
                $query = "UPDATE $table SET $update $where";
            }
        } else {
            $join_arr = $this->createJoin($set, $table);
            $join = $join_arr['join'];
            $join_tables = $join_arr['tables'];
            $query = 'DELETE '.$table.$join_tables.' FROM '.$table.' '.$join.' '.$where;
        }
        exit($query);
        return $this->query($query, 'd');
    }

    final public function showColumns($table)
    {
        $query = "SHOW COLUMNS FROM $table";
        $result = $this->query($query);
        $columns = [];
        if (!empty($result)) {
            foreach ($result as $row) {
                $columns[$row['Field']] = $row;
                if ($row['Key'] === 'PRI') {
                    $columns['id_row'] = $row['Field'];
                }
            }
        }
        return $columns;
    }
}