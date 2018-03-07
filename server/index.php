<?php
if (strstr($_SERVER['HTTP_ORIGIN'], 'localhost')) {
	session_id(substr(base64_encode($_SERVER['REMOTE_ADDR']), 0, 6));
}
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


$params 								= explode('/', $_GET[ACTION]);
$retour 								= array();
$retour[MESSAGES]				= array();
$retour[INFOS_FICHIERS] = array();
$action 								= array_shift($params);
$retour["ORIGIN"]				= $_SERVER['HTTP_ORIGIN'];

$bdd				= new Bdd();
$user				= new User($bdd);

switch ($action) {
	
	case IPUSER :
		if ($_SESSION[CONNECTE]) {
			$user->rechargeDroits();
			$retour[USER] = $user->toPublicObject();
		} else {
			$user->loadInfosFromIp();
			$retour[IPUSER] = $user->toPublicObject();
			$retour[USER] 	= $user->toPublicObject();
		}
		break;
		
	case LOGIN :
		$user->authenticate($_POST[LOGIN], $_POST[PASSWORD]);
		$retour[USER] = $user->toPublicObject();
		if ($retour[USER]->login) {
			$retour[MESSAGES][] = array( type => SUCCESS, text => "Connexion réussie !" );
		} else {
			$retour[MESSAGES][] = array( type => ERROR, text => "Erreur d'authentification" );
		}
		break;
	
	case LOGOUT :
		$_SESSION[CONNECTE] = false;
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
		$user->rechargeDroits();
		if ($user->niveauDroit($_POST[NUM_PROJET]) < 1) {
			$retour[MESSAGES][] = array( type => ERROR, text => "Vous n'avez pas le de modifier ce projet" );
		} else {
			$_POST[PROJET][FINANCEMENT] = isset($_POST[PROJET][FINANCEMENT]) ? $_POST[PROJET][FINANCEMENT] : array();
			$_POST[PROJET][DROITS] = isset($_POST[PROJET][DROITS]) ? $_POST[PROJET][DROITS] : array();
			$_POST[PROJET][ETAPES] = isset($_POST[PROJET][ETAPES]) ? $_POST[PROJET][ETAPES] : array();

			if (intval($_POST[PROJET]->num_projetInitial) == 0) {
				$_POST[PROJET]['createur'] = $_SESSION[LOGIN];
			}

			$retour[NUM_PROJET] = $bdd->sauveProjet($_POST[PROJET], $_POST[NUM_PROJET]);
			
			$bdd->sauveFinancement($_POST[PROJET][FINANCEMENT] ,$retour[NUM_PROJET]);
			$bdd->sauveDroits($_POST[PROJET][DROITS], $retour[NUM_PROJET]);
			$retour[ETAPES] = $bdd->sauveEtapes($_POST[PROJET][ETAPES] ,$retour[NUM_PROJET]);

			if ($_POST[VALIDE_PROJET] == '1') {
				$bdd->valideProjet($retour[NUM_PROJET]);
			}

			if (count($bdd->getDbg()) == 0) {
				$retour[MESSAGES][] = array( type => SUCCESS, text => "Sauvegarde réussie !" );
			} else {
				$retour[MESSAGES][] = array( type => ERROR, text => "Échec de la sauvegarde. (".$bdd->getDbg()[0][2].")" );
			}
		}
		break;

	case CHARGE_PROJET :
		$user->rechargeDroits();
		if ($user->niveauDroit($_POST[NUM_PROJET]) < 0) {
			$retour[MESSAGES][] = array( type => ERROR, text => "Vous n'avez pas accès à ce projet" );
		} else {
			$retour[PROJET] = $bdd->chargeProjet($_POST[NUM_PROJET]);
			$retour[INSTANCES] = $bdd->chargeInstances($_POST[NUM_PROJET]);
			$retour[PROJET]->{FINANCEMENT} = $bdd->chargeFinancements($_POST[NUM_PROJET]);
			$retour[PROJET]->{ETAPES} = $bdd->chargeEtapes($_POST[NUM_PROJET]);

			foreach ($retour[PROJET]->{ETAPES} as $etape) {
				$etape->{INSTANCES} = $bdd->chargeInstances($etape->num_etape);

				foreach ($etape->{INSTANCES} as $instance) {
					foreach ($instance->fichiers as $fichier){
						$retour[INFOS_FICHIERS][] = $bdd->infosFichier($fichier);
					}
				}
			}

			$retour[IS_LAST_VERSION] = $bdd->isLastVersion($_POST[NUM_PROJET]);
			$retour[PRECEDENT] = $bdd->num_projetPrecedent($_POST[NUM_PROJET]);
			$retour[SUIVANT] = $bdd->num_projetSuivant($_POST[NUM_PROJET]);
			if (intval($_POST[NUM_PROJET]) != 0) {
				$retour[PROJET]->{DROITS} = $bdd->droits($_POST[NUM_PROJET]);
			} else {
				$retour[PROJET]->{DROITS} = array();
			}

			
			foreach ($retour[PROJET]->{ETAPES} as $etape) {
				if (intval($etape->validationFichier) != -1) {
					$retour[INFOS_FICHIERS][] = $bdd->infosFichier($etape->validationFichier);
				}

			}
		}

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

		$upload_dir 						= FILES_FOLDER.'/';
		$Upload 								= new FileUpload(FICHIER_UPLOAD);

		$realName								= $Upload->getFileName();
		$ext 										= $Upload->getExtension();

		$Upload->newFileName 		= nomFichier($upload_dir, $realName);
		$result 								= $Upload->handleUpload($upload_dir);
		
		$retour[NUM_FICHIER] 		= $bdd->sauveFichier($realName, $Upload->getFileName(), $ext);
		$retour[NOM_FICHIER] 		= $realName;
		$retour[EXTENSION] 			= $ext;
		$retour[NOM_FICHIER_FS]	= $Upload->getFileName();
		$nomFichier							= $Upload->getFileName();

		if (in_array(strtolower($retour[EXTENSION]), array('docx', 'doc', 'odt', 'xlsx', 'xls', 'pptx', 'ppt'))) {
			copy(FILES_FOLDER.'/'.$nomFichier, PDF_FOLDER.'/'.$retour[NUM_FICHIER].'.'.$retour[EXTENSION]);
		}

	/*	if (!$result) {
			$retour['success'] = false;
			$retour['msg'] = $Upload->getErrorMsg();  
		} else {
			$retour['success'] = true;
			$retour['file'] = $Upload->getFileName();
		}*/
		break;

	case UTILISATEURS:
		//$retour['q'] = $_GET;
		if (isset($_GET['q'])) {
			die(json_encode($bdd->utilisateurs($_GET['q'])));
		}
		if (isset($_GET['rq'])) {
			die(json_encode($bdd->nomUtilisateur($_GET['rq'])));
		}
		$retour[UTILISATEURS] = $bdd->utilisateurs('');
		break;
}

$retour[DEBUG] = $bdd->getDbg();
$retour['SESSION'] = $_SESSION;

die(json_encode($retour));
