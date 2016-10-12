<?php

include_once 'BaseClasse.php';

class BaseAffichage extends BaseClasse {
	protected $twig; // Moteur de template
	protected $error; // Msg d'erreur
	
	// Vérification type de diplay + clé primaire ok + tuple chargé
	public function checkDisplay() {
		// Instanciation twig
		require_once __DIR__.'/../twig/lib/Twig/Autoloader.php';
		Twig_Autoloader::register();
		$loader = new Twig_Loader_Filesystem(__DIR__.'/../vues');
		$this->twig = new Twig_Environment($loader, array('cache' => false));
		// Vérifie si erreur
		if ($_GET && array_key_exists('error', $_GET) && $_GET['error'] >= '0' && $_GET['error'] < 3) {
			$msgError = array('Identifiants incorrects', 'Page introuvable', 'Erreur de traitement');
			$this->error = $msgError[$_GET['error']];
		}
		if (array_key_exists('list',$this::$atrList)) // Page liste
			return $this->display();
		$key = (array_key_exists('login', $this::$atrList))? 'login' : 'id';
		if (!isset($_GET) || !array_key_exists($key, $_GET)) // Variable clé primaire absente
			Connect::displayError(1);
		$this-> $key = $_GET[$key];
		if (!$this::checkPK($key, $_GET[$key])) // Erreur format clé primaire
			Connect::displayError(1);
		$args = array();
		foreach ($this::$atrList as $k=>$v)
			array_push($args, $k);
		$this->loadInstance($args);
		$this->display(); // Chargement ok
	}
	
	// Récupérer attributs dans un array pour passage à twig
	protected function getAttributs() {
		// Attributs à ne pas envoyer à twig
		$noSend = array('twig', 'table', 'atrList', 'filtreList');
		$send = array();
		foreach ($this as $k => $v) {
			if (!in_array($k, $noSend))
				$send[$k] = $v;
		}
		return $send;
	}
}
