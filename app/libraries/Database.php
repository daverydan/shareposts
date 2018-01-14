<?php
/*
* PDO Database Connection
* Connect to database
* Create prepared statements
* Bind values
* Return rows & results
*/
class Database {
	private $host = DB_HOST;
	private $user = DB_USER;
	private $pass = DB_PASS;
	private $dbname = DB_NAME;

	private $dbh; // db handler
	private $stm; // statement
	private $error;

	public function __construct()
	{
		// Set DSN
		$dsn = "mysql:host=$this->host;dbname=$this->dbname;";
		$options = array(
			PDO::ATTR_PERSISTENT => true, // persistent connection
			PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION // silent, warning & exception
		);

		// Create PDO Instance
		try {
			$this->dbh = new PDO($dsn, $this->user, $this->pass, $options);
		} catch(PDOException $e) {
			$this->error = $e->getMessage();
			echo $this->error;
		}
	}

	// prepare statement with query
	public function query($sql)
	{
		$this->stm = $this->dbh->prepare($sql);
	}

	// Bind values
	public function bind($param, $value, $type = null)
	{
		if (is_null($type)) {
			switch (true) {
				case is_int($value):
					$type = PDO::PARAM_INT;
					break;
				case is_bool($value):
					$type = PDO::PARAM_BOOL;
					break;
				case is_null($value):
					$type = PDO::PARAM_NULL;
					break;
				default:
					$type = PDO::PARAM_STR;
			}
		}

		$this->stm->bindValue($param, $value, $type);
	}

	// Execute prepared statement
	public function execute()
	{
		return $this->stm->execute();
	}

	// Get result set as array of objects
	public function resultSet()
	{
		$this->execute();
		return $this->stm->fetchAll(PDO::FETCH_OBJ);
	}

	// Get single record as an object
	public function single()
	{
		$this->execute();
		return $this->stm->fetch(PDO::FETCH_OBJ);
	}

	// Get row count
	public function rowCount()
	{
		return $this->stm->rowCount(); // PDO method
	}
}