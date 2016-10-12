<?php

include_once "CnxBdd.php";

abstract class BaseClasse {
	protected $username;
	protected $droits;
	
	//Vérifier format clé primaire
	protected static function checkPK($key, $val) {
		if ($key == 'login')
			return preg_match('/^\w{4,20}$/i', $val) != -1;
		else // $key = id
			return preg_match('/^\d{1,6}$/', $val) != -1;
	}

	// $atr : nom de l'attribut (string)
	// Fonction inutilisée pour le moment
	public function get($atr) {
		if (isset($this->$atr))
			return $this->$atr;
	}
	// $args : array(nom_attribut => valeur)
	public function set ($args) {
		foreach ($args as $k => $v) {
			$this->$k = $v;
		}
	}
	
	// Récupérer attributs + assigner aux variables d'instance
	protected function loadInstance($args) {
		$key = $args[0];
		$atr = '';
		$len = count($args);
		for ($i=1; $i<$len; $i++)
			$atr .= $args[$i].', ';
		$atr = substr($atr, 0, -2);
		$query = "SELECT $atr FROM ".$this::$table." WHERE $key=".$this->$key;
		$cnx = new CnxBdd();
		$args = $cnx->send($query);
		if (count($args) != 1) // Entrée non trouvée
			Connect::displayError(1);
		foreach($args[0] as $k => $v)
			if ($v != null)
				$this->$k = $v;
		return true;
	}
	
	// Enregistrer $this
	protected function saveInstance () {
		$query = 'INSERT INTO '.$this::$table.' ('; // Requête
		$val = array(); // array(valeur_attributs)
		$types = ''; // Chaine des types d'attributs
		$finQuery = ''; // Fin de la requête (remplace valeur attributs par ?)
		foreach ($this::$atrList as $k => $v) {
			if ($this->$k != null) {
				$query .= $k.', ';
				array_push($val , $this->$k);
				$finQuery .= '?, ';
				$types .= ($v == 'int')? 'i':'s';
			}
		}
		$query = substr($query, 0, -2) .') VALUES ('.substr($finQuery, 0, -2).')';
		$cnx = new CnxBdd();
		$cnx->sendPrep($query, $val, $types);
		unset($cnx);
	}
	// $atr : array(nom_attributs) à mettre à jour
	protected function updateInstance ($key) {
		$query = 'UPDATE '.$this::$table.' SET ';
		$val = array();
		$types = ''; // Chaine des types d'attributs
		foreach($this::$atrList as $k=>$v) {
			if ($k != $key && $this->$k !== null) {
				$query .= $k.'=?, ';
				array_push($val, $this->$k);
				$types .= ($v == 'int')? 'i':'s';
			}
		}
		$query = substr($query, 0, -2).' WHERE ';
		$query .= "$key=?";
		$types .= 'i';
		array_push($val, $this->$key);
		$cnx = new CnxBdd();
		$cnx->sendPrep($query, $val, $types);
		unset($cnx);
	}
	// Supprimer $this
	protected function deleteInstance($key) {
		$query = 'DELETE FROM '.$this::$table." WHERE $key=?";
		$val = array($this->$key);
		$type = ($this::$atrList[$key] == 'int')? 'i':'s';
		$cnx = new CnxBdd();
		$cnx->sendPrep($query, $val, $type);
		unset($cnx);
	}	
}
