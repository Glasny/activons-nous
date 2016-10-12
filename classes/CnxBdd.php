<?php


include 'Info.php';

class CnxBdd {
	private $cnx;
	
	public function __construct () {
		$host = Info::$host;
		$database = Info::$database;
		$login = Info::$login; 	// Login BD
		$pass = Info::$pass; 	// Pass BD
		if (!$this->cnx = new mysqli($host, $login, $pass, $database))
			Connect::displayError(2);
		if (!$this->cnx->set_charset('utf8'))
			Connect::displayError(2);
	}
	
	public function __destruct () {
		$this->cnx->close(); // Fermeture cnx mysqli_connect
	}
	// Envoi requête préparée
	public function sendPrep ($query, $val, $types) {
		$stmt = $this->cnx->prepare($query);
		if ($this->cnx->errno)
			Connect::displayError(2);
		$ref = array();
		$ref[0] = $types; // Tableau de référence pour paramètres bind_param dans call_user_func_array
		$len = count($val);
		for ($i=0;$i<$len;$i++) {
			$ref[$i+1] = &$val[$i];
		}
		call_user_func_array(array($stmt, 'bind_param'), $ref); // bind_param dynamique
		if ($this->cnx->errno)
			Connect::displayError(2);
		$result = $stmt -> execute();
		if ($this->cnx->errno)
			Connect::displayError(2);
		$stmt -> close();
	}
	// Requête non préparée
	public function send ($query) {
		$result = $this->cnx->query($query);
		if (!$result)
			Connect::displayError(2);
		else {
			$ret = array();
			if ($result -> num_rows > 0) {
				while ($line = $result -> fetch_assoc())
					array_push($ret, $line);
			}
			return $ret;
		}
	}
}
