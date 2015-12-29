<?php
class Db_MySqlResult
{
    private $result;
    
    public function __construct($result){
        $this->result = $result;
    }

    public function result(){
        return $this->result;
    }
    
    public function fetchRow($type = MYSQLI_ASSOC){
		return mysqli_fetch_array($this->result, $type);
	}    
    
    public function fetchOne(){
		list($res) = $this->fetchRow(MYSQLI_NUM);
		return $res;
	}
	
	public function fetchObj(){
		return mysqli_fetch_object($this->result);
	}

	public function fetchAsObj($class_name){
		//$obj = new $class_name;
		//$obj->_loadFromDb();
		// Создадим наш объект, заполним его вернем
		//return mysql_fetch_object($this->result);
	}
	
    public function numRows(){
        return mysqli_num_rows($this->result);
    }
    
    public function dataSeek($row_num){
        return mysqli_data_seek($this->result, $row_num);
    }

    public function asArray()
    {
        $result = [];
        while($row = $this->fetchRow())
        {
            $arr = [];
            foreach ($row as $key => $val)
            {
                $arr = array_merge($arr, [$key => $val]);
            }
            $result[] = $arr;
        }

        return $result;
    }
}
?>