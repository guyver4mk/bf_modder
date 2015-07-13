<?php

class OCIDB
{

	private $_db;
	private $_dbuser;
	private $_dbpass;
	private $_dbcon;

	private $_error = false;

	public function __construct($username = null, $password = null, $db = null) {
		$this->_db 	   = LIVE_DBNAME;
		$this->_dbuser = LIVE_DBUSER;
		$this->_dbpass = LIVE_DBPASS;
        
		            
        if($username!==null)
            $this->_dbuser = $username;
        
        if($password!==null)
            $this->_dbpass = $password;
        
        if($db!==null)
            $this->_db = $db;
        
		//$this->_dbcon = oci_connect($this->_dbuser,$this->_dbpass,$this->_db);
		$this->_dbcon = oci_connect($this->_dbuser,$this->_dbpass,$this->_db, 'AL32UTF8');
		if (!$this->_dbcon)
		{
		    $this->_error = oci_error();
		    return false;
		}
		
			//$this->_dbcon->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
			if (!$this->_dbcon) {
			    $this->_error = oci_error();
			    $this->send_oci_error('CONNECT',trigger_error(htmlentities($this->_error['message'], ENT_QUOTES), E_USER_ERROR)); 
			    return false;
			}
	}
	
	private function send_oci_error($query,$error)
	{
		$mb = "OCI Error Thrown running:".PHP_EOL;
		$mb .= $query.PHP_EOL.PHP_EOL;
		$mb .= "Error:".PHP_EOL;
		$mb .= var_export($error,true).PHP_EOL.PHP_EOL;
		
		if(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR']!=="")
			$mb .= "IP Address:".$_SERVER['REMOTE_ADDR'];
		else
			$mb .= "IP Address not detected (Cron?)";
		
		mail('development@debitfinance.co.uk', "OCI Error Thrown", $mb,
			"Content-Type: text/plain; charset=utf-8\r\nFrom: development@debitfinance.co.uk");
	}
	
	public function query($sql,$params = array()) {

		$stmt = oci_parse($this->_dbcon,$sql);

		if(!empty($params)) {
			foreach($params as $param)
			{
				if($param['type']==="int")
				{
					oci_bind_by_name($stmt,$param['name'],$param['value']);
				}
				elseif($param['type']==="blob")
				{
					oci_bind_by_name($stmt,$param['name'],$param['value'],-1,OCI_B_BLOB);
				}
				else
				{
					oci_bind_by_name($stmt,$param['name'],$param['value']);
				}
			}
			
		}

		oci_execute($stmt);
		if(!$stmt) {
			$this->_error = oci_error($this->_dbcon);
			return false;
		}

		oci_fetch_all($stmt,$data,0,-1,OCI_FETCHSTATEMENT_BY_ROW);
		

		if(!$data || $data===null || is_null($data) || $data === "")
		{
			$data = array();
		}

		return $data;

	}
	
	public function count($sql,$params = array()) {

			$stmt = oci_parse($this->_dbcon,$sql);
			if(!empty($params))
			{
				foreach($params as $param)
				{
					if($param['type']==="int")
					{
						oci_bind_by_name($stmt,$param['name'],$param['value']);
					}
					elseif($param['type']==="blob")
					{
						oci_bind_by_name($stmt,$param['name'],$param['value'],-1,OCI_B_BLOB);
					}
					else
					{
						oci_bind_by_name($stmt,$param['name'],$param['value']);
					}
				}
			}
			
			oci_execute($stmt);

		if(!$stmt) {
			$this->_error = oci_error($this->_dbcon);
			return false;
		}

		oci_fetch_all($stmt,$data,0,-1,OCI_FETCHSTATEMENT_BY_ROW);

		if(!$data || $data===null || is_null($data) || $data === "")
		{
			$data = array();
		}

		return count($data);
	}
	
	public function apply($sql,$params = array()) {
		
			$stmt = oci_parse($this->_dbcon,$sql);
			if(!empty($params))
			{
				foreach($params as $param)
				{
					if($param['type']==="int")
					{
						oci_bind_by_name($stmt,$param['name'],$param['value']);
					}
					elseif($param['type']==="blob")
					{
						oci_bind_by_name($stmt,$param['name'],$param['value'],-1,OCI_B_BLOB);
					}
					else
					{
						oci_bind_by_name($stmt,$param['name'],$param['value']);
					}
				}
				
			}

			oci_execute($stmt);
			return true;
		if(!$stmt) {
			$this->_error = oci_error($this->_dbcon);
			return false;
		}
	}

	public function error()
	{
		var_dump($this->_error);
	}
	public function return_error()
	{
		return $this->_error;
	}
	public function export($var)
	{
		$return = "<pre>".var_export($var)."</pre>";

		return $return;
	}

	public function dump($var)
	{
		$return = "<pre>".var_dump($var)."</pre>";

		return $return;
	}	
}