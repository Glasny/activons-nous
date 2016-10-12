<?php

include_once 'BaseClasse.php';

class Commentaire extends BaseClasse {
	protected static $table = 'commentaire';
	protected static $atrList = array('id'=>'int','contenu'=>'string','user'=>'string','activite'=>'int');
	protected $id;
	protected $contenu;
	protected $user;
	protected $activite;
	
	public function save() {
		// Vérification droits + variables POST ok
		if ($this->droits > 1 || !$_POST || !array_key_exists('contenu', $_POST) || !array_key_exists('activite',$_POST))
			Connect::displayError(2);
		// Remplace les sauts de ligne textarea par <br> et ajoute backslash sur guillemets et backslash pour éviter erreur d'affichage
		$contenu = nl2br(htmlspecialchars($_POST['contenu'], ENT_QUOTES, "UTF-8"));
		$this->set(array('contenu'=>$contenu, 'user'=>$this->username, 'activite'=>$_POST['activite']));
		$this->saveInstance();
		Connect::forward();
	}
	
	public function delete() {
		// Vérification droits + variables POST ok
		if ($this->droits>1 || !$_POST || !array_key_exists('id', $_POST) || !$this::checkPK('id', $_POST['id']))
			Connect::displayError(2);
		$this->id = $_POST['id'];
		if ($this->droits == 1) { // Si utilisateur normal, vérification qu'il s'agit de l'auteur du commentaire
			$res = $this->loadInstance(array('id','user'));
			// Commentaire non trouvé ou auteur != utilisateur actuel
			if (count($res) != 1 || $this->user != $this->username)
				Connect::displayError(2);
		}
		$this->deleteInstance('id');
		Connect::forward();
	}
	
	public function update() {
		// Vérification droits + variables POST ok
		if ($this->droits>1 || !$_POST || !array_key_exists('id', $_POST) || !$this::checkPK('id', $_POST['id']) || !array_key_exists('contenu', $_POST) || $_POST['contenu'] == '')
			Connect::displayError(2);
		$this->id = $_POST['id'];
		if ($this->droits == 1) { // Si utilisateur normal, vérification qu'il s'agit de l'auteur du commentaire
			$res = $this->loadInstance(array('id','user'));
			// Commentaire non trouvé ou auteur != utilisateur actuel
			if (count($res) != 1 || $this->user != $this->username)
				Connect::displayError(2);
		}
		$this->contenu = nl2br(htmlspecialchars($_POST['contenu'], ENT_QUOTES, "UTF-8"));
		$this -> updateInstance('id');
		Connect::forward();
	}
}
