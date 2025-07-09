<?php
require_once '../config/db.php';

// Tu utilises FPDF mais tu instancies TCPDF - il faut choisir !
require_once ('../fpdf186/fpdf.php');

function getCandidatData($id) {
    global $pdo;
    // Ajouter l'id dans le SELECT si tu veux l'afficher
    $stmt = $pdo->prepare("SELECT id, nom, prenom, email, notes, score, date_depot FROM users WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

if(isset($_GET['id']) && !empty($_GET['id'])) {
    $candidat_id = $_GET['id'];
    $candidat = getCandidatData($candidat_id);

    if($candidat) {
        // Utiliser FPDF puisque tu l'as inclus
        $pdf = new FPDF();
        $pdf->AddPage();

        // Contenu personnalisé
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 15, 'Rapport Personnel', 0, 1, 'C');

        $pdf->SetFont('Arial', '', 12);
        $pdf->Ln(10);

        // Corriger l'affichage de l'ID
        $pdf->Cell(0, 10, 'ID: ' . $candidat['id'], 0, 1);
        $pdf->Cell(0, 10, 'Nom: ' . $candidat['nom'], 0, 1);
        $pdf->Cell(0, 10, 'Prenom: ' . $candidat['prenom'], 0, 1);
        $pdf->Cell(0, 10, 'Email: ' . $candidat['email'], 0, 1);
        $pdf->Cell(0, 10, 'Score: ' . $candidat['score'], 0, 1);
        $pdf->Cell(0, 10, 'Date de depot: ' . $candidat['date_depot'], 0, 1);
        $pdf->Ln(5);

        // MultiCell pour les notes longues
        $pdf->MultiCell(0, 10, 'Notes: ' . $candidat['notes']);

        // Téléchargement automatique
        $filename = 'rapport_' . $candidat['nom'] . '_' . $candidat['prenom'] . '.pdf';
        $pdf->Output('D', $filename); // FPDF utilise 'D' en premier paramètre
    } else {
        echo "Candidat non trouve";
    }
} else {
    echo "ID manquant";
}
?>