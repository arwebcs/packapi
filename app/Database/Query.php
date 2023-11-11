<?php

namespace App\Database;

use PDO;
use PDOException;

class Query extends Database
{
    protected static $table = '';
    protected static $sql = '';
    protected static $paramValues = [];

    public function __construct($sql = '')
    {
        self::$sql = $sql;
        parent::__construct();
    }

    public static function setParamValues($values)
    {
        self::$paramValues = array_merge(self::$paramValues, $values);
    }

    public static function getParamValues()
    {
        return self::$paramValues;
    }

    public static function table($table)
    {
        self::$paramValues = [];
        self::$sql = "";
        self::$table = $table;
        return new Query('');
    }

    public static function customQuery($sql, $fields_values = [])
    {
        self::$sql = $sql;
        self::setParamValues($fields_values);
        return new Query(self::$sql);
    }

    public static function select($fields = "*")
    {
        self::$sql .= 'SELECT ' . $fields . ' FROM ' . self::$table . ' ';
        return new Query(self::$sql);
    }

    public static function join($joinTable, $type, $condition)
    {
        self::$sql .= $type . ' ' . $joinTable . ' ON ' . $condition . ' ';
        return new Query(self::$sql);
    }

    public static function where($statement, $params = [])
    {
        $parameters[] = $params;
        self::$sql .= 'WHERE ' . $statement . ' ';
        self::setParamValues(array_merge(...$parameters));
        return new Query(self::$sql);
    }

    public static function limit($fields_values = [])
    {

        if (empty($fields_values['limit']) && $fields_values['limit'] !== 0) {
            $limit = "";
        } else {
            $limit = $fields_values['limit'] < 0 ? "" : $fields_values['limit'];
        }

        if (empty($fields_values['start']) && $fields_values['start'] !== 0) {
            $start = "";
        } else {
            $start = (($fields_values['start']) - 1) * $limit;
        }


        if (empty($start) && $start !== 0 && empty($limit) && $limit !== 0) {
            self::$sql .= ' ';
        } else {
            if (empty($limit) && $limit  !== 0) {
                self::$sql .= 'LIMIT ' . $limit  . ' ';
            } else {
                self::$sql .= 'LIMIT ' . $start  . ', ' . $limit . ' ';
            }
        }
        return new Query(self::$sql);
    }

    public static function order($field_orderType = '')
    {
        self::$sql .= "ORDER BY $field_orderType ";
        return new Query(self::$sql);
    }

    public static function group($fields)
    {
        self::$sql .= "GROUP BY $fields ";
        return new Query(self::$sql);
    }

    public static function having($fieldName, $operator, $value)
    {
        self::$sql .= "HAVING COUNT($fieldName) $operator ? ";
        self::setParamValues([$value]);
        return new Query(self::$sql);
    }

    public static function insert($fields_values)
    {
        self::$sql = '';
        $insertValues = "";
        asort($fields_values);
        foreach ($fields_values as $values) {
            $insertValues .= "?,";
        }
        self::$sql .= 'INSERT INTO ' . self::$table . '(' . implode(', ', array_keys($fields_values)) . ') 
                      VALUES(' . trim($insertValues, ",") . ')';
        self::setParamValues(array_values($fields_values));
        return new Query(self::$sql);
    }

    public static function update($fields_values)
    {
        self::$sql = '';
        $updateValues = "";
        asort($fields_values);
        foreach ($fields_values as $keys => $values) {
            $updateValues .= $keys . "=?, ";
        }
        self::$sql .= 'UPDATE ' . self::$table . ' SET ' . trim($updateValues, ", ") . ' ';
        self::setParamValues(array_values($fields_values));
        return new Query(self::$sql);
    }

    public static function delete()
    {
        self::$sql = '';
        self::$sql .= 'DELETE FROM ' . self::$table . ' ';
        return new Query(self::$sql);
    }

    public static function count()
    {
        try {
            $stmt = parent::getConnection()->prepare(self::$sql);
            $stmt->execute(self::getParamValues());
            $numRows = $stmt->rowCount();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }

        return $numRows;
    }

    public static function get()
    {
        try {
            $stmt = parent::getConnection()->prepare(self::$sql);
            $stmt->execute(self::getParamValues());
            $retData = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo $e->getMessage();
        }

        return $retData;
    }

    public static function single($key = "")
    {
        try {
            $stmt = parent::getConnection()->prepare(self::$sql);
            $stmt->execute(self::getParamValues());
            $retData = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo $e->getMessage();
        }

        return $key == "" ? $retData : $retData[$key];
    }

    public static function save()
    {
        try {
            $stmt = parent::getConnection()->prepare(self::$sql);
            $retVal = $stmt->execute(self::getParamValues());
        } catch (PDOException $e) {
            echo $e->getMessage();
        }

        return $retVal;
    }

    public static function showQuery()
    {
        die(self::$sql);
    }

    public function __destruct()
    {
        parent::__destruct();
    }
}
