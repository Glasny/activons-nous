<?php

include_once "BaseAffichage.php";

class Activite extends BaseAffichage {
	protected static $table = 'activite';
	protected static $atrList = array('id'=>'int', 'creation'=>'string', 'user'=>'string', 'groupe'=>'int', 'titre'=>'string', 'description'=>'string', 'date'=>'string', 'ville'=>'int', 'lieu'=>'string', 'image'=>'int', 'prive'=>'int');
	
	protected $id;
	protected $creation; // Date création
	protected $user; // Organisateur
	protected $groupe; // Groupe correspondant si privé
	protected $titre;
	protected $description;
	protected $date; // Date début
	protected $ville;
	protected $lieu;
	protected $image;
	protected $prive; // 1 = privé
	protected $categories = array(); // array(id => nom)
	protected $participants = array(); // array(login)
	protected $commentaires = array();
	protected $appreciations = array();
	protected $invitations = array();
	
	// Affichage de la page
	protected function display () {
		$cond = 'activite='.$_GET['id'];
		$cnx = new CnxBdd();
		$date = new DateTime($this -> date);
		$this->date = $date->format("\L\\e j/n/Y à G\hi");
		$res = $cnx->send('SELECT nom FROM ville WHERE id='.$this->ville);
		$this->ville = $res[0]['nom'];
		if ($this->image) { // Récupération image
			$res = $cnx->send('SELECT fichier, repertoire FROM image WHERE id='.$this->image);
			if (count($res) == 1) // remplace id image par répertoire/fichier
				$this->image = $res[0]['repertoire'].'/'.$res[0]['fichier'];
			else // Image non trouvée
				$this->image = null;
		}
		$this->categories = $cnx->send("SELECT C.nom, C.id FROM categorie C, activiteCategorie EC WHERE EC.categorie=C.id AND $cond");
		$this->participants = $cnx->send("SELECT user AS login FROM activiteUtilisateur WHERE $cond");
		$this->commentaires = $cnx->send("SELECT * FROM commentaire WHERE $cond");
		$this->appreciations = $cnx->send("SELECT * FROM appreciation WHERE $cond");
		$this->invitations = $cnx->send("SELECT * FROM invitation WHERE $cond");
		unset($cnx);
		echo $this->twig->render('activite.html', $this->getAttributs());
		exit;
	}
}

