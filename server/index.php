<?php
session_start();

header('Access-Control-Allow-Origin:  '.$_SERVER['HTTP_ORIGIN']);
header('Access-Control-Allow-Methods: GET, POST'); 
header('Access-Control-Allow-Credentials: true');
header('Content-Type: application/json');

require_once('includes/globals.inc.php');
require_once('includes/class.bdd.php');
require_once('includes/class.user.php');


$params 			= explode('/', $_GET[ACTION]);
$retour 			= array();
$action 			= array_shift($params);

$bdd				= new Bdd();
$user				= new User($bdd);

switch ($action) {
	
	case IPUSER :
		$user->loadInfosFromIp();
		$retour[USER] = $user->toPublicObject();
		break;
		
	case LOGIN :
		$user->authenticate($_POST[LOGIN], $_POST[PASSWORD]);
		$retour[USER] = $user->toPublicObject();
		break;
	
	case CONSTANTS :
		header('Content-Type: application/javascript');
		define ('SERVER_URL', str_replace(CONSTANTS, "", str_replace(CONSTANTS."/", "", "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]")));
		die('var C = '.json_encode(get_defined_constants(true)['user']));
		break;

	case LISTE_PROJETS :
		$retour["test"] = "coucou4";
		break;

	case SAUVE_PROJET :
		// $retour["post"] = $_POST;
		$retour[NUM_PROJET] = $bdd->sauveProjet($_POST[PROJET], $_POST[NUM_PROJET]);
		$bdd->sauveInstances($_POST[INSTANCES] ,$retour[NUM_PROJET]);
		$retour[ETAPES] = $bdd->sauveEtapes($_POST[ETAPES] ,$retour[NUM_PROJET]);
		break;

	case CHARGE_PROJET :
		$retour[PROJET] = $bdd->chargeProjet($_POST[NUM_PROJET]);
		$retour[INSTANCES] = $bdd->chargeInstances($_POST[NUM_PROJET]);
		$retour[ETAPES] = $bdd->chargeEtapes($_POST[NUM_PROJET]);
		$retour[IS_LAST_VERSION] = $bdd->isLastVersion($_POST[NUM_PROJET]);
		break;

	case CREER_VERSION :
		$retour[NUM_PROJET] = $bdd->creeVersion($_POST[NUM_PROJET]);
		break;

	case VALIDE_PROJET :
		$bdd->valideProjet($_POST[NUM_PROJET]);
		break;
}

$retour["DEBUG"] = $bdd->getDbg();

die(json_encode($retour));
