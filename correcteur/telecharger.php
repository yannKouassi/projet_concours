<?php global $pdo;
require_once __DIR__ . '/../config/db.php';

if (!isset($_GET['identifiant_anonyme'])) {
    die(" Identifiant anonyme manquant.");
}

$identifiant = basename($_GET['identifiant_anonyme']);


$stmt = $pdo->prepare("SELECT format_fichier FROM copies WHERE identifiant_anonyme = ?");
$stmt->execute([$identifiant]);
$copie = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$copie || empty($copie['format_fichier'])) {
    die("Copie introuvable ou extension manquante.");
}

$extension = ltrim($copie['format_fichier'], '.');
$fichierComplet = $identifiant . '.' . $extension;
$chemin = __DIR__ . '/../data/' . $fichierComplet;

if (!file_exists($chemin)) {
    var_dump($chemin);
    die("Le fichier '{$fichierComplet}' est introuvable sur le serveur.");
}

header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $fichierComplet . '"');
header('Content-Length: ' . filesize($chemin));
readfile($chemin);
exit;
