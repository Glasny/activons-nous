<?php

include_once "BaseListe.php";


class ActiviteList extends BaseListe {
	protected static $atrList = array('list'=>'string');
	protected static $table = 'activite';
	protected static $limit = 10;
	protected static $filtreList = array('ville', 'categorie');
	
	protected $activites = array();
	protected $villes = array();
	protected $categories = array();
	
	
	
	
	// Affichage de la page
	public function display () {
		$this->prepList();
		$query = 'SELECT A.id, A.titre, DATE_FORMAT(A.date, "Le %e/%c/%Y à %kh%i") AS date, A.user, V.nom AS ville FROM activite A, ville V'; 
		if ($this->filtre == 'categorie') // Filtre par catégorie
			$query .= ', activiteCategorie C WHERE A.ville=V.id AND C.activite=A.id AND C.categorie='.$this->val;
		else {
			$query .= ' WHERE A.ville=V.id';
			if ($this->filtre && $this->val) // Autre filtre
				$query .= ' AND '.$this->filtre.'='.$this->val;
		}
		$query .= ' ORDER BY A.id DESC LIMIT '.($this->page-1)*$this::$limit.','.$this::$limit;
		$cnx = new CnxBdd();
		$this->activites = $cnx->send($query);
		$this->villes = $cnx->send("SELECT DISTINCT V.id, V.nom FROM ville V, activite A WHERE A.ville=V.id ORDER BY V.nom ASC");
		$this->categories = $cnx->send("SELECT DISTINCT C.id, C.nom FROM categorie C, activiteCategorie A 
			WHERE A.categorie=C.id ORDER BY C.nom ASC");
		unset($cnx);
		echo $this->twig->render('activiteList.html', $this->getAttributs());
	}
}

