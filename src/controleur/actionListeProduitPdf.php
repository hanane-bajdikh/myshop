<?php

use Spipu\Html2Pdf\Html2Pdf;
use Spipu\Html2Pdf\Exception\Html2PdfException;

function actionListeProduitPdf($twig, $db){
    $produit = new Produit($db);
    $liste = $produit->select(); // Récupère tous les produits

    $html = $twig->render('produit-liste-pdf.html.twig', ['liste' => $liste]);

    try {
        ob_end_clean(); // Vide le buffer si quelque chose a déjà été affiché
        $html2pdf = new Html2Pdf('P', 'A4', 'fr');
        $html2pdf->writeHTML($html);
        $html2pdf->output('liste-des-produits.pdf');
    } catch (Html2PdfException $e) {
        echo 'Erreur PDF : ' . $e->getMessage();
    }
}
