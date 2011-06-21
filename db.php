<?php
	require_once('common.inc.php');
	
	class DB{	
		private static $conn;	
		function __construct(){
			global $db_host,$db_username,$db_password,$db_dbname;
			self::$conn = mysqli_connect($db_host,$db_username,$db_password,$db_dbname) or die('error');
		}		
		
		private function query($query){
			mysqli_set_charset(self::$conn ,'utf8');
			$result = mysqli_query(self::$conn ,$query);//or die(mysqli_error(self::$conn ));
			
			return $result;
		}
		
		function getConn(){
			return self::$conn;
		}
		
		private function select($result){			
			$arr = array();
			while($row = mysqli_fetch_object($result)){
				array_push($arr,$row);				
			}
			return $arr;
		}
		
		function has($tablename,$arr){
			$wherearr = array();
			foreach($arr as $k=>$v){
				array_push($wherearr,"$k = '$v'");
			}
			$wherestr = implode($wherearr,' and ');
			$querystr = "select * from $tablename where $wherestr";
			
			$result = $this->query($querystr);			
			return mysqli_affected_rows(self::$conn);
		}
		
		
		function getOne($tablename,$where){
			$wherearr = array();
			foreach($where as $k=>$v){
				array_push($wherearr,"$k = '$v'");
			}
			$wherestr = implode($wherearr,' and ');
			$querystr = "select * from $tablename where $wherestr";			
			$result = $this->query($querystr);			
			$ret = $this->select($result);
			return !empty($ret)?$ret[0]:false;
		}
		
		
		function delete($tablename,$where){
			$wherearr = array();
			foreach($where as $k=>$v){
				array_push($wherearr,"$k = '$v'");
			}
			$wherestr = implode($wherearr,' and ');
			$querystr = "delete from `$tablename` where ($wherestr);";	
			self::query($querystr);
		}
		
		function update($tablename,$arr,$where){
			$setsarr = array();
			foreach($arr as $k=>$v){
				array_push($setsarr,"`$k`='$v'");
			}
			$sets = implode($setsarr,',');
		
			$wherearr = array();
			foreach($where as $k=>$v){
				array_push($wherearr,"$k = '$v'");
			}
			$wherestr = implode($wherearr,' and ');
			$querystr = "update `$tablename` set $sets where $wherestr";
			self::query($querystr);
		}
		
		//insert('usr',array('name'=>'asd','qwe'=>'qwe'));
		function insert($tablename,$arr){
			$keys = array_keys($arr);
			$values = array_values($arr);
			$keyarr= array();
			foreach($keys as $key){
				array_push($keyarr,"`$key`");			
			}
			
			$valuearr= array();
			foreach($values as $value){
				if(!is_null($value)){
					$value = "'$value'";
				}else{
					$value = "NULL";
				}
				array_push($valuearr,$value);			
			}
			
			$keystr = implode($keyarr,',');
			$valuestr = implode($valuearr,',');
			
			$querystr = "insert into `$tablename` ($keystr) values ($valuestr)";
			
			self::query($querystr);
		}

	}
	
	/*
	class User{
		private $id;
		private $email;
		private $nickname;
		private $password;
		private $createtime;
		private $signal;
		private $latlng;
		function __construct($id,$nickname,$password,$createtime,$signal,$latlng){
			$this -> id = $id;
			$this -> nickname = $nickname;
			$this -> password = $password;
			$this -> createtime = $createtime;
			$this -> signal = $signal;
			$this -> latlng = $latlng;
		}
	}
	*/
	
	
	class DAO extends DB{	
		private $user;
		private static $db;
		private static $tbname;
		
		function __construct($tablename){
			$this->tbname = $tablename;
			self::$db = new DB();
		}
		
		function create($arr){	
			self::$db->insert($this->tbname,$arr);			
		}	
		
		function delete($arr){
			self::$db->delete($this->tbname,$arr);
		}
		
		function update($arr,$where){		
			self::$db->update($this->tbname,$arr,$where);
		}
		
		function lastID(){
			return mysqli_insert_id(self::$db->getConn());
		}
		
		function getOne($arr){
			return parent::getOne($this->tbname,$arr);
		}
		
		function has($arr){			
			return parent::has($this->tbname,$arr);
		}
			
	}	
	
	function sigpost($url,$data = null){
		global $key,$secret,$user;
		$consumer = new OAuthConsumer($key, $secret, NULL);
		$signal_method =  new OAuthSignatureMethod_HMAC_SHA1();
		$access_token = new OAuthConsumer($user->username, $user->password);
		$acc_req = OAuthRequest::from_consumer_and_token($consumer, $access_token, "POST", $url, NULL);
		$acc_req->sign_request($signal_method, $consumer, $access_token);
		
		$header = $acc_req->to_header('http://127.0.0.1');
		
		$ch = curl_init();	
		curl_setopt($ch, CURLOPT_URL, $url); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/atom+xml', $header)); 
		curl_setopt($ch, CURLOPT_POST, true); 
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data); 
		$response = curl_exec($ch); 
		curl_close($ch); 
		return $response;
	}
	
	
	function islogged(){
		global $sessiondao,$userdao,$sigkey,$user;
		$logged = false;
		$sid = @$_COOKIE['sid'];
		
		if(isset($sid)){
			$session = $sessiondao->getOne(array('sid'=>$sid));
			if($session){
				$user = $userdao->getOne(array('id'=>$session->uid));
				$sigarr = array(
					'username' => $user->username,
					'password' => $user->password,
					'ua'  => $_SERVER['HTTP_USER_AGENT'],
					'ip'  => $_SERVER['REMOTE_ADDR'],
					'key' => $sigkey
				);
				
				if(md5(implode($sigarr,'')) == @$_COOKIE['sig']){
					$logged = true;	
				}
			}
		}
		return $logged;
	}
	
	
	
	$userdao = new DAO('user');
	$invitedao = new DAO('invite');
	$sessiondao = new DAO('session');
	
	
	
	
	
	
?>