<?php

include 'BaseAffichage.php';

class Accueil extends BaseAffichage {
	protected static $atrList = array('list'=>0);
	protected $users = array();
	protected $activites = array();
	
	public function display() {
		$cnx = new CnxBdd();
		$this->users = $cnx->send('
			SELECT U.login, V.nom AS ville, (DATEDIFF(CURRENT_DATE, U.naissance) DIV 365.25) AS age, I.repertoire, I.fichier 
			FROM ville V, utilisateur U LEFT JOIN image I ON U.portrait=I.id WHERE U.ville=V.id ORDER BY U.creation DESC LIMIT 5');
		$this->activites = $cnx->send('
			SELECT A.id, A.titre, DATE_FORMAT(A.date, "Le %e/%c/%Y à %kh%i") AS date, A.user, I.repertoire, I.fichier, V.nom AS ville
			FROM ville V, activite A LEFT JOIN image I on A.image=I.id WHERE A.ville=V.id ORDER BY A.id DESC LIMIT 5');
		echo $this->twig->render('accueil.html', $this->getAttributs());
	}
}
