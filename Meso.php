<?php
namespace Meso;
require_once ("Base.php");
class Meso extends Base
{
    public function __construct($servername, $username, $password, $dbname)
    {
        $this->servername = $servername;
        $this->username = $username;
        $this->password = $password;
        $this->dbname = $dbname;
        $this->settings = parse_ini_file("conf.ini");
    }

    static public function formatString(array $array, string $key_or_item = "item", bool $escape = false)
    {
        $values = "";
        $thing = "item";
        if($key_or_item == "key")
        {
            $thing = "key";
        }
        foreach($array as $key=>$item)
        {
            if($escape && gettype($item) == "string")
            {
                $item = "'{$item}'";
            }
            if($key === array_key_first($array))
            {
                $values = $values."(".$$thing.", ";
            }
            if($key === array_key_last($array))
            {
                $values = $values.$$thing.")";
            }
            elseif($key !== array_key_first($array))
            {
                $values = $values.$$thing.", ";
            }
        }
        return $values;
    }

    static public function andFormat(array $array, string $join_word = "AND")
    {
        $values = "";
        foreach($array as $key=>$item)
        {
            if($key !== array_key_last($array))
            {
                if(gettype($item) == "string")
                {
                    $values = $values."{$key} = '{$item}' {$join_word} ";
                    continue;
                }
                $values = $values."{$key} = {$item} {$join_word} ";
            }
            else
            {
                if(gettype($item) == "string")
                {
                    $values = $values."{$key} = '{$item}'";
                    break;
                }
                $values = $values."{$key} = {$item}";
            }
        }
        return $values;
    }

    static public function getAttributes(object $object)
    {
        $object = (array) $object;
        $key_value_pair = array();
        $values_keys = self::formatString($object, "key");
        $values_items = self::formatString($object, "item", true);
        array_push($key_value_pair, $values_keys);
        array_push($key_value_pair, $values_items);
        return $key_value_pair;
    }

    public function getRecords(string $table, object $object)
    {
        // $isStatic = !(isset($this) && get_class($this) == __CLASS__);
        if(!preg_match("/[`]/", $table))
        {
            $table = "`".$table."`";
        }
        $object = (array) $object;
        $values = self::andFormat($object);
        $query = "SELECT * FROM {$table} WHERE {$values}";
        $resquery = $this->connection->query($query);
        $results = array();
        while($row = mysqli_fetch_assoc($resquery))
        {
            array_push($results, $row);
        }
        return $results;
    }

    public function insert(string $table, object $object)
    {
        if(!preg_match("/[`]/", $table))
        {
            $table = "`".$table."`";
        }
        $keys = self::getAttributes($object)[0];
        $values = self::getAttributes($object)[1];
        $query = "INSERT INTO {$table} {$keys} VALUES 
		{$values}";
        print_r($query);
        return  $this->query($query);
    }

    public function delete(string $table, object $object)
    {
        if(!preg_match("/[`]/", $table))
        {
            $table = "`".$table."`";
        }
        $object_array = (array) $object;
        $rowID = self::andFormat($object_array);
        $query = "DELETE FROM {$table} WHERE {$rowID}";
        return $this->query($query);
    }

    public function readOne(string $table, object $object)
    {
        if(!preg_match("/[`]/", $table))
        {
            $table = "`".$table."`";
        }
        $object_array = (array) $object;
        $rowID = self::andFormat($object_array);
        $query = "SELECT * FROM {$table} WHERE {$rowID}";
        $resquery = $this->query($query);
        if($resquery == false)
        {
            return false;
        }
        $row = mysqli_fetch_assoc($resquery);
        if($row == false || $row == null || count($row) == 0)
        {
            return false;
        }
        return $row;
    }

    public function readList(string $table, int $offset = 0, int $limit = 10, $order = false, object $where = null, bool $IsLike = false)
    {
        if(!preg_match("/[`]/", $table))
        {
            $table = "`".$table."`";
        }
        $query = "SELECT * FROM {$table}";

        if($where != null) {
            $where = (array)$where;
            $rowID = self::andFormat($where);
            $query .= " WHERE {$rowID}";
        }

        if($order) {
            $query .= " ORDER BY {$order}";
        }

        $query .= " LIMIT {$limit} OFFSET {$offset}";
//        if($offset == 0)
//    	{
//    		$query = "SELECT * FROM {$table}";
//    	}
        print_r($query);
        $resquery = $this->query($query);
        if(!$resquery)
            return false;
        return $resquery;
//		$values = "";
//		foreach($rows as $key=>$row)
//		{
//			$temp = self::formatString($row);
//			$values = $values.$temp;
//			if($key !== array_key_last($rows))
//			{
//				$values = $values.", ";
//			}d
//		}
    }

    public function insertMultiple(string $table, array $rows)
    {
        if(!preg_match("/[`]/", $table))
        {
            $table = "`".$table."`";
        }
        if(count($rows) == 0)
        {
            return 0;
        }
        $fieldnames = array_keys($rows[0]);
        $names = "(";
        $names = self::formatString($fieldnames);
        $values = "";
        foreach($rows as $key=>$row)
        {
            $temp = self::formatString($row, "item", true);
            $values  = $values.$temp;
            if($key !== array_key_last($rows))
            {
                $values = $values.", ";
            }
        }
        $query = "INSERT INTO {$table} {$names} VALUES {$values}";
        if(strlen($query) > 999999)
        {
            $half2 = ceil(count($rows)/2);
            $halves = array_chunk($rows, $half2);
            $this->insertMultiple($table, $halves[0]);
            $this->insertMultiple($table, $halves[1]);
            unset($GLOBALS['rows']);
        }
        $this->connection->query($query);
    }

    public function update(string $table, object $object, object $where)//not done yet.
    {
        if(!preg_match("/[`]/", $table))
        {
            $table = "`".$table."`";
        }
        $object_array = (array) $object;
        $where = (array) $where;
        $values = self::andFormat($object_array, ", ");
        $rowID = self::andFormat($where);
        $query = "UPDATE {$table} SET {$values} WHERE {$rowID}";
        print_r($query);
        return $this->query($query);
    }

//    public function RunQuery(string $table, object $object, $Query)
//    {
//        if(!preg_match("/[`]/", $table))
//        {
//            $table = "`".$table."`";
//        }
//        $object_array = (array) $object;
//        $rowID = self::andFormat($object_array);
//        $query = "SELECT * FROM {$table} WHERE {$rowID}";
//        $resquery = $this->connection->query($query);
//        if($resquery == false)
//        {
//            return false;
//        }
//        $results = array();
//        while($row = mysqli_fetch_assoc($resquery))
//        {
//            array_push($results, $row);
//        }
//        if(count($results) == 0)
//        {
//            return 0;
//        }
//        $values = "";
//        foreach($results as $key=>$item)
//        {
//            $temp = self::formatString($item);
//            $values = $values.$temp;
//            if($key !== array_key_last($results))
//            {
//                $values = $values.", ";
//            }
//        }
//        return $values;
//    }

}




?>