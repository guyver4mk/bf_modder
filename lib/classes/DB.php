<?php
/*if (!defined("BFMOD") && BFMOD)
    die("Access Restricted");

require_once(dirname(__FILE__).'/../../includes/configuration.php');
*/
class DB
{

	private $_dbname;
	private $_dbhost;
	private $_dbuser;
	private $_dbpass;
	private $_dbcon;

	private $_error = false;

	public function __construct($hostname = null, $username = null, $password = null, $dbname = null) {
		$this->_dbname = DBNAME;
		$this->_dbhost = DBHOST;
		$this->_dbuser = DBUSER;
		$this->_dbpass = DBPASS;
        
		        if($hostname!==null)
		            $this->_dbhost = $hostname;
		            
		        if($username!==null)
		            $this->_dbuser = $username;
		        
		        if($password!==null)
		            $this->_dbpass = $password;
		        
		        if($dbname!==null)
		            $this->_dbname = $dbname;
		            
		        $opts = array(
					\PDO::ATTR_ERRMODE    => \PDO::ERRMODE_EXCEPTION 
			); 
        
        try
        {
			$this->_dbcon = new PDO('mysql:host='.$this->_dbhost.';dbname='.$this->_dbname.';charset=UTF8',$this->_dbuser, $this->_dbpass, $opts);
			$this->_dbcon->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
        } catch(PDOException $e) {
			$this->_error = $e;
			$this->send_mysql_error('CONNECT',$e);
			return false;
		}
	}
	
	private function send_mysql_error($query,$error)
	{
		$mb = "MySQL Error Thrown running:".PHP_EOL;
		$mb .= $query.PHP_EOL.PHP_EOL;
		$mb .= "Error:".PHP_EOL;
		$mb .= var_export($error,true).PHP_EOL.PHP_EOL;
		
		if(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR']!=="")
			$mb .= "IP Address:".$_SERVER['REMOTE_ADDR'];
		else
			$mb .= "IP Address not detected (Cron?)";
		
	}
	
	public function query($sql,$params = array()) {
		try {
			$stmt = $this->_dbcon->prepare($sql);
			if(!empty($params)) {
				foreach($params as $param) {
					if($param['type']==="int") {
						$stmt->bindParam($param['name'],$param['value'],PDO::PARAM_INT);
					} else {
						$stmt->bindParam($param['name'],$param['value'],PDO::PARAM_STR);
					}
				}
			}
			$stmt->execute();
			return $stmt->fetchAll(PDO::FETCH_ASSOC);
		} catch(PDOException $e) {
			$this->_error = $e;
			$this->send_mysql_error($sql,$e);
			return false;
		}
	}
	
	public function count($sql,$params = array()) {
		try {
			$stmt = $this->_dbcon->prepare($sql);
			if(!empty($params)) {
				foreach($params as $param) {
					if($param['type']==="int") {
						$stmt->bindParam($param['name'],$param['value'],PDO::PARAM_INT);
					} else {
						$stmt->bindParam($param['name'],$param['value'],PDO::PARAM_STR);
					}
				}
			}
			
			$stmt->execute();
			return count($stmt->fetchAll(PDO::FETCH_ASSOC));
		} catch(PDOException $e) {
			$this->_error = $e;
			//$this->send_mysql_error($sql,$e);
			return 0;
		}
	}
	
	public function apply($sql, $return_id=false, $params = array()) {
		try {
			$stmt = $this->_dbcon->prepare($sql);
			if(!empty($params)) {
				foreach($params as $param) {
					if($param['type']==="int") {
						$stmt->bindParam($param['name'],$param['value'],PDO::PARAM_INT);
					} else {
						$stmt->bindParam($param['name'],$param['value'],PDO::PARAM_STR);
					}
				}
			}
			$stmt->execute();
			if($return_id===true)
				return $this->_dbcon->lastInsertId();
			return true;
		} catch(PDOException $e) {
            $this->_error = $e;
            //$this->send_mysql_error($sql,$e);
			return false;
		}
	}

	public function error()
	{
		var_dump($this->_error);
	}
}