<?php
/**
 * @package framework
 * @subpackage Db
 */

class Db_MySql{
    private $host, $user, $pass, $db_name;
    private $link;
    private $result;

	public $error;
    
	/**
	 * �����������
	 *
	 * @param string $host 
	 * @param string $user 
	 * @param string $password 
	 * @param string $db
	 * @param boolean $persistant
	 * @return void
	 */    
    public function __construct($host, $user, $password, $db, $persistant = true)
    {
		$this->host     = $host;        // ����
		$this->user     = $user;	// ������������
		$this->pass     = $password;	// ������
		$this->db_name  = $db;	        // ���� ������

		$this->connect($persistant);
		return;
    }
	
    
	/**
	 * ����������� � ��
	 *
	 * @param boolean $persist ���������� ����������
	 * @return boolean
	 */
	public function connect($persist = true)
	{
		if ($persist) {
            $link = mysqli_connect('p:' . $this->host, $this->user, $this->pass, $this->db_name);
        } else {
            $link = mysqli_connect($this->host, $this->user, $this->pass, $this->db_name);
        }

		if (!$link)
			trigger_error('Could not connect to the database.', E_USER_ERROR);

		if ($link){
			$this->link = $link;

			mysqli_query($this->link, "SET CHARACTER SET utf8");
			mysqli_query($this->link, "SET NAMES utf8 COLLATE utf8_general_ci");

			return true;
		}

		return false;
	}
    
    /**
    * ��������� ����������
    * @return boolean
    */
    public function close()
    {
        return mysqli_close($this->link);
    }
    

    public function query($query, $noresult = false, &$err = null, $logging = true){
    	
		$this->result = mysqli_query($this->link, $query);

        if ($logging && LOG_DATABASE) {
            global $App;
            $App->WriteLog($query, LOG_MSG_TYPE_DATABASE, '', false);
        }

		if ($this->result == false) {
			//trigger_error('������ ���������� �������: "' . $this->error() . '"<br>' . strval($query));
			$this->error = '������ ���������� �������: "' . $this->error() . '"<br>' . strval($query);
			return false;
		}
		
		if ($noresult) {
			// ���������� ������ ��������� ������� mysql_query ��� �������� ��������, �������
			return $this->result;
		} else {
			// ���������� ���� ������
			if(!GL_CLASS_AUTOLOAD) require_once classPath('Db_MySqlResult');
			return new Db_MySqlResult($this->result);
		}
    }
	

    function queryOne($query, $type = MYSQLI_ASSOC){
		$res = $this->query($query);
		return $res->fetchOne($type);
	}	
	
    function queryRow($query, $type = MYSQLI_ASSOC){
		$res = $this->query($query);
		return $res->fetchRow($type);
	}
	
	// ����� ����� ��� ��������� � ���������� ����������� �������
	// � ������� ����� ������������ ���������� �� ������� (������ � ������� ���������)
	public function getObject($query){
		$res = $this->query($query);
		return $res->fetchObj();	
	}	
	
    public function affectedRows()
    {
        return mysqli_affected_rows($this->link);
    }
	
    public function insertId()
    {
        return mysqli_insert_id($this->link);
    }
	
    public function tableExists($table_name)
    {
        $res = mysqli_query($this->link, "SHOW TABLES FROM `" . $this->db_name . "` LIKE '" . $table_name . "'");
        return (mysqli_num_rows($res) == 1);
    }

	public function queryFields($query = false)
	{
		$res = mysqli_query($this->link, $query);
	
		if(!$res)
			return array();
		
		$fields = array();		
		while($field = mysqli_fetch_field($res))
			$fields[$field->name] = $field;

		return $fields;
	}
    
    
	/**
	 * ���������� ����� ������, � ����������� �� ����������� ���������
	 *
	 * @param mixed $result ������ ������� ��� ����� ������
	 * @return void
	 */
	private function resultCalc(&$result)
	{
		if ($result == false)
			$result = $this->result;
		else {
			if (gettype($result) != 'resource')
				$result = $this->query($result);
		}

		return;
	}    
    
	/**
	 * Get the error description from the last query
	 *
	 * @return string
	 */
	public function error()
	{
		return mysqli_error($this->link);
	}    
}

?>
