<?php


namespace core\base\model;


use core\base\controller\Singleton;
use core\base\exceptions\DbException;

class BaseModel extends BaseModelMethods
{

    use Singleton;

    protected $db;

    private function __construct()
    {

        $this->db = @new \mysqli(HOST, USER, PASS, DB_NAME);

        if ($this->db->connect_error) {

            throw new DbException(' *** incorrect connection to the Data base: code '
                . $this->db->connect_errno . ' *** ' . $this->db->connect_error); // вывести код ошибки и сообщение об ошибке

        }

        $this->db->query('SET NAMES UTF8'); // запрос на установку соединения

    }

    /**
     * @param $query
     * @param string $crud = 'r' - SELECT / 'c' - INSERT / 'u' - UPDATE / 'd' - DELETE
     * @param false $return_id
     * @return array|bool|mixed
     * @throws DbException
     */
    final public function query($query, $crud = 'r', $return_id = false)
    {

        $result = $this->db->query($query); // присвоить объект с результатом запроса($query) методом query, объекта mysqli

        if ($this->db->affected_rows === -1) { // если запрос вернул ошибку
            throw new DbException('*** Error in the SQL-query: '
                . $query . ' code is - ' . $this->db->errno . ' *** ' . $this->db->error); // код ошибки(erno), сообщение об ошибке(error)

        }

        switch ($crud) {

            case 'r':

                if ($result->num_rows) { // если что-то пришло в result

                    $res = [];

                    for ($i = 0; $i < $result->num_rows; $i++) {
                        $res[$i] = $result->fetch_assoc(); // метод возвращает массив каждого ряда выборки из запроса, который храниться в объекте result
                    }
                    return $res;

                }

                return false;

            case 'c':

                if ($return_id) return $this->db->insert_id; //

                return true;

            default:

                return true;

        }

    }

    /**
     * @param $table - таблицы базы данных
     * @param array $set -
     *  'fields' => ['id', 'name'],
     *  'where' => ['fio' => 'Sergeeva', 'name' => 'Masha', 'surname' => 'Smirnova'],
     *  'operand' => ['=', '<>'],
     *  'condition' => ['AND'],
     *  'order' => ['fio', 'name', 'surname'],
     *  'order_direction' => ['ASC', 'DESC'],
     *  'limit' => '1',
     *  'join' => [
     *     [
     *        'table' => 'join_table1',
     *        'fields' => 'id as j_id, name as j_name',
     *        'type' => 'left',
     *        'where' => ['name' => 'Sasha'],
     *        'operand' => ['='],
     *        'condition' => ['OR'],
     *        'on' => ['id', 'parent_id'],
     *        'group_condition' => 'AND',
     *     ],
     *     'join_table1' => [
     *         'fields' => 'id as j2_id, name as j2_name',
     *         'type' => 'left',
     *         'where' => ['name' => 'Sasha'],
     *         'operand' => ['='],
     *         'condition' => ['AND'],
     *         'on' => [
     *            'table' => 'teachers',
     *            'fields' => ['id', 'parent_id'],
     *         ],
     *     ],
     *  ]
     */

    final public function get($table, $set = [])
    {

        $fields = $this->createFields($set, $table);

        $order = $this->createOrder($set, $table);

        $where = $this->createWhere($set, $table);

        if (!$where) $new_where = true;
        else $new_where = false;

        $join_arr = $this->createJoin($set, $table, $new_where);

        $fields .= $join_arr['fields'];
        $join = $join_arr['join'];
        $where .= $join_arr['where'];

        $fields = rtrim($fields, ',');

        $limit = $set['limit'] ? 'LIMIT ' . $set['limit'] : '';

        $query = "SELECT $fields FROM $table $join $where $order $limit";

        return $this->query($query);
    }

    /**
     * @param $table - таблица для вставки данных
     * @param array $set - массив параметров:
     * fields => [поле => значение]; - если не указан, то обрабатывается $_POST[поле => значение]
     * разрешена передача например NOW() в качестве MySql функции обычной строкой
     * files => [поле => значение]; - можно подать массив вида [поле => [массив значений]]
     * except => ['исключение 1', 'исключение 2'] - исключает данные элементы массива из       добавленных в запрос
     * return_id => true | false - возвращать или нет идентификатор вставленной записи
     *@return mixed
     */
    final public function add($table, array $set) {

       $set['fields'] = (is_array($set['fields']) && !empty($set['fields'])) ? $set['fields'] : $_POST;
       $set['files'] = (is_array($set['files']) && !empty($set['files'])) ? $set['files'] : false;

       if (!$set['fields'] && !$set['files']) return false;

       $set['except'] = (is_array($set['except']) && !empty($set['except'])) ? $set['except'] : false;
       $set['return_id'] = $set['return_id'] ? true : false;

       $insert_arr = $this->createInsert($set['fields'], $set['files'], $set['except']);

       if ($insert_arr) {

           $query = "INSERT INTO teachers ({$insert_arr['fields']}) VALUES ({$insert_arr['values']})";

           return $this->query($query, 'c', $set['return_id']);
       }

       return false;

    }
}