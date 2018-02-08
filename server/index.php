<?php
session_start();

if (isset($_SERVER['HTTP_ORIGIN'])) {
	header('Access-Control-Allow-Origin:  '.$_SERVER['HTTP_ORIGIN']);
	header('Access-Control-Allow-Methods: GET, POST'); 
	header('Access-Control-Allow-Credentials: true');
}
header('Content-Type: application/json');

if (isset($_SERVER['REQUEST_METHOD'])) {

	if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

			if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])) {
					header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
			}

			if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) {
					header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
			}

			exit;
	}
}

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
		$retour[LISTE_PROJETS] = $bdd->listeProjets();
		break;

	case SAUVE_PROJET :
		$_POST[INSTANCES] = isset($_POST[INSTANCES]) ? $_POST[INSTANCES] : array();
		$_POST[ETAPES] = isset($_POST[ETAPES]) ? $_POST[ETAPES] : array();

		$retour[NUM_PROJET] = $bdd->sauveProjet($_POST[PROJET], $_POST[NUM_PROJET]);
		$bdd->sauveInstances($_POST[INSTANCES] ,$retour[NUM_PROJET]);
		$retour[ETAPES] = $bdd->sauveEtapes($_POST[ETAPES] ,$retour[NUM_PROJET]);

		if ($_POST[VALIDE_PROJET] == '1') {
			$bdd->valideProjet($retour[NUM_PROJET]);
		}

		break;

	case CHARGE_PROJET :
		$retour[PROJET] = $bdd->chargeProjet($_POST[NUM_PROJET]);
		$retour[INSTANCES] = $bdd->chargeInstances($_POST[NUM_PROJET]);
		$retour[ETAPES] = $bdd->chargeEtapes($_POST[NUM_PROJET]);
		$retour[IS_LAST_VERSION] = $bdd->isLastVersion($_POST[NUM_PROJET]);
		$retour[PRECEDENT] = $bdd->num_projetPrecedent($_POST[NUM_PROJET]);
		$retour[SUIVANT] = $bdd->num_projetSuivant($_POST[NUM_PROJET]);
		break;

	case CREER_VERSION :
		$retour[NUM_PROJET] = $bdd->creeVersion($_POST[NUM_PROJET]);
		break;
	
	case TYPES_ETAPES :
		$retour[TYPES_ETAPES] = $bdd->typesEtapes();
		break;
	
	case AXES:
		$retour[AXES] = $bdd->axes();
		$retour[SOUS_AXES] = $bdd->sousAxes();
		break;

	case DIRECTIONS:
		$retour[DIRECTIONS] = $bdd->directions();
		break;

	case UPLOAD :
		require('includes/Uploader.php');
		require('includes/fansi.inc.php');

		$upload_dir 					= FILES_FOLDER.'/';
		$Upload 							= new FileUpload(FICHIER_UPLOAD);

		$realName							= $Upload->getFileName();
		$ext 									= $Upload->getExtension();

		$Upload->newFileName 	= nomFichier($upload_dir, $realName);
		$result 							= $Upload->handleUpload($upload_dir);
		
		$retour[NUM_FICHIER] 	= $bdd->sauveFichier($realName, $Upload->getFileName(), $ext);
		$retour[NOM_FICHIER] 	= $realName;
		$retour[EXTENSION] 		= $ext;

	/*	if (!$result) {
			$retour['success'] = false;
			$retour['msg'] = $Upload->getErrorMsg();  
		} else {
			$retour['success'] = true;
			$retour['file'] = $Upload->getFileName();
		}*/
		break;
}

$retour["DEBUG"] = $bdd->getDbg();

die(json_encode($retour));
