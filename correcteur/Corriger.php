<?php


function nettoyer(string $donnee) {
    return trim(htmlspecialchars($donnee, ENT_QUOTES, 'UTF-8'));
}


function registerNote(PDO $pdo, int $id_copie, int $id_correcteur, float $note, string $commentaire, string $critere) {

    if ($note < 0 || $note > 20) {
        echo " Note invalide.";
        exit;
    }

    $commentaire = nettoyer($commentaire);
    $critere = nettoyer($critere);


    $pdo->beginTransaction();

    try {

        $pdo->prepare("
            INSERT INTO grille_evaluation (id_copie, id_correcteur, note, commentaire, critere)
            VALUES (?, ?, ?, ?, ?)
        ")->execute([$id_copie, $id_correcteur, $note, $commentaire, $critere]);


        $pdo->prepare("
            UPDATE copies
            SET statut = 'corrigee', corrected_by = ?
            WHERE id = ?
        ")->execute([$id_correcteur, $id_copie]);

        $pdo->commit();
        echo "Correction enregistrée avec succès.";
    } catch (Exception $e) {
        $pdo->rollBack();
        echo " Échec lors de l’enregistrement : " . $e->getMessage();
    }
}
