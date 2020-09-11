<?php 
    require_once "CONFIG.INI.php";
	/**
	* Database Connection
	*/
	class DB {
		private $server = DB_SERVER;
		private $dbname = DB_DATABASE;
		private $user = DB_USER;
		private $pass = DB_PASS;

		public function connect() {
			try {
				$conn = new PDO('mysql:host=' .$this->server .';dbname=' . $this->dbname, $this->user, $this->pass);
				$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				return $conn;
			} catch (\Exception $e) {
				echo "Database Error: " . $e->getMessage();
			}
		}
	}
 ?>