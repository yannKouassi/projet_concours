<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../config/db.php';


require_once ('../fpdf186/fpdf.php');
$id = $_GET['id'];
function getCandidatData($id) {
    global $pdo;

    $stmt = $pdo->prepare("
    SELECT u.id, u.nom, u.prenom, u.email,
           g.note AS notes, g.commentaire, g.critere, c.date_depot,c.statut,c.id AS id_copie
    FROM users u
    JOIN copies c ON u.id = c.id_candidat
    JOIN grille_evaluation g ON g.id_copie = c.id
    WHERE c.id = ?
");

    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

if(isset($_GET['id']) && !empty($_GET['id'])) {
    $candidat_id = $_GET['id'];

    $candidat = getCandidatData($candidat_id);


    if($candidat) {
        if ($candidat['statut'] !== 'corrigee') {
            echo "La copie n'est pas encore corrigée. Rapport indisponible.";
            exit;
        }

        $pdf = new FPDF();
        $pdf->AddPage();


        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 15, 'Rapport Personnel', 0, 1, 'C');

        $pdf->SetFont('Arial', '', 12);
        $pdf->Ln(10);


        $pdf->Cell(0, 10, 'ID: ' . $candidat['id'], 0, 1);
        $pdf->Cell(0, 10, 'Nom: ' . $candidat['nom'], 0, 1);
        $pdf->Cell(0, 10, 'Prenom: ' . $candidat['prenom'], 0, 1);
        $pdf->Cell(0, 10, 'Email: ' . $candidat['email'], 0, 1);
        $pdf->MultiCell(0, 10, 'Notes: ' . $candidat['notes']);
        $pdf->MultiCell(0, 10, utf8_decode('Commentaire: ' . $candidat['commentaire']), 0, 1);
        $pdf->MultiCell(0, 10,utf8_decode( 'Critere: ' . $candidat['critere']), 0, 1);
        $pdf->Cell(0, 10, 'Date de depot: ' . $candidat['date_depot'], 0, 1);
        $pdf->Ln(5);




        $filename = 'rapport_' . $candidat['nom'] . '_' . $candidat['prenom'] . '.pdf';
        $pdf->Output('D', $filename);
    } else {
        echo "<h3 style='color:#4b5563;' > La copie n'est pas encore corrigée. Rapport indisponible.</h3>";
        echo "<p><a href='Profil.php' style='color: blue; text-decoration: underline;'>⬅ Retour au profil</a></p>";
        exit;

    }
} else {
    echo "ID manquant";
}
?>