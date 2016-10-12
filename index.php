<?php

session_start();

require_once __DIR__.'/classes/Connect.php';

if (isset($_GET) && array_key_exists ('action', $_GET)) {
	if ($_GET['action'] == 'logout') // Requête déconnexion
		Connect::logout();
	else if ($_GET['action'] == 'login') // Requête connexion
		Connect::login();
}

if (Connect::checkSession()) { // Utilisateur connecté
	$username = $_SESSION['login'];
	$droits = $_SESSION['droits'];
	session_regenerate_id(true);
} else { // Utilisateur non-connecté
	$username = null;
	$droits = 2;
}

require_once __DIR__.'/classes/Dispatcher.php';

