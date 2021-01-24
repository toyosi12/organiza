<?php
	require_once "config.php";
	class DbConn{
		private static $host = DB_HOST;
		private static $user = DB_USER;
		private static $pass = DB_PASS;
		private static $db_name = DB_NAME;
		protected static $conn;
		public function __construct(){
		}
		public static function connect(){
			self::$conn = new \mysqli(self::$host,self::$user,self::$pass,self::$db_name);
			if(!self::$conn){
				echo "Error! Could not connect to the database".self::$conn->connect_error;
				return false;
			}else{
				return true;
			}
		}


	}

	DbConn::connect();

?>