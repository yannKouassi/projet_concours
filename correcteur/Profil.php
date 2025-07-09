
<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'correcteur') {
header('Location: ../views/public/accueil.php');
exit;
}
global $pdo;
require_once __DIR__ . '/../config/db.php';
require_once  'Corriger.php';


$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $note = isset($_POST['note']) ? (float) $_POST['note'] : 0.0;
    $commentaire = $_POST['commentaire'] ?? '';
    $critere = $_POST['critere'] ?? '';

  $id_correcteur=  $_SESSION['user']['id'];

    $result = registerNote(
        $pdo,
        $_SESSION['copieId'],
        $id_correcteur,
        $note,
        $commentaire,
        $critere

    );


    $_SESSION['flash_message'] = "Note créée avec succès.";
    header('Location: Profil.php');
    exit;

}
?>
<?php

global $pdo;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/db.php';

$stmt = $pdo->query("SELECT COUNT(id) AS total FROM copies WHERE statut = 'non_corrigee'");
$copies = $stmt->fetch();

 $_SESSION['total']=$copies['total'];



?>
<?php

global $pdo;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/db.php';
$stmt = $pdo->prepare("
    SELECT COUNT(*) AS totalC
    FROM copies
    WHERE statut = 'corrigee'
    AND corrected_by = ?
");
$stmt->execute([$_SESSION['user']['id']]);
$copiesC = $stmt->fetch();

$_SESSION['totalC'] = $copiesC['totalC'];



?>



<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Espace Correcteur - Correction Anonyme</title>
    <link rel="stylesheet" href="../assets/css/correct.css">
</head>
<body>



    <header class="header">
        <nav class="logo">
            <h2>Mon espace</h2>
        </nav>
        <nav class="header__nav">
            <ul class="menu">

                <h3><a href="../views/public/logout.php">Deconnection</a></h3>

            </ul>
        </nav>
    </header>
 <main>
    <div class="main-content">
        <!-- Tableau de bord statistiques -->
         <div class="monHead">
        <div class="stats-dashboard">
            <div class="stat-card stat-pending">
                <h3 id="statPending"><?=$_SESSION['total']?>
                </h3>
                <p>À corriger</p>
            </div>

            <div class="stat-card stat-completed">
                <h3 id="statCompleted"><?=$_SESSION['totalC']?></h3>
                <p>Corrigées</p>
            </div>
        </div>
        <div class="profile-card">
                <div class="profile-image">
                    <svg
                    fill="#000000"
                    xml:space="preserve"
                    viewBox="0 0 64 64"
                    height="70px"
                    width="70px"
                    xmlns:xlink="http://www.w3.org/1999/xlink"
                    xmlns="http://www.w3.org/2000/svg"
                    id="Layer_1"
                    version="1.0"
                    >
                    <g stroke-width="0" id="SVGRepo_bgCarrier"></g>
                    <g
                        stroke-linejoin="round"
                        stroke-linecap="round"
                        id="SVGRepo_tracerCarrier"
                    ></g>
                    <g id="SVGRepo_iconCarrier">
                        <g>
                        <path
                            d="M18,12c0-5.522,4.478-10,10-10h8c5.522,0,10,4.478,10,10v7c0-3.313-2.687-6-6-6h-6c-2.209,0-4-1.791-4-4 c0-0.553-0.447-1-1-1s-1,0.447-1,1c0,2.209-1.791,4-4,4c-3.313,0-6,2.687-6,6V12z"
                            fill="#506C7F"
                        ></path>
                        <path
                            d="M62,60c0,1.104-0.896,2-2,2H4c-1.104,0-2-0.896-2-2v-8c0-1.104,0.447-2.104,1.172-2.828l-0.004-0.004 c4.148-3.343,8.896-5.964,14.046-7.714C20.869,45.467,26.117,48,31.973,48c5.862,0,11.115-2.538,14.771-6.56 c5.167,1.75,9.929,4.376,14.089,7.728l-0.004,0.004C61.553,49.896,62,50.896,62,52V60z"
                            fill="rgba(44, 62, 80, 1)"
                        ></path>
                        <g>
                            <path
                            d="M32,42c-2.853,0-5.502-0.857-7.715-2.322c-1.675,0.283-3.325,0.638-4.934,1.097 C22.602,43.989,27.041,46,31.973,46c4.938,0,9.383-2.017,12.634-5.238c-1.595-0.454-3.231-0.803-4.892-1.084 C37.502,41.143,34.853,42,32,42z"
                            fill="#F9EBB2"
                            ></path>
                            <path
                            d="M46,22h-1c-0.553,0-1-0.447-1-1v-1v-1c0-2.209-1.791-4-4-4h-6c-2.088,0-3.926-1.068-5-2.687 C27.926,13.932,26.088,15,24,15c-2.209,0-4,1.791-4,4v1v1c0,0.553-0.447,1-1,1h-1c-0.553,0-1,0.447-1,1v2c0,0.553,0.447,1,1,1h1 c0.553,0,1,0.447,1,1v1c0,6.627,5.373,12,12,12s12-5.373,12-12v-1c0-0.553,0.447-1,1-1h1c0.553,0,1-0.447,1-1v-2 C47,22.447,46.553,22,46,22z"
                            fill="#F9EBB2"
                            ></path>
                        </g>
                        <path
                            d="M62.242,47.758l0.014-0.014c-5.847-4.753-12.84-8.137-20.491-9.722C44.374,35.479,46,31.932,46,28 c1.657,0,3-1.343,3-3v-2c0-0.886-0.391-1.673-1-2.222V12c0-6.627-5.373-12-12-12h-8c-6.627,0-12,5.373-12,12v8.778 c-0.609,0.549-1,1.336-1,2.222v2c0,1.657,1.343,3,3,3c0,3.932,1.626,7.479,4.236,10.022c-7.652,1.586-14.646,4.969-20.492,9.722 l0.014,0.014C0.672,48.844,0,50.344,0,52v8c0,2.211,1.789,4,4,4h56c2.211,0,4-1.789,4-4v-8C64,50.344,63.328,48.844,62.242,47.758z M18,12c0-5.522,4.478-10,10-10h8c5.522,0,10,4.478,10,10v7c0-3.313-2.687-6-6-6h-6c-2.209,0-4-1.791-4-4c0-0.553-0.447-1-1-1 s-1,0.447-1,1c0,2.209-1.791,4-4,4c-3.313,0-6,2.687-6,6V12z M20,28v-1c0-0.553-0.447-1-1-1h-1c-0.553,0-1-0.447-1-1v-2 c0-0.553,0.447-1,1-1h1c0.553,0,1-0.447,1-1v-2c0-2.209,1.791-4,4-4c2.088,0,3.926-1.068,5-2.687C30.074,13.932,31.912,15,34,15h6 c2.209,0,4,1.791,4,4v2c0,0.553,0.447,1,1,1h1c0.553,0,1,0.447,1,1v2c0,0.553-0.447,1-1,1h-1c-0.553,0-1,0.447-1,1v1 c0,6.627-5.373,12-12,12S20,34.627,20,28z M24.285,39.678C26.498,41.143,29.147,42,32,42s5.502-0.857,7.715-2.322 c1.66,0.281,3.297,0.63,4.892,1.084C41.355,43.983,36.911,46,31.973,46c-4.932,0-9.371-2.011-12.621-5.226 C20.96,40.315,22.61,39.961,24.285,39.678z M62,60c0,1.104-0.896,2-2,2H4c-1.104,0-2-0.896-2-2v-8c0-1.104,0.447-2.104,1.172-2.828 l-0.004-0.004c4.148-3.343,8.896-5.964,14.046-7.714C20.869,45.467,26.117,48,31.973,48c5.862,0,11.115-2.538,14.771-6.56 c5.167,1.75,9.929,4.376,14.089,7.728l-0.004,0.004C61.553,49.896,62,50.896,62,52V60z"
                            fill="#242424"
                        ></path>
                        <path
                            d="M24.537,21.862c0.475,0.255,1.073,0.068,1.345-0.396C25.91,21.419,26.18,21,26.998,21 c0.808,0,1.096,0.436,1.111,0.458C28.287,21.803,28.637,22,28.999,22c0.154,0,0.311-0.035,0.457-0.111 c0.491-0.253,0.684-0.856,0.431-1.347C29.592,19.969,28.651,19,26.998,19c-1.691,0-2.618,0.983-2.9,1.564 C23.864,21.047,24.063,21.609,24.537,21.862z"
                            fill="#242424"
                        ></path>
                        <path
                            d="M34.539,21.862c0.475,0.255,1.073,0.068,1.345-0.396C35.912,21.419,36.182,21,37,21 c0.808,0,1.096,0.436,1.111,0.458C38.289,21.803,38.639,22,39.001,22c0.154,0,0.311-0.035,0.457-0.111 c0.491-0.253,0.684-0.856,0.431-1.347C39.594,19.969,38.653,19,37,19c-1.691,0-2.618,0.983-2.9,1.564 C33.866,21.047,34.065,21.609,34.539,21.862z"
                            fill="#242424"
                        ></path>
                        </g>
                    </g>
                    </svg>
                </div>
                <div class="profile-info">
                    <p class="profile-name"><?=$_SESSION['user']['nom'] ?>   <?=$_SESSION['user']['prenom'] ?></p>
                    <div class="profile-title"><?=$_SESSION['user']['email'] ?></div>

                </div>
                
                
                <div class="stats">
                    <div class="stat-item">
                    <div class="stat-value"><?=$_SESSION['user']['role'] ?></div>

                    </div>

                    <div class="stat-item">
                    <div class="stat-value">Date d'inscription</div>
                    <div class="stat-label"><?=$_SESSION['user']['date'] ?></div>
                    </div>
                </div>
                </div>
        </div>

        <!-- Section copies à corriger -->
        <div class="section">
            <h2 class="section-title">Copies à corriger</h2>

            <div class="alert alert-info">
                <strong>Information :</strong> Cliquez sur "Prendre en charge" pour verrouiller une copie et commencer
                la correction. Vous avez 2 heures pour terminer une correction avant que le verrou expire.
            </div>

            <div class="table-container">
                <table>
                    <thead>
                    <tr>
                        <th>Identifiant Anonyme</th>
                        <th>Date de Dépôt</th>
                        <th>Statut</th>

                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody id="copiesToCorrect">

                    <?php
                    global $pdo;
                    if (session_status() === PHP_SESSION_NONE) {
                        session_start();
                    }

                    require_once __DIR__ . '/../config/db.php';

                    $stmt = $pdo->query(" SELECT id, identifiant_anonyme, date_depot, statut,format_fichier,taille_fichier FROM copies WHERE statut = 'non_corrigee'
 ORDER BY date_depot DESC");
                    $copies = $stmt->fetchAll();

                    //$copies = $stmt->fetchAll();

                    foreach ($copies as $copie):
                        ?>



                        <tr>
                            <?php $_SESSION['copieId']= $copie['id'] ?>

                            <td><?= htmlspecialchars($copie['identifiant_anonyme']) ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($copie['date_depot'])) ?></td>
                            <td><?= ucfirst(str_replace('_', ' ', $copie['statut'])) ?></td>
                            <td>
                                <button class="btn btn-primary"
                                        onclick="openCorrection(this)"
                                        data-copy-id="<?= $copie['identifiant_anonyme'] ?>"
                                        data-date="<?= $copie['date_depot'] ?>"
                                        data-taille="<?= $copie['taille_fichier'] ?>"
                                        data-format="<?= $copie['format_fichier'] ?>">
                                    📝 Corriger
                                </button>


                                <button class="btn btn-secondary"
                                        onclick="releaseCopy('<?= htmlspecialchars($copie['identifiant_anonyme']) ?>')">
                                    🔓 Libérer
                                </button>
                            </td>

                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Section historique -->
        <div class="section">
            <h2 class="section-title">Mes corrections terminées</h2>

            <div class="table-container">
                <table>
                    <thead>
                    <tr>
                        <th>Identifiant Anonyme</th>
                        <th>Date de Correction</th>
                        <th>Note</th>

                    </tr>
                    </thead>
                    <tbody>


                    <?php
                    global $pdo;
                    if (session_status() === PHP_SESSION_NONE) {
                        session_start();
                    }

                    require_once __DIR__ . '/../config/db.php';

                    $stmt = $pdo->prepare("
                            SELECT 
                                c.identifiant_anonyme,
                                g.note,
                                g.date_correction
                            FROM copies c
                            INNER JOIN grille_evaluation g ON g.id_copie = c.id
                            WHERE c.statut = 'corrigee' AND c.corrected_by = ?
                            ORDER BY c.date_depot DESC
                        ");


                    $stmt->execute([$_SESSION['user']['id']]);
                    $copies = $stmt->fetchAll();



                    foreach ($copies as $copie):
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($copie['identifiant_anonyme']) ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($copie['date_correction'])) ?></td>
                            <td><?= ucfirst(str_replace('_', ' ', $copie['note'])) ?></td>

                        </tr>
                    <?php endforeach; ?>

                    </tbody>
                </table>
            </div>
        </div>
    </div>


<!-- Modal de correction -->
<div id="correctionModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>🔍 Correction de copie</h2>
            <button class="close" onclick="closeCorrectionModal()">&times;</button>
        </div>
        <div class="modal-body">
            <div class="document-viewer">
                <div class="copy-info">
                    <h3>Informations de la copie</h3>
                    <div class="copy-info-grid">
                        <div class="copy-info-item">
                            <label>Identifiant Anonyme</label>
                            <span id="currentCopyId"><?= htmlspecialchars($copie['identifiant_anonyme'] ?? '') ?></span>
                        </div>
                        <div class="copy-info-item">
                            <label>Date de dépôt</label>
                            <span id="currentCopyDate"></span>
                        </div>
                        <div class="copy-info-item">
                            <label>Taille du fichier</label>
                            <span id="currentCopySize"></span>
                        </div>
                        <div class="copy-info-item">
                            <label>Format</label>
                            <span id="currentCopyFormat"></span>
                        </div>
                    </div>
                </div>

                <div class="document-placeholder">
                    📄<br>
                    Aperçu du document<br>
                    <small>Cliquez pour télécharger : <a href="#" onclick="downloadCopy()">📥 Télécharger la copie</a></small>
                </div>
            </div>

            <div class="evaluation-panel">
                <form class="evaluation-form" id="evaluationForm" action="" method="POST">
                    <h3 style="margin-bottom: 20px; color: #374151;">Grille d'évaluation</h3>

                    <div class="form-group">
                        <label for="noteInput">Note sur 20</label>
                        <div class="note-input">
                            <input type="number" id="noteInput" min="0" max="20" step="0.5" name="note" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="commentaireInput">Commentaire détaillé</label>
                        <textarea id="commentaireInput" name="commentaire" placeholder="Saisissez vos commentaires sur la copie..." required></textarea>
                    </div>

                    <div class="form-group">
                        <label for="criteresInput">Critères d'évaluation</label>
                        <textarea id="criteresInput" name="critere" placeholder="Points forts, points à améliorer, conseils..."></textarea>
                    </div>

                    <div class="form-actions">

                        <button type="submit" class="btn btn-success" name="submit">
                            ✅ Valider la correction
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</main>

<script>
    // Variables globales
    let currentCopyId = null;
    let corrections = {};

    // Initialisation
    document.addEventListener('DOMContentLoaded', function() {
        loadStatistics();
        setupEvaluationForm();
    });



    // Prendre en charge une copie
    function takeCopy(copyId) {
        if (confirm(`Voulez-vous prendre en charge la copie ${copyId} ?`)) {
            // Simulation de la prise en charge
            showAlert(`Copie ${copyId} prise en charge avec succès !`, 'success');
            updateCopyStatus(copyId, 'locked');


        }
    }

    // Libérer une copie
    function releaseCopy(copyId) {
        if (confirm(`Voulez-vous libérer la copie ${copyId} ? Elle redeviendra disponible pour d'autres correcteurs.`)) {
            showAlert(`Copie ${copyId} libérée.`, 'info');
            updateCopyStatus(copyId, 'non_corrigee');
        }
    }

    // Ouvrir la modal de correction
    function openCorrection(button) {
        const copyId = button.dataset.copyId;
        const date = button.dataset.date;
        const format = button.dataset.format;
        const taille = button.dataset.taille;

        currentCopyId = copyId;

        document.getElementById('currentCopyId').textContent = copyId;
        document.getElementById('currentCopyDate').textContent = formatDate(date);
        document.getElementById('currentCopySize').textContent = formatOctets(taille);
        document.getElementById('currentCopyFormat').textContent = format;

        document.getElementById('correctionModal').style.display = 'block';

        // Pré-remplir le formulaire si on a déjà une ébauche
        if (corrections[copyId]) {
            document.getElementById('noteInput').value = corrections[copyId].note;
            document.getElementById('commentaireInput').value = corrections[copyId].commentaire;
            document.getElementById('criteresInput').value = corrections[copyId].criteres;
        }
    }
    function formatDate(dateStr) {
        return new Date(dateStr).toLocaleString('fr-FR');
    }

    function formatOctets(o) {
        const taille = parseInt(o);
        if (taille >= 1048576) return (taille / 1048576).toFixed(2) + " Mo";
        if (taille >= 1024) return (taille / 1024).toFixed(2) + " Ko";
        return taille + " o";
    }


    // Fermer la modal de correction
    function closeCorrectionModal() {
        document.getElementById('correctionModal').style.display = 'none';
        currentCopyId = null;
    }

    // Configuration du formulaire d'évaluation
    function setupEvaluationForm() {
        document.getElementById('evaluationForm').addEventListener('submit', function(e) {
            e.preventDefault();
            submitCorrection();
        });
    }



    // Soumettre la correction
    function submitCorrection() {
        if (!currentCopyId) return;

        const note = document.getElementById('noteInput').value;
        const commentaire = document.getElementById('commentaireInput').value;
        const criteres = document.getElementById('criteresInput').value;

        if (!note || !commentaire) {
            showAlert('Veuillez remplir tous les champs obligatoires.', 'warning');
            return;
        }

        if (confirm('Êtes-vous sûr de vouloir valider cette correction ? Cette action est définitive.')) {
            // Simulation de la soumission
            showAlert(`Correction de la copie ${currentCopyId} validée avec succès !`, 'success');
            updateCopyStatus(currentCopyId, 'corrigee');
            closeCorrectionModal();

            // Réinitialiser le formulaire
            document.getElementById('evaluationForm').reset();
            delete corrections[currentCopyId];

            // Mettre à jour les statistiques
            updateStats();


        }
    }

    // Télécharger une copie
    function downloadCopy() {
        const identifiant = document.getElementById('currentCopyId')?.textContent?.trim();

        if (identifiant) {
            showAlert(`Téléchargement de la copie ${identifiant}...`, 'info');
            // redirection vers le script de téléchargement
            window.location.href = `telecharger.php?identifiant_anonyme=${encodeURIComponent(identifiant)}`;
        } else {
            showAlert("Impossible de récupérer l'identifiant de la copie.", 'danger');
        }
    }



    // Voir une correction terminée
    function viewCorrection(copyId) {
        showAlert(`Affichage de la correction ${copyId}`, 'info');
        // Ouvrir en mode lecture seule
    }

    // Mettre à jour le statut d'une copie dans le tableau
    function updateCopyStatus(copyId, status) {
        const rows = document.querySelectorAll('#copiesToCorrect tr');
        rows.forEach(row => {
            const idCell = row.cells[0];
            if (idCell && idCell.textContent === copyId) {
                const statusCell = row.cells[2];
                const actionCell = row.cells[4];

                if (status === 'locked') {
                    statusCell.innerHTML = '<span class="status status-locked">Verrouillée</span>';
                    row.cells[3].textContent = 'Prof. Martin';
                    actionCell.innerHTML = `
                            <button class="btn btn-primary" onclick="openCorrection('${copyId}')">📝 Corriger</button>
                            <button class="btn btn-secondary" onclick="releaseCopy('${copyId}')">🔓 Libérer</button>
                        `;
                } else if (status === 'non_corrigee') {
                    statusCell.innerHTML = '<span class="status status-non-corrigee">Non corrigée</span>';
                    row.cells[3].textContent = '-';
                    actionCell.innerHTML =                    `<button class="btn btn-success" onclick="takeCopy('${copyId}')">🔒 Prendre en charge</button>`;
                } else if (status === 'corrigee') {
                    // Optionnel : retirer la ligne ou déplacer dans l'historique
                    row.remove();
                    // Mise à jour dans l’historique (dans un vrai projet, recharger depuis le backend)
                     simulateAddToHistory(copyId, note, new Date()); // à faire si tu veux simuler
                }
            }
        });
    }

    // Met à jour les statistiques (simulation)
    function updateStats() {
        let pending = parseInt(document.getElementById('statPending').textContent);
        let progress = parseInt(document.getElementById('statProgress').textContent);
        let completed = parseInt(document.getElementById('statCompleted').textContent);

        if (pending > 0) pending--;
        if (progress > 0) progress--;
        completed++;

        document.getElementById('statPending').textContent = pending;
        document.getElementById('statProgress').textContent = progress;
        document.getElementById('statCompleted').textContent = completed;
    }

    // Affiche une alerte temporaire
    function showAlert(message, type = 'info') {
        const alertBox = document.createElement('div');
        alertBox.className = `alert alert-${type}`;
        alertBox.textContent = message;
        document.querySelector('.main-content').prepend(alertBox);

        setTimeout(() => alertBox.remove(), 4000);
    }

    // Déconnexion (à personnaliser)
    function logout() {
        if (confirm("Êtes-vous sûr de vouloir vous déconnecter ?")) {

            window.location.href = "../views/public/logout.php";
        }
    }
</script>
</body>
</html>
