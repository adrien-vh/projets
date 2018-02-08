<?php
require_once('globals.inc.php');

class Bdd {
	
	private $pdo 	= null;
	private $dbg 	= array();
	private $bddOk 	= true;
	
	public function getDbg () { return $this->dbg; }
	
	public function __construct () {
		$this->initialize();
	}
	
	private function initialize () {
		try {
			$this->pdo = new PDO("mysql:host=mysql;dbname=projets", 'projets', 'projets@BDD@CCBP');
		} catch (PDOException $e) {
			$this->bddOk = false;
		}

		if ($this->bddOk) {
			$this->pdo->exec("set names utf8");
		}
	}
	
	public function getUserFullName($_login) {
		return $this->varFromRequete(
			"SELECT nom FROM utilisateur WHERE login = :login",
			array( ':login' => trim($_login) )
		);
	}

	public function sauveFichier($_nom, $_nomFS, $_ext) {
		return $this->insertFromRequete(
			"INSERT INTO fichier (nom, nomFS, ext) VALUES (:nom, :nomFS, :ext)",
			array(
				":nom" => $_nom,
				":nomFS" => $_nomFS,
				":ext" => $_ext
			)
		);
	}

	public function listeProjets () {
		$projets = $this->arrayFromRequete("SELECT p2.num_projet, p2.nom, p2.chefProjet, p2.budgetPrev, p2.brouillon, p2.num_direction, p2.num_axe
			FROM projet p1, projet p2
			WHERE p1.num_projet <> 0 
				AND p2.num_projetInitial = p1.num_projet 
				AND p2.`version` = (
					SELECT MAX(`version`) 
					FROM projet p3
					WHERE p3.num_projetInitial = p1.num_projet
			)");

		foreach ($projets as $projet) {
			$num_projetLies = $this->num_projetLies($projet->num_projet);

			$projet->fin = $this->varFromRequete(
				"SELECT MAX(date_add(debut, INTERVAL duree MONTH)) 
				FROM etape
				WHERE etape.num_projet IN (".implode(",", $num_projetLies).")"
			);

			$projet->finPrev = $this->varFromRequete(
				"SELECT MAX(date_add(debutInitial, INTERVAL dureeInitial MONTH)) 
				FROM etape
				WHERE etape.num_projet IN (".implode(",", $num_projetLies).")"
			);
		}

		return $projets;
	}

	public function typesEtapes() {
		return $this->arrayFromRequete("SELECT * FROM typeEtape");
	}

	public function axes() {
		return $this->arrayFromRequete("SELECT * FROM axe");
	}

	public function directions() {
		return $this->arrayFromRequete("SELECT * FROM direction");
	}

	public function sousAxes() {
		return $this->arrayFromRequete(
			"SELECT a.couleur, sa.* 
			FROM sousAxe sa
				LEFT JOIN axe a USING (num_axe)"
		);
	}

	public function sauveProjet($_projet, $_num_projet) {
		if ($_num_projet) {
			if (intval($_num_projet) != 0) {
				return $this->updateFromObject($_projet, "projet", "num_projet", $_num_projet);
			}
		}
		$num_projet = $this->insertObject($_projet, "projet");
		if (intval($_projet->num_projetInitial) == 0) {
			$this->execRequete(
				"UPDATE projet SET num_projetInitial = num_projet WHERE num_projet = :num_projet",
				array(":num_projet" => $num_projet)
			);
		}

		return $num_projet;
	}

	public function sauveInstances($_instances, $_num_projet) {
		foreach($_instances as $instance) {
			$fichiers = array();
			if (isset($instance['fichiers'])) {
				$fichiers = $instance['fichiers'];
				unset($instance['fichiers']);
			}
			if (isset($instance['num_instance'])) {		
				$this->updateFromObject($instance, "instance", "num_instance", $instance['num_instance']);
			} else {
				$instance['num_projet'] = $_num_projet;
				$instance['num_instance'] = $this->insertObject($instance, "instance");
			}
			foreach($fichiers as $fichier) {
				$this->execRequete(
					"INSERT IGNORE INTO fichier_instance (num_fichier, num_instance) VALUES (:num_fichier, :num_instance)",
					array(
						':num_fichier' => $fichier['num_fichier'],
						':num_instance' => $instance['num_instance']
					)
				);
			}
		}
	}

	public function sauveEtapes($_etapes, $_num_projet) {
		$retour = [];
		$nums_etape = array(-1);

		$retour[] = $_num_projet;
	
		foreach($_etapes as $etape) {
			if (isset($etape['new'])) {
				unset($etape['new']);
			}
			if (isset($etape['transactions'])) {
				$transactions = $etape['transactions'];
				unset($etape['transactions']);
			} else {
				$transactions = array();
			}

			if (isset($etape['num_etape'])) {
				$num_etape = $etape['num_etape'];
				if ($this->isFirstVersion($_num_projet)) {
					$etape['dureeInitial'] = $etape['duree'];
					$etape['debutInitial'] = $etape['debut'];
				} else {
					if (isset($etape['dureeInitial'])) { unset($etape['dureeInitial']); }
					if (isset($etape['debutInitial'])) { unset($etape['debutInitial']); }
				}
				$this->updateFromObject($etape, "etape", "num_etape", $etape['num_etape']);
				$nums_etape[] = $etape['num_etape'];
			} else {
				$retour[] = $etape;
				$etape['num_projet'] = $_num_projet;
				$etape['dureeInitial'] = $etape['duree'];
				$etape['debutInitial'] = $etape['debut'];
				if (!isset($etape['commentaires'])) { $etape['commentaires'] = ""; }
				if (!isset($etape['objectifs'])) { $etape['objectifs'] = ""; }
				$num_etape = $this->insertObject($etape, "etape");
			}
			foreach($transactions as $transaction) {
				$this->sauveTransaction($transaction, $num_etape);
			}
		}

		$this->execRequete("UPDATE etape SET supprime = 1 WHERE num_etape NOT IN (".implode(",", $nums_etape).") AND num_projet = ".$_num_projet);

		return $retour;
	}

	public function sauveTransaction($_transaction, $_num_etape) {
		if (isset($_transaction['num_transaction'])) {
			$this->updateFromObject($_transaction, "transaction", "num_transaction", $_transaction['num_transaction']);
		} else {
			$_transaction['num_etape'] = $_num_etape;
			$this->insertObject($_transaction, "transaction");
		}
	}

	public function chargeProjet($_num_projet) {
		return $this->objFromRequete(
			"SELECT * FROM projet WHERE num_projet = :num_projet",
			array ( ':num_projet' => $_num_projet )
		);
	}

	public function num_projetPrecedent($_num_projet) {
		$version = $this->varFromRequete("SELECT `version` FROM projet WHERE num_projet = :num_projet", array(':num_projet' => $_num_projet));
		$num_projetInitial = $this->num_projetInitial($_num_projet);
		return $this->varFromRequete(
			"SELECT num_projet FROM projet WHERE num_projetInitial = :num_projetInitial AND `version` = (SELECT MAX(`version`) FROM projet WHERE num_projetInitial = :num_projetInitial AND `version` < :version)",
			array(
				":num_projetInitial" => $num_projetInitial,
				":version" => $version
			)
		);
	}

	public function num_projetSuivant($_num_projet) {
		$version = $this->varFromRequete("SELECT `version` FROM projet WHERE num_projet = :num_projet", array(':num_projet' => $_num_projet));
		$num_projetInitial = $this->num_projetInitial($_num_projet);
		return $this->varFromRequete(
			"SELECT num_projet FROM projet WHERE `version` = (SELECT MIN(`version`) FROM projet WHERE num_projetInitial = :num_projetInitial AND `version` > :version)",
			array(
				":num_projetInitial" => $num_projetInitial,
				":version" => $version
			)
		);
	}

	public function chargeInstances($_num_projet) {
		$num_projetInitial = $this->num_projetInitial($_num_projet);
		//return $num_projetInitial;
		$instances = $this->arrayFromRequete(
			"SELECT * FROM instance WHERE num_projet IN ( SELECT num_projet FROM projet WHERE num_projetInitial = :num_projetInitial)",
			array (":num_projetInitial" => $num_projetInitial)
		);

		foreach($instances as $instance) {
			$instance->fichiers = $this->arrayFromRequete(
				"SELECT * FROM fichier WHERE num_fichier IN (SELECT num_fichier FROM fichier_instance WHERE num_instance = :num_instance)",
				array(":num_instance" => $instance->num_instance)
			);
		}

		return $instances;
	}

	public function chargeEtapes($_num_projet) {
		$num_projetInitial = $this->num_projetInitial($_num_projet);
		$num_projetInitial = $num_projetInitial == -1 ? $_num_projet : $num_projetInitial;
		//return $num_projetInitial;
		$etapes = $this->arrayFromRequete(
			"SELECT * FROM etape WHERE num_projet IN ( SELECT num_projet FROM projet WHERE num_projetInitial = :num_projetInitial OR num_projet = :num_projetInitial) AND NOT supprime",
			array (":num_projetInitial" => $num_projetInitial)
		);
		foreach ($etapes as $etape) {
			$etape->transactions = $this->arrayFromRequete(
				"SELECT * FROM `transaction` WHERE num_etape = :num_etape",
				array(":num_etape" => $etape->num_etape)
			);
		}
		return $etapes;
	}

	public function isLastVersion($_num_projet) {
		$num_version = intval($this->varFromRequete(
			"SELECT version FROM projet WHERE num_projet = :num_projet",
			array (':num_projet' => $_num_projet)
		));

		$lastVersion = intval($this->varFromRequete(
			"SELECT MAX(version) FROM projet WHERE num_projetInitial = :num_projetInitial",
			array ( ':num_projetInitial' => $this->num_projetInitial($_num_projet) ))
		);

		if ($lastVersion > $num_version) {
			return false;
		} else {
			return true;
		}
	}

	public function isFirstVersion($_num_projet) {
		$num_version = intval($this->varFromRequete(
			"SELECT version FROM projet WHERE num_projet = :num_projet",
			array (':num_projet' => $_num_projet)
		));

		$firstVersion = intval($this->varFromRequete(
			"SELECT MIN(version) FROM projet WHERE num_projetInitial = :num_projetInitial",
			array ( ':num_projetInitial' => $this->num_projetInitial($_num_projet) ))
		);

		if ($firstVersion < $num_version) {
			return false;
		} else {
			return true;
		}
	}

	public function valideProjet($_num_projet) {
		return $this->execRequete(
			"UPDATE projet SET brouillon = 0 WHERE num_projet = :num_projet",
			array ( ':num_projet' => $_num_projet )
		);
	}

	public function creeVersion($_num_projet) {
		$projetActuel = $this->objFromRequete(
			"SELECT * FROM projet WHERE num_projet = :num_projet",
			array ( ':num_projet' => $_num_projet )
		);
		unset($projetActuel->num_projet);
		$projetActuel->brouillon = 1;
		$projetActuel->num_projetInitial = $this->varFromRequete(
			"SELECT num_projetInitial FROM projet WHERE num_projet = :num_projet",
			array ( ':num_projet' => $_num_projet )
		);

		$num_projet = $this->insertObject($projetActuel, "projet");

		$this->execRequete(
			"UPDATE projet SET version = version + 1 WHERE num_projet = :num_projet",
			array ( ':num_projet' => $num_projet )
		);

		return $num_projet;
	}
	
	private function num_projetInitial ($_num_projet) {
		return intval($this->varFromRequete(
			"SELECT num_projetInitial FROM projet WHERE num_projet = :num_projet",
			array ( ':num_projet' => $_num_projet )
		));
	}

	private function num_projetLies ($_num_projet) {
		return $this->simpleArrayFromRequete(
			"SELECT num_projet FROM projet WHERE num_projetInitial = :num_projetInitial",
			array(':num_projetInitial' => $this->num_projetInitial($_num_projet))
		);
	}

	private function updateFromObject($_objet, $_table, $_champIndex, $_valIndex) {
		$liste_affectations = array();
		$liste_champs_prefixe = array();
		$liste_valeurs = array();

		foreach ((array) $_objet as $key => $value) {
			if ($key != $_champIndex) {
				$liste_affectations[] = "`".$key."` = :".$key;
				$liste_valeurs[":".$key] = $value;
			}
		}
		//return "UPDATE `".$_table."` SET ".implode(", ", $liste_affectations)." WHERE `".$_champIndex."` = ".$_valIndex;

		$this->execRequete(
			"UPDATE `".$_table."` SET ".implode(", ", $liste_affectations)." WHERE `".$_champIndex."` = ".$_valIndex,
			$liste_valeurs
		);

		return $_valIndex;
	}

	private function insertObject($_objet, $_table) {
		$liste_champs = array();
		$liste_champs_prefixe = array();
		$liste_valeurs = array();

		foreach ((array) $_objet as $key => $value) {
			$liste_champs[] = "`".$key."`";
			$liste_champs_prefixe[] = ":".$key;
			$liste_valeurs[":".$key] = $value;
		}

		$requete = "INSERT INTO `".$_table."` (".implode(", ", $liste_champs).") VALUES (".implode(", ", $liste_champs_prefixe).")";

		return $this->insertFromRequete($requete, $liste_valeurs);;
	}

	// Test exécution requête
	private function testExecution($_rq)
	{
		$infos = $_rq->errorInfo();
		if ($infos[0] != '00000') {
			array_push($this->dbg, $infos);
		}
	}

	// Recuperation d'un tableau d'objet depuis une requete
	private function arrayFromRequete($_requete, $_parametres = array(), $_assoc = false)
	{
		$retour = array();

		$rq = $this->pdo->prepare($_requete);

		if (!$rq) {
			array_push($this->dbg, $this->pdo->errorInfo());
			return false;
		}

		$rq->execute($_parametres);
		$this->testExecution($rq);
		$rq->setFetchMode($_assoc?(PDO::FETCH_ASSOC):(PDO::FETCH_OBJ));

		while ($ligne = $rq->fetch()) {
			$retour[] = $ligne;
		}

		if (count($retour) > 0) {
			return $retour;
		} else {
			return array();
		}
	}

	// Recuperation d'une variable depuis une requete
	private function varFromRequete($_requete, $_parametres = array())
	{
		$rq = $this->pdo->prepare($_requete);

		if (!$rq) {
			array_push($this->dbg, $this->pdo->errorInfo());
			return false;
		}

		$rq->execute($_parametres);

		$this->testExecution($rq);

		$rq->setFetchMode(PDO::FETCH_NUM);

		if ($ligne = $rq->fetch()) {
			return $ligne[0];
		} else {
			return null;
		}
	}

	// Recuperation d'un objet depuis une requete
	private function objFromRequete($_requete, $_parametres = array())
	{
		$rq = $this->pdo->prepare($_requete);

		if (!$rq) {
			array_push($this->dbg, $this->pdo->errorInfo());
			return false;
		}

		$rq->execute($_parametres);

		$this->testExecution($rq);

		$rq->setFetchMode(PDO::FETCH_OBJ);

		if ($ligne = $rq->fetch()) {
			return $ligne;
		} else {
			return null;
		}
	}

	// Recuperation d'un tableau de string depuis un requete
	private function simpleArrayFromRequete($_requete, $_parametres = array())
	{
		$retour = array();

		$rq = $this->pdo->prepare($_requete);

		if (!$rq) {
			array_push($this->dbg, $this->pdo->errorInfo());
			return false;
		}

		$rq->execute($_parametres);

		$this->testExecution($rq);

		$rq->setFetchMode(PDO::FETCH_NUM);

		while ($ligne = $rq->fetch()) {
			$retour[] = $ligne[0];
		}

		return $retour;
	}

	// Recuperation d'un tableau de string depuis un requete
	private function fieldsArrayFromRequete($_requete, $_parametres = array())
	{
		$retour = array();

		$rq = $this->pdo->prepare($_requete);

		if (!$rq) {
			array_push($this->dbg, $this->pdo->errorInfo());
			return false;
		}

		$rq->execute($_parametres);

		$this->testExecution($rq);

		$rq->setFetchMode(PDO::FETCH_NUM);

		if ($ligne = $rq->fetch()) {
			$retour = $ligne;
		}

		return $retour;
	}

	// Execution d'un requete
	private function execRequete($_requete, $_parametres = array())
	{
		$rq = $this->pdo->prepare($_requete);

		if (!$rq) {
			array_push($this->dbg, $this->pdo->errorInfo());
			return false;
		}

		$rq->execute($_parametres);

		$this->testExecution($rq);

		return true;
	}

	// Insere un élément et retourne l'id
	private function insertFromRequete($requete, $parametres = array())
	{
		$rq = $this->pdo->prepare($requete);

		if (!$rq) {
			array_push($this->dbg, $this->pdo->errorInfo());
			return false;
		}

		$rq->execute($parametres);

		$this->testExecution($rq);

		return $this->pdo->lastInsertId();
	}
}