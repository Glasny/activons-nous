<?php

include_once "BaseAffichage.php";

abstract class BaseListe extends BaseAffichage {
	protected $page=1; // Page à afficher
	protected $filtre; // Attribut du filtre
	protected $val; // Valeur du filtre
	protected $nb=0; // Nb d'activités à afficher
	protected $nbPages; // Nb de pages correspondant
	
	// Récupération page, filtre
	protected function prepList() {
		if (isset($_GET)) {
			$query = 'SELECT count(*) AS nb FROM '.$this::$table;
			if (array_key_exists('page', $_GET) && preg_match('/^\d{1,3}$/', $_GET['page']) != -1 && $_GET['page'] > 0)
				$this->page = $_GET['page'];
			// Filtre défini et correspond à format attribut
			if (array_key_exists('filtre', $_GET) && in_array($_GET['filtre'], $this::$filtreList)) {
				// Valeur (du filtre) défini et correspond à clé étrangère (login ou id)
				if (array_key_exists('val', $_GET) && preg_match('/^\w{4,20}|\d{1,6}$/i', $_GET['val']) != -1) {
					$this->filtre = $_GET['filtre'];
					$this->val = $_GET['val'];
					if ($this::$table == 'activite' && $this->filtre == 'categorie') // exception : table externe
						$query .= ' A, activiteCategorie C WHERE A.id=C.activite AND C.categorie='.$this->val;
					else
						$query .= ' WHERE '.$this->filtre.'='.$this->val;
				}
			}

			$cnx = new CnxBdd();
			$nb = $cnx->send($query);
			if (count($nb) == 1)
				$this->nb = $nb[0]['nb']; // Nb d'activités à afficher
			$nbPages = (int)($this->nb/$this::$limit);
			if ($this->nb%$this::$limit != 0)
				$nbPages += 1;
			$this->nbPages = $nbPages; // Nb de page correspondant
			unset($cnx);
		}
	}
}
	
	
