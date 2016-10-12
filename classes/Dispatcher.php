<?php


if (isset($_GET)) {
	$display = (array_key_exists('display', $_GET))? $_GET['display'] : '';
	$action = (array_key_exists('action',$_GET))? $_GET['action'] : '';
} else {
	$display = '';
	$action = '';
}

// Vérifie classe à instancier. Accueil par défaut
switch ($display) {
	case 'activite' : 	include_once 'Activite.php';
						$page = new Activite();
						break;
	case 'commentaire' : include_once 'Commentaire.php';
						$page = new Commentaire();
						break;
	case 'activites' :	include_once 'ActiviteList.php';
						$page = new ActiviteList();
						break;
	default :			include_once 'Accueil.php';
						$page = new Accueil();
}

// Ajoute nom d'utilisateur et droits aux variables d'instance
$page -> set(array('username'=>$username, 'droits'=>$droits));

// Vérifie méthode à appeler. Accueil par défaut
switch ($action) {
	case 'enregistrer' : 	$page -> save();
							break;
	case 'modifier': 		$page -> update();
							break;
	case 'supprimer' :		$page -> delete();
							break;
	default :				$page -> checkDisplay();
}
