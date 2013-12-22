<?php
class Database{
	protected $password, $username, $server, $db_name, $connection, $debug;
	public function __construct($server, $db_name, $username, $password) {
		$this->server = $server;
		$this->db_name = $db_name;
		$this->username = $username;
		$this->password= $password;
                $this->debug = false;
	}
        public function setDebug($debug){
            $this->debug = $debug;
        }
	public function getServer(){
		return $this->server;
	}
	public function getUsername(){
		return $this->username;
	}
	public function getDBName(){
		return $this->db_name;
	}
	public function getConnection(){
		return $this->connection;
	}

	public function connect(){
		$this->connection= mysql_connect($this->server, $this->username,$this->password)
		or die('Could not connect: ' . mysql_error());
		mysql_select_db($this->db_name, $this->connection);
	}

	public function close(){
		if($this->connection) mysql_close($this->connection);
	}


        public function lastId(){
            return mysql_insert_id($this->connection);
        }

	public function __destruct() {
		$this->close();
	}

        /**
         * Used like sprintf to run mysql inserts,
         * builds queries with multiple insert lines
         * $sql = "INSERT INTO `table` (`col1`, `col2`)";<br/>
         * $row1 = array($val1, $val2);<br/>
         * $row2 = array($val3, $val4);<br/>
         * $values = array($row1, $row2);<br/>
         * $success = $GLOBALS["DATABASE"]->insertMany($sql, $values);<br/>
         *
         * @param String sql query to run up to 'values'
         * @param [] values array of arrays
         */
        public function insertMany($sql, $values){
            $sql = trim($sql);
            $sql .= " VALUES ";
            if(!is_array($values)){
                return false;
            }
            $valuesCount = count($values);
            for($i=0;$i<$valuesCount; $i++){
                $valueArr = $values[$i];
                if(!is_array($valueArr)){
                    continue;
                }
                if($i!=0) $sql .= ", ";
                $sql .= "(";
                $valueArrCount = count($valueArr);
                for($j=0;$j<$valueArrCount; $j++){
                     if($j!=0) $sql .= ", ";
                     $sql .= "\"".mysql_real_escape_string($valueArr[$j])."\"";
                }
                $sql .= ")";
            }
            $result = mysql_query($sql, $this->connection)
                or die(($this->debug?$query:"Query Failed")."<hr/>".mysql_error());

            return $result;
        }
        public function free($result){
            mysql_free_result($result);
        }

        /**
         * Used like sprintf to run mysql queries,
         * if the result is a single row it returns that,
         * otherwise it returns an array
         * %s for strings, %% for a percentage, %d for digits (integer)
         * %f for a float and %i to make no change to argument (ignore)
         * @param String sql query to run
         * @param [] arguments to pass to query
         */
         public static $queryargs;
         public function query($sql){
            $sql = trim($sql);

            Database::$queryargs = func_get_args();
            //Remove the first argument
            array_shift(Database::$queryargs);
            $query = preg_replace_callback("/%./", create_function('$matches', '
            $type = $matches[0];
            switch($type){
                case "%s":
                    return mysql_real_escape_string(array_shift(Database::$queryargs));
                break;
                case "%i":
                    return array_shift(Database::$queryargs);
                break;
                case "%d":
                    return intval(array_shift(Database::$queryargs),10);
                break;
                case "%f":
                    return floatval(array_shift(Database::$queryargs));
                break;
                case "%%":
                    return "%";
                break;
                default:
                    return "";
            }'), $sql);
            Database::$queryargs = null;
            $result = mysql_query($query, $this->connection)
                or die(($this->debug?$query:"Query Failed")."<hr/>".mysql_error());
            //SELECT, SHOW, DESCRIBE, EXPLAIN

            preg_match("/^[^a-z]*([a-z][a-z]*)[^a-z].*/i", $sql, $matches);
            $type = $matches[1];
            //$type = strtoupper(substr($sql, 0, strpos($sql, " ")));

            switch($type){
                case "SELECT":
                case "SHOW":
                case "DESCRIBE":
                case "EXPLAIN":
                    $objects = array();
                    while($obj = mysql_fetch_object($result)){
                        $objects[] = $obj;
                    }
                    //$objectCount = count($objects);
                    //if($objectCount == 0) return null;
                    //if($objectCount == 1) return $objects[0];
                    return $objects;
                    break;
                default:
                    return ($result);
            }
            $this->free($result);
        }

        public function queryOld($sql){
            $sql = trim($sql);
            $phpString = 'return sprintf($sql';
            $numargs = func_num_args();
            $args = func_get_args();
            if ($numargs > 1) {
                for ($i = 1; $i < $numargs; $i++) {
                    $phpString .= ', mysql_real_escape_string($args['.$i.'])';
                }
            }
            $phpString .= ');';
            $query = eval($phpString);
            $result = mysql_query($query, $this->connection)
                or die(($this->debug?$query:"Query Failed")."<hr/>".mysql_error());
            //SELECT, SHOW, DESCRIBE, EXPLAIN
            //Need to get first alpha char because of parenthesis
            //so can't use
            //$type = strtoupper(substr($sql, 0, strpos($sql, " ")));
            preg_match("/^[^a-z]*([a-z][a-z]*)[^a-z].*/i", $sql, $matches);
            $type = $matches[1];
            //$type = strtoupper(substr($sql, 0, strpos($sql, " ")));

            switch($type){
                case "SELECT":
                case "SHOW":
                case "DESCRIBE":
                case "EXPLAIN":
                    $objects = array();
                    while($obj = mysql_fetch_object($result)){
                        $objects[] = $obj;
                    }
                    //$objectCount = count($objects);
                    //if($objectCount == 0) return null;
                    //if($objectCount == 1) return $objects[0];
                    return $objects;
                    break;
                default:
                    return ($result);
            }
            $this->free($result);
        }

	public function clear(){
		$sql = "SHOW TABLES";
		$conn = $this->getConnection();
		$res = mysql_query($sql, $conn) or die($sql."<hr/>".mysql_error());
		$tables =  array();
		while($row = mysql_fetch_array($res)){
			$tables[] = $row[0];
		}
		mysql_free_result($res);
		$tables2 = array();
		foreach($tables as $tbl){
			$sql = "DROP TABLE IF EXISTS `$tbl`";
			mysql_query($sql, $conn) or ($tables2[] = $tbl);
		}
		$tables = array();
		foreach($tables2 as $tbl){
			$sql = "DROP TABLE IF EXISTS `$tbl`";
			mysql_query($sql, $conn) or ($tables[] = $tbl);
		}
		foreach($tables as $tbl){
			$sql = "DROP TABLE IF EXISTS `$tbl`";
			mysql_query($sql, $conn) or die($sql."<hr/>".mysql_error());
		}
	}
	public function run($source){

	}
}


?>