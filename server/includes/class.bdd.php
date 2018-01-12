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