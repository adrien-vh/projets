<?php
	session_start();
	require_once('includes/globals.inc.php');
	require_once('includes/class.bdd.php');
	
	/*if (!$_SESSION['connecte']) {
		exit;
	}*/
	
	$bdd 	= new Bdd();
	$file 	= FILES_FOLDER.'/'.$bdd->fileName($_GET['num_fichier']);;
	
	header('Content-Description: File Transfer');
	header('Content-Type: application/octet-stream');
 
	header('Content-Disposition: attachment; filename='.basename($file));
	//header('Content-Disposition: inline; filename='.basename($file));
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
