<?php

include_once 'CnxBdd.php';

class Connect {
	// Vérification session: existe + login/droits définis
	public static function checkSession() {
		if (isset($_SESSION) && array_key_exists('login', $_SESSION)  && array_key_exists('droits', $_SESSION))
			return true;
		return false;
	}
	// Connection : détruit session + renvoi page précédente
	public static function logout() {
		setcookie(session_name(), NULL, -1);
		unset($_SESSION);
		session_destroy();
		self::forward();
	}
	// Connection : vérification paramètres + login/pass + renvoi page précédente
	public static function login () {
		// vérification paramètres $_POST + format login correct
		if (!isset($_POST) || !array_key_exists('login', $_POST) || !self::checkLogin($_POST['login']) || !array_key_exists('pass', $_POST))
			self::displayError(0);
		$login = $_POST['login'];
		$cnx = new CnxBdd();
		// Récupérer le salt
		$query = "SELECT salt FROM utilisateur WHERE login='$login'";
		$res = $cnx->send($query);
		if (count($res) != 1) // Entrée non trouvée
			self::displayError(0);
		$pass = hash('sha256', $res[0]['salt'].$_POST['pass']);
		// Vérification couple login / pass
		$query = "SELECT droits FROM utilisateur WHERE login='$login' AND pass='$pass'";
		$res = $cnx->send($query);
		if (count($res) != 1) // login / pass incorrects
			self::displayError(0);
		$droits = $res[0]['droits'];
		setcookie(session_id(), NULL, 60*60);
		$_SESSION['login'] = $login;
		$_SESSION['droits'] = $droits;
		self::forward();
	}
	// Erreur: Renvoi page précédente + code erreur
	public static function displayError($err) {
		if (strpos($_SERVER['HTTP_REFERER'],'?') == false)
			$_SERVER['HTTP_REFERER'] .= "?error=$err";
		else if (strpos($_SERVER['HTTP_REFERER'],'error=') != false)
			$_SERVER['HTTP_REFERER'] = substr($_SERVER['HTTP_REFERER'], 0, -1).$err;
		else
			$_SERVER['HTTP_REFERER'] .= "&error=$err";
		header('Location: '.$_SERVER['HTTP_REFERER']);
		exit;
	}
	// Renvoi page précédente
	public static function forward() {
		if (strpos($_SERVER['HTTP_REFERER'],'error=') != false)
			$_SERVER['HTTP_REFERER'] = substr($_SERVER['HTTP_REFERER'], 0, -8);
		header('Location: '.$_SERVER['HTTP_REFERER']);
		exit;
	}
	// Vérification format login
	private static function checkLogin($login) {
		return preg_match('/^\w{4,20}$/i', $login) != -1;
	}
}
