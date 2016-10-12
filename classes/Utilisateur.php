<?php

include_once "ClasseBase.php";

class Utilisateur extends ClasseBase {
	protected static $table = 'utilisateur';
	protected static $atrList = array('login', 'nom', 'prenom', 'mail', 'naissance', 'ville', 'portrait');
	
	protected $login;
	protected $creation;
	protected $pass;
	protected $salt;
	protected $nom;
	protected $prenom;
	protected $mail;
	protected $naissance;
	protected $ville;
	protected $portrait;
	protected $groupes = array();
	protected $activites = array();
	protected $commentaires = array();
	protected $appreciations = array();
	protected $invitations = array();
	
	protected function display() {
		
	}
	
}
