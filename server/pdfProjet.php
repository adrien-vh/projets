<?php

define('__MARGES_LATERALES__',  10);
define('__MARGES_HAUTBAS__',    10);
define('__LARGEUR_UTILE__',			210 - 2 * __MARGES_LATERALES__);

if (strstr($_SERVER['HTTP_ORIGIN'], 'localhost')) {
	session_id(substr(base64_encode($_SERVER['REMOTE_ADDR']), 0, 6));
}
session_start();

require('includes/fpdf/fpdfHtml.php');
require_once('includes/globals.inc.php');
require_once('includes/class.bdd.php');
require_once('includes/class.user.php');

$bdd	      = new Bdd();
$user	      = new User($bdd);
$num_projet = '101';

$Projet                 = $bdd->chargeProjet($num_projet);
$Projet->{FINANCEMENT}  = $bdd->chargeFinancements($num_projet);
$Projet->{ETAPES}       = $bdd->chargeEtapes($num_projet);

$pdf = new PDF_HTML();
$pdf->AddFont('Calibri');
$pdf->AddFont('Calibri','I');
$pdf->AddFont('Calibri','B');

$pdf->AddPage('P', 'A4');
$pdf->SetFont('calibri','',20);	

$pdf->SetTitre(toPdf($Projet->nom));

$pdf->SetXY(__MARGES_LATERALES__, __MARGES_HAUTBAS__);
$pdf->Cell(210 - 2 * __MARGES_LATERALES__ ,10,toPdf($Projet->nom), '', 2, 'C');

titre($pdf, 'Présentation du projet');

ligneLabelValeur($pdf, 'Axe / Sous-axe', $bdd->nomAxe($Projet->num_axe).' / '.$bdd->nomSousAxe($Projet->num_sousAxe));

demiLigneLabelValeur($pdf, 'Budget prévisionnel', $Projet->budgetPrev.' k€');
demiLigneLabelValeur($pdf, 'Durée prévisionnelles', $Projet->dureePrev.' mois');
$pdf->Ln();
$pdf->SetY($pdf->GetY() + 2);

labelValeur($pdf, 'Descriptif sommaire :', $Projet->description, true);

ligneLabelValeur($pdf, 'Pilote politique du projet', $Projet->pilotePolitique);
ligneLabelValeur($pdf, 'Chef de projet', ucwords(strtolower($bdd->getUserFullName($Projet->chefProjet))));
ligneLabelValeur($pdf, 'Équipe projet interne', $Projet->equipeProjet);
ligneLabelValeur($pdf, 'Direction', $bdd->nomDirection($Projet->num_direction));
ligneLabelValeur($pdf, 'Instances de pilotage', $Projet->instances);
labelValeur($pdf, 'Partenaires externes impliqués :', $Projet->partenaires, true);

titre($pdf, 'Objectifs');
html($pdf, $Projet->objectifs);

$total = 0;

foreach ($Projet->{FINANCEMENT} as $financement) {
	$financement->type 		= (intval($financement->recette) == 1 ? 'Recette' : 'Dépense').(intval($financement->fonctionnement) == 1 ? ' de fonctionnement' : " d'investissement");
	$financement->date 		= formatDate($financement->date);
	$total 								= intval($financement->recette) == 1 ? $total + intval($financement->montant) :  $total - intval($financement->montant);
	$financement->montant = (intval($financement->recette) == 1 ? '+ ' : '- ').$financement->montant.' k€';
	
}

titre($pdf, "Financement (".$total." k€)");

$pdf->SetFont('calibri','',10);	

tableau(
	$pdf,
	$Projet->{FINANCEMENT},
	array(60, 50, 30, 20),
	array('Intitulé', 'Type', 'Date', 'Montant'),
	array('intitule', 'type', 'date', 'montant')
);

titre($pdf, "Calendrier");

$nbEtapes = count($Projet->{ETAPES});
foreach($Projet->{ETAPES} as $etape) {
	$pdf->SetFont('calibri','U',12);	
	$pdf->Cell(__LARGEUR_UTILE__, 8, toPdf($etape->nom));
	$pdf->Ln();
	//titre($pdf, $etape->nom);
}


$pdf->Output('I', '', true);
//$pdf->Output('D', 'test.pdf');

function toPdf ($text) {
	return iconv('UTF-8', 'windows-1252', $text);
}

function ligneLabelValeur ($pdf, $label, $valeur) {
	$pdf->SetFont('calibri','B',10);
	$pdf->Cell(__LARGEUR_UTILE__ / 4, 5, toPdf($label." :"), 0, 0, 'L');
	$pdf->SetFont('calibri','',10);
	$pdf->Cell(3 * __LARGEUR_UTILE__ / 4, 5, toPdf($valeur), 0, 1);
	$pdf->SetY($pdf->GetY() + 2);
}

function demiLigneLabelValeur ($pdf, $label, $valeur) {
	$pdf->SetFont('calibri','B',10);
	$pdf->Cell(__LARGEUR_UTILE__ / 4, 5, toPdf($label." :"), 0, 0, 'L');
	$pdf->SetFont('calibri','',10);
	$pdf->Cell(__LARGEUR_UTILE__ / 4, 5, toPdf($valeur), 0, 0);
	//$pdf->SetY($pdf->GetY() + 2);
}

function labelValeur ($pdf, $label, $valeur, $html = false) {
	titre2($pdf, $label);
	if ($html) { html($pdf, $valeur); }
	else { 
		
		$pdf->SetFont('calibri','',10);
		$pdf->write(5, toPdf($valeur));
		$pdf->Ln();
	}
}

function tableau ($pdf, $objets, $largeurs, $entetes, $champs) {
	$nbElements = min(count($largeurs), count($entetes), count($champs));

	$largeurTotale = 0;
	for ($i = 0; $i < $nbElements; $i++) {
		$largeurTotale += $largeurs[$i];
	}

	$margeGauche = (210 - $largeurTotale) / 2;

	$pdf->SetX($margeGauche);
	$pdf->SetFillColor(200);
	for ($i = 0; $i < $nbElements; $i++) {
		$pdf->cell($largeurs[$i], 5, toPdf($entetes[$i]), 'LBTR', 0, '', true);
	}

	$pdf->ln();

	foreach ($objets as $objet) {
		$yCourant =	$pdf->GetY();
		$ySuivant = $yCourant;
		$xCourant = $margeGauche;

		for ($i = 0; $i < $nbElements; $i++) {
			$pdf->SetY($yCourant);
			$pdf->SetX($xCourant);
			$pdf->MultiCell($largeurs[$i], 4, toPdf($objet->{$champs[$i]}), 0, 'L');
			$xCourant += $largeurs[$i];
			$ySuivant = max($ySuivant, $pdf->GetY());
		}

		$xCourant = $margeGauche;

		for ($i = 0; $i < $nbElements; $i++) {
			$pdf->SetY($yCourant);
			$pdf->SetX($xCourant);
			$pdf->MultiCell($largeurs[$i], $ySuivant - $yCourant, ' ', 'LBTR');
			$xCourant += $largeurs[$i];
		}

		$pdf->SetY($ySuivant);
	}
	$pdf->ln();
}

function toHtml ($text) {
	return toPdf(html_entity_decode(str_replace(array('<p>&nbsp;</p>', '&rsquo;', '&hellip;'), array('', "'", '...'), $text )));
}

function titre ($pdf, $text) {
	$pdf->SetFont('calibri','',15);	
	$pdf->Cell(__LARGEUR_UTILE__ ,10, toPdf($text), 'B', 2, 'L');
	$pdf->SetY($pdf->GetY() + 5);
}

function titre2 ($pdf, $text) {
	$pdf->SetFont('calibri','B',10);	
	$pdf->Cell(__LARGEUR_UTILE__ ,5, toPdf($text), '', 2, 'L');
}

function html($pdf, $text) {
	$pdf->SetY($pdf->GetY() - 10);
	$pdf->SetFont('calibri','',10);	
	$pdf->WriteHTML(toHtml($text));
	$pdf->SetY($pdf->GetY() + 8);
}

function formatDate($date) {
	$listeMois = array('janvier', 'février', 'mars', 'avril', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre');
	list($annee, $mois, $jour) = explode("-", $date);

	return $listeMois[intval($mois) - 1].' '.$annee;
}