<?php
require_once('globals.inc.php');
require_once('utiles.inc.php');
require_once('adLDAP.php');

class User {
	private $login		= null;
	private $fullName	= null;
	private $bdd 		= null;
	
	// private $userInfos 		= null;
	// private $isConnected 	= false;
	
	// public function getUserInfos () { return $this->userInfos; }
	
	public function __construct ($bdd) {
		$this->initialize($bdd);
	}
		
	private function initialize ($bdd) {
		$this->bdd = $bdd;
	}
	
	public function authenticate ($login, $password) {
		$ad 			= new adLDAP();
		$authenticated 	= $ad->authenticate($login, $password);
		
		if ($authenticated) {
			$this->login = $login;
			$this->fullName = $this->bdd->getUserFullName($this->login);
			return true;
		}
		
		return false;
		
		/*if ($authenticated) {
			
			$_SESSION = array_merge($_SESSION, $bdd->userInfos($login));
			
			$_SESSION[NIVEAU] 	= intval($_SESSION[NIVEAU]);
			$this->isConnected	= $_SESSION[CONNECTE] = ($_SESSION[NIVEAU] >= 0);
			
			$this->userInfos 	= array_merge(array(), $_SESSION);
			return true;
		}
		
		return false;*/
	}
	
	public function loadInfosFromIp () {
		$fichierIP = LOGINS_FOLDER.'/'.getIP().'.'.IP_FILE_EXT;
		
		if (file_exists($fichierIP)) {
			$this->login = trim(file_get_contents($fichierIP));
			$this->fullName = $this->bdd->getUserFullName($this->login);
		}
	}
	
	public function toPublicObject () {
		$array = array(
			'login' => $this->login,
			'fullName' => $this->fullName
		);
		
		return json_decode(json_encode($array), FALSE);
	}
}