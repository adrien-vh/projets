<?php
require_once('globals.inc.php');
require_once('utiles.inc.php');
require_once('adLDAP.php');

class User {
	private $bdd 							= null;
	
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
		$ad 						= new adLDAP();
		$authenticated 	= $ad->authenticate($login, $password);
		
		if ($authenticated) {
			$this->initSession($login);
			return true;
		}
		
		return false;
	}
	
	public function loadInfosFromIp () {
		$fichierIP = LOGINS_FOLDER.'/'.getIP().'.'.IP_FILE_EXT;
		
		if (file_exists($fichierIP)) {
			$this->initSession(trim(file_get_contents($fichierIP)));
		} else {
			$this->resetSession();
		}
	}
	
	public function toPublicObject () {
		$array = array(
			CONNECTE 				=> $_SESSION[CONNECTE],
			LOGIN 					=> $_SESSION[LOGIN],
			FULL_NAME 			=> $_SESSION[FULL_NAME],
			DROITS_PROJETS 	=> $_SESSION[DROITS_PROJETS]
		);
		
		return json_decode(json_encode($array), FALSE);
	}

	public function rechargeDroits () {
		$_SESSION[DROITS_PROJETS] = $this->chargeDroitsProjets();
	}

	public function niveauDroit ($_num_projet) {
		$_num_projetInitial = $this->bdd->num_projetInitial($_num_projet);
		foreach ($_SESSION[DROITS_PROJETS] as $droit) {
			if ($droit->num_projet == $_num_projetInitial) {
				return intval($droit->niveau);
			}
		}
		return -1;
	}

	private function initSession ($_login) {
		$_SESSION[CONNECTE] 			= true;
		$_SESSION[LOGIN] 					= $_login;
		$_SESSION[FULL_NAME] 			= $this->bdd->getUserFullName($_login);
		$_SESSION[DROITS_PROJETS] = $this->chargeDroitsProjets();
	}

	private function resetSession() {
		$_SESSION[CONNECTE] 			= null;
		$_SESSION[LOGIN] 					= null;
		$_SESSION[FULL_NAME] 			= null;
		$_SESSION[DROITS_PROJETS] = null;
	}

	private function chargeDroitsProjets () {
		if ($_SESSION[CONNECTE]) {
			return $this->bdd->droitsParUtilisateur($_SESSION[LOGIN]);
		} else {
			return null;
		}
	}
}