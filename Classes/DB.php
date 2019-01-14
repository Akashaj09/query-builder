<?php /** @noinspection SqlDialectInspection */

namespace Classes;
class DB extends \PDO
{
    private $table;
    private $query;
    private $select = "*";
    private $wheres = [];
    private $orWheres = [];
    private $bindings = [];
    private $joins = "";
    public function __construct($table)
    {
        $dsn = "mysql:dbname=api;host=127.0.0.1";
        $username = "root";
        $password = "";
        $options = [\PDO::ATTR_PERSISTENT => false, \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_OBJ];
        $this->table = $table;
        parent::__construct($dsn, $username, $password, $options);
    }
    public function all(){
        $sql = "SELECT * FROM $this->table WHERE 1";
        $sql = $this->prepare($sql);
        $sql->execute();
        return $sql->fetchAll();
    }
    public function create(array $data){
        $keys = array_keys($data);
        $sql = "INSERT INTO ".$this->table."(".implode(", ", $keys).") VALUES(".':'.implode(', :', $keys).")";
        $statement = $this->prepare($sql);
        foreach ($keys as $key){
            $statement->bindParam(":$key", $data[$key]);
        }

        return $statement->execute();
    }

    /**
     * added where clause in query builder
     * @param $field
     * @param $operator
     * @param $value
     * @return DB
     */
    public function where($field, $operator, $value){
        array_push($this->wheres, $field." ".$operator." ?");
        array_push($this->bindings, $value);
        return $this;
    }

    /**
     * added where clause in query builder
     * @param $field
     * @param $operator
     * @param $value
     * @return DB
     */
    public function orWhere($field, $operator, $value){
        array_push($this->orWheres, $field." ".$operator." ?");
        array_push($this->bindings, $value);
        return $this;
    }

    public function select($columns){
        $this->select = $columns;
        return $this;
    }
    /**
     *merge all where clause as like query
     */
    private function mergeWhere(){
        $wheres = $this->wheres;
        if (count($wheres) == 1)
            return $wheres[0];
        $whereString = "";
        foreach ($wheres as $where){
            $whereString .= $where." AND ";
        }
        return rtrim($whereString, " AND ");
    }

    /**
     *merge all where clause as like query
     */
    private function mergeOrWhere(){
        $wheres = $this->orWheres;
        if (count($wheres) == 1)
            return $wheres[0];
        $whereString = "";
        foreach ($wheres as $where){
            $whereString .= $where." OR ";
        }
        return rtrim($whereString, " OR ");
    }

    //get all value from database depend on query
    public function get(){
        $this->query = "SELECT ".$this->select." FROM $this->table WHERE ";
        $wheres = $this->mergeWhere()." OR ".$this->mergeOrWhere();
        $query = $this->query.$wheres;
        $statement = $this->prepare($query);
        $statement->execute($this->bindings);
        return $statement->fetchAll();
    }
}