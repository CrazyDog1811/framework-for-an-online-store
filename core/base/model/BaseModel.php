<?php


namespace core\base\model;


use core\base\controller\Singleton;
use core\base\exceptions\DbException;

class BaseModel
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
}