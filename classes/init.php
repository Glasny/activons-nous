<?php

	include 'Info.php';

	header('Content-Type: text/html; charset=utf-8');
	
	$login = Info::$mysqlLogin;	
	$pass =  Info::$mysqlPass;
	$host = Info::$host;
	$database = Info::$database;
	$newLogin = Info::$login; 	// Login BD
	$newPass = Info::$pass; 	// Pass BD
	
    
    // Création d'une table + confirmation ou msg erreur
    function createTable($query) {
		global $connect;
		if ($connect -> query($query)) {
			$nom = substr($query, 13, strpos($query, "(")-14);
			echo "Table $nom crée<br/>\n";
		} else
			echo $connect -> error ."<br/>\n";
	}
	
	function insert($table, $atr, $data) {
		global $connect;
		$len = count($atr);
		$query = "INSERT INTO $table (";
		for ($i=0; $i<$len; $i++)
			$query .= $atr[$i].', ';
		$query = substr($query, 0, -2) .') VALUES';
		$val = '';
		$len2 = count($data);
		for ($i=0; $i<$len2; $i++) {
			$val .= ' (';
			for ($j=0; $j<$len; $j++) {
				if (is_null($data[$i][$j]))
					$val .= 'null, ';
				elseif (gettype($data[$i][$j]) == 'integer')
					$val .= $data[$i][$j].', ';
				else
					$val .= '"'.$data[$i][$j].'", ';
			}
			$val = substr($val, 0, -2).'),';
		}
		$query .= substr($val, 0, -1);
		if (!$connect -> query($query)) {
			echo $connect -> error ."<br/>\n";
		}
	}
    
    
    // CRÉATION + CONNEXION À LA BASE
    
    global $connect;
    
    // Connection serveur SQL
    if (!$connect = new mysqli($host, $login, $pass)) {
        echo $connect -> error;
        exit();
    }
    $connect -> query("DROP DATABASE $database");
    // Création BDD
    if ($connect -> query("CREATE DATABASE $database"))
        echo "Base de donnée crée<br/>\n";
    // Connection BDD
    if (!$connect = new mysqli($host, $login, $pass, $database)) {
        echo $connect -> error;
        exit();
    }
    // Vérif caractères utf-8
    if (!$connect -> set_charset('utf8')) {
        echo $connect -> error;
        exit();
    }
    
    
    
    // CRÉATION DES TABLES
    
    /* Table ville */
    createTable('CREATE TABLE ville (id INT AUTO_INCREMENT PRIMARY KEY,
					nom VARCHAR(40) NOT NULL)');
	createTable('CREATE TABLE image (id INT AUTO_INCREMENT PRIMARY KEY,
					fichier VARCHAR(40) NOT NULL,
					repertoire VARCHAR(40) NOT NULL)');
    createTable('CREATE TABLE utilisateur (login VARCHAR(20) UNIQUE PRIMARY KEY,
					creation TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
					pass VARCHAR(64) NOT NULL,
					salt VARCHAR(60) NOT NULL,
					droits INT NOT NULL DEFAULT 1,
					nom VARCHAR(40),
					prenom VARCHAR(40),
					mail VARCHAR(50),
					naissance DATE NOT NULL,
					ville INT NOT NULL,
					portrait INT,
					CONSTRAINT fk_usr_v FOREIGN KEY(ville) REFERENCES ville(id),
					CONSTRAINT fk_usr_p FOREIGN KEY(portrait) REFERENCES image(id) ON DELETE SET NULL)');
	createTable('CREATE TABLE groupe (id INT AUTO_INCREMENT PRIMARY KEY,
					creation TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
					nom VARCHAR(80) NOT NULL,
					description TEXT,
					user VARCHAR(20) NOT NULL,
					CONSTRAINT fk_grp_u FOREIGN KEY(user) REFERENCES utilisateur(login) ON DELETE CASCADE)');
	createTable('CREATE TABLE groupeUtilisateur (id INT AUTO_INCREMENT PRIMARY KEY,
					groupe INT NOT NULL,
					user VARCHAR(20) NOT NULL,
					CONSTRAINT fk_gU_u FOREIGN KEY(user) REFERENCES utilisateur(login) ON DELETE CASCADE,
					CONSTRAINT fk_gU_g FOREIGN KEY(groupe) REFERENCES groupe(id) ON DELETE CASCADE,
					CONSTRAINT cc_gU_gu UNIQUE (groupe, user))');
	createTable('CREATE TABLE activite (id INT AUTO_INCREMENT PRIMARY KEY,
					creation TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
					titre VARCHAR(80) NOT NULL,
					description TEXT,
					date DATETIME NOT NULL,
					lieu VARCHAR(80),
					ville INT NOT NULL,
					image INT,
					prive TINYINT(1) NOT NULL DEFAULT 0,
					user VARCHAR(20) NOT NULL,
					groupe INT,
					CONSTRAINT fk_evt_v FOREIGN KEY(ville) REFERENCES ville(id),
					CONSTRAINT fk_evt_p FOREIGN KEY(image) REFERENCES image(id) ON DELETE SET NULL,
					CONSTRAINT fk_evt_u FOREIGN KEY(user) REFERENCES utilisateur(login) ON DELETE CASCADE,
					CONSTRAINT fk_evt_g FOREIGN KEY(groupe) REFERENCES groupe(id) ON DELETE CASCADE)');
	createTable('CREATE TABLE activiteUtilisateur (id INT AUTO_INCREMENT PRIMARY KEY,
					activite INT NOT NULL,
					user VARCHAR(20) NOT NULL,
					CONSTRAINT fk_eU_u FOREIGN KEY(user) REFERENCES utilisateur(login) ON DELETE CASCADE,
					CONSTRAINT fk_eU_e FOREIGN KEY(activite) REFERENCES activite(id) ON DELETE CASCADE,
					CONSTRAINT cc_eU_eu UNIQUE (activite, user))');
	createTable('CREATE TABLE categorie (id INT AUTO_INCREMENT PRIMARY KEY,
					nom VARCHAR(40) UNIQUE)');
	createTable('CREATE TABLE activiteCategorie (id INT AUTO_INCREMENT PRIMARY KEY,
					activite INT NOT NULL,
					categorie INT NOT NULL,
					CONSTRAINT fk_eC_e FOREIGN KEY(activite) REFERENCES activite(id) ON DELETE CASCADE,
					CONSTRAINT fk_eC_c FOREIGN KEY(categorie) REFERENCES categorie(id) ON DELETE CASCADE,
					CONSTRAINT cc_eC_ec UNIQUE (activite, categorie))');
	createTable('CREATE TABLE commentaire (id INT AUTO_INCREMENT PRIMARY KEY,
					date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
					contenu TEXT NOT NULL,
					user VARCHAR(20) NOT NULL,
					activite INT NOT NULL,
					CONSTRAINT fk_com_u FOREIGN KEY(user) REFERENCES utilisateur(login) ON DELETE CASCADE,
					CONSTRAINT fk_com_e FOREIGN KEY(activite) REFERENCES activite(id) ON DELETE CASCADE)');
	createTable('CREATE TABLE appreciation (id INT AUTO_INCREMENT PRIMARY KEY,
					date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
					note INT NOT NULL,
					commentaire TEXT,
					user VARCHAR(20) NOT NULL,
					activite INT NOT NULL,
					CONSTRAINT fk_apr_u FOREIGN KEY(user) REFERENCES utilisateur(login) ON DELETE CASCADE,
					CONSTRAINT fk_apr_e FOREIGN KEY(activite) REFERENCES activite(id) ON DELETE CASCADE,
					CONSTRAINT cc_apr_ae UNIQUE (user, activite))');
	createTable('CREATE TABLE invitation (id INT AUTO_INCREMENT PRIMARY KEY,
					date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
					message TEXT NOT NULL,
					user VARCHAR(20) NOT NULL,
					activite INT NOT NULL,
					CONSTRAINT fk_inv_u FOREIGN KEY(user) REFERENCES utilisateur(login) ON DELETE CASCADE,
					CONSTRAINT fk_inv_e FOREIGN KEY(activite) REFERENCES activite(id) ON DELETE CASCADE,
					CONSTRAINT cc_inv_ue UNIQUE (user, activite))');
	createTable('CREATE TABLE favoris (id INT AUTO_INCREMENT PRIMARY KEY,
					emet VARCHAR(20) NOT NULL, 
					desti VARCHAR(20) NOT NULL,
					CONSTRAINT fk_fav_e FOREIGN KEY(emet) REFERENCES utilisateur(login) ON DELETE CASCADE,
					CONSTRAINT fk_fav_d FOREIGN KEY(desti) REFERENCES utilisateur(login) ON DELETE CASCADE,
					CONSTRAINT cc_fav_ed UNIQUE (emet, desti))');
	createTable('CREATE TABLE message (id INT AUTO_INCREMENT PRIMARY KEY,
					date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
					emet VARCHAR(20) NOT NULL, 
					desti VARCHAR(20) NOT NULL,
					objet VARCHAR(80) NOT NULL,
					contenu TEXT,
					supEmet TINYINT(1) DEFAULT 0,
					supDesti TINYINT(1) DEFAULT 0,
					CONSTRAINT fk_msg_e FOREIGN KEY(emet) REFERENCES utilisateur(login) ON DELETE CASCADE,
					CONSTRAINT fk_msg_d FOREIGN KEY(desti) REFERENCES utilisateur(login) ON DELETE CASCADE)');
    
    // Utilisateur + droits
    if ($connect->query("GRANT SELECT, INSERT, UPDATE, DELETE ON $database.* TO $newLogin@$host IDENTIFIED BY '$newPass'")) {
        echo "Utilisateur crée<br/>\n";
    }
    
    
    // Insert valeurs départ
    insert('ville', array('nom'), array(
		array('Paris'),
		array('Lyon'),
		array('Nantes'),
		array('Marseilles')));
	insert('categorie', array('nom'), array(
		array('Restau'),
		array('Cinéma'),
		array('Concert'),
		array('Théâtre'),
		array('Musée'),
		array('Expo'),
		array('Visite'),
		array('Balade'),
		array('Prendre un verre'),
		array('En plein air'),
		array('Insolite'),
		array('Culture'),
		array('Divertissement'),
		array('Sortir'),
		array('Détente'),
		array('Sport'),
		array('Discothèque')));
	insert('image', array('fichier', 'repertoire'), array(
		array('img1.jpg', 'img1'),
		array('img2.jpg', 'img1'),
		array('img3.jpg', 'img1'),
		array('img4.jpg', 'img1'),
		array('img5.jpg', 'img1')));
	$pass = 'azeaze';
	$aPass = array();
	for ($i=0; $i<6; $i++) {
		$s = password_hash($pass, PASSWORD_DEFAULT);
		array_push($aPass, array($s, hash('sha256', $s.$pass)));
	}
    insert('utilisateur', array('login', 'pass', 'salt', 'ville', 'droits','prenom','nom', 'mail', 'naissance','portrait'), array(
		array('admin', $aPass[0][1], $aPass[0][0], 1, 0, null, null ,null, '1974-03-16', null),
		array('VVG12', $aPass[1][1], $aPass[1][0], 1, 1,'Vincent', 'Van-Gogh', 'vvg12@gmail.com', '1963-03-16', 1),
		array('PP_Officiel', $aPass[2][1], $aPass[2][0], 1, 1, null, null, null, '1962-03-16', 2),
		array('Mona42', $aPass[3][1], $aPass[3][0], 1, 1, 'Mona', null, null,  '1983-03-16', 3),
		array('Denis05', $aPass[4][1], $aPass[4][0], 2, 1, null, null, null, '1989-03-16', null),
		array('Dupont', $aPass[5][1], $aPass[5][0], 2, 1, null, null, null, '1979-03-16', null)));
	insert('activite', array('titre','description','date','lieu','ville','image','user'), array(
		array('Rétrospective Hitchcock', '<br/>Programme: <br/>- Les oiseaux<br/>- Psychose', '2016-07-2 19:00:00',
			'MK2 Quai de Loire', 1, 4, 'VVG12'),
		array('Dîner Malgache', 'Entre 10€ et 15€ par personnes', '2016-06-26 20:00:00', 'Restaurant Le Marianina - 88 rue Blanche, 9ème arr.', 1, null, 'PP_Officiel'),
		array('Sortie escalade', 'Prévoyez votre matériel !', '2016-07-05 14:30:00', 'Le mur de Lyon - 11 rue Lortet', 2, null, 'Dupont')));
	insert('activiteCategorie', array('activite', 'categorie'), array(
		array(1, 2),
		array(1, 13),
		array(2, 1),
		array(2, 14),
		array(3, 14),
		array(3, 16)));
	insert('activiteUtilisateur', array('activite', 'user'), array(
		array(1, 'PP_Officiel'),
		array(1, 'Mona42'),
		array(2, 'Mona42'),
		array(3, 'Denis05')));
	insert('commentaire', array('contenu', 'user', 'activite'), array(
		array('Un premier commentaire ...','Mona42',1),
		array('Un deuxième <br/> sur deux lignes !','VVG12',1),
		array("Et un troisième <br/> avec des caractères spéciaux: \ / \\\\'azaz'",'PP_Officiel',1)));
	insert('appreciation', array('note','commentaire', 'user', 'activite'), array(
		array(4, 'Un commentaire <br/>d\'appréciation ...','Mona42',1),
		array(5, null,'PP_Officiel',1)));
    
    
    $connect->close();
    
    
    
    
    
