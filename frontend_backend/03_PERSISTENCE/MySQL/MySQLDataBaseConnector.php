<?php 
	
	class MySQLDataBaseConnector{

		private $servername = "dbSQL";
		private $username = "user";
		private $password = "pwd";
		private $dbname = "RecipeWebsiteDB";
		private $conn =  null;

		private $title;
		private $body;
		private $date_created;

		public function __construct() {
			try {
				//* init new PDO
				$this->conn = new PDO("mysql:host=$this->servername;dbname=$this->dbname", $this->username, $this->password);
				if (!$this->conn) {
					die("ERROR-MySQL: No Connection!");
				}
				//* set PDO error mode to exception
				$this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			} 

			catch(PDOException $e) {
				die ("ERROR-MySQL: " . $e->getMessage());
			}
		}

		/**
		 * return database connection
		 * @return PDO (not null and activ)
		 */
		public function getConnection(){
			return $this->conn;
		}

		

		/**
		 * checks whether given databaseConnection is activ or not
		 * @param mixed $databaseConnection
		 * @return mixed
		 */
		public static function checkIfConnectionIsOpen($databaseConnection){
			$status = $databaseConnection->getAttribute(PDO::ATTR_CONNECTION_STATUS);
			if ($status !== false) {
				echo "Connection is <b>activ</b>.";
			} else {
				echo "Connection is <b>not activ</b>.";
			}
			return $status;
		}

		

		

		public function getTitle() {
			return $this->title;
		}
	
		public function getBody() {
			return $this->body;
		}
	
		public function getDateCreated() {
			return $this->date_created;
		}

		
    }

?>