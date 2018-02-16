<?php
	/*session_start();
	require_once('includes/globals.inc.php');
	// require_once('includes/bdd.inc.php');
	// require_once('includes/utiles.inc.php');
	require_once('includes/fansi.inc.php');
	require_once('includes/class.geddoc.php');
	require_once('includes/class.gedbdd.php');
	
	if (!$_SESSION[CONNECTE]) {
		exit;
	}
	
	$bdd 	= new GedBdd();
	$doc 	= new GedDoc(isset($_GET[NUM_DOC]) ? $_GET[NUM_DOC] : $_GET[NOM_DOC], $bdd, isset($_GET[NOM_DOC]));
  $file 	= $doc->cheminPdf();*/
  
  $file = '/var/www/pdf/'.$_GET['num_fichier'].'.pdf';
		
	// die($doc->cheminPdf());
		
	header('Content-Description: File Transfer');
	header('Content-Type: application/pdf');
 
	//header('Content-Disposition: attachment; filename='.basename($file));
	header('Content-Disposition: inline; filename='.basename($file));
	header('Content-Transfer-Encoding: binary');
	header('Expires: 0');
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Pragma: public');
	header('Content-Length: ' . filesize($file));
	header('X-Content-Type-Options: nosniff');
	ob_clean();
	flush();
	readfile($file);
	exit;