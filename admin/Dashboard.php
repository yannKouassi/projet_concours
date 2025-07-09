<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ../views/public/accueil.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration - Système de Correction</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
<div class="app-container">
    <!-- Sidebar Navigation -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <div class="logo">

                <span class="logo-text">Administrateur</span>
            </div>
        </div>

        <nav class="sidebar-nav">
            <ul class="nav-menu">
                <li class="nav-item active">
                    <a href="#dashboard" class="nav-link">
                        <span class="nav-icon">📊</span>
                        <span class="nav-text">Tableau de Bord</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link" onclick="goToStudentPage()">
                        <span class="nav-icon">👨‍🎓</span>
                        <span class="nav-text">Étudiants</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link" onclick="goToCorrectorPage()">
                        <span class="nav-icon">👨‍🏫</span>
                        <span class="nav-text">Correcteurs</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link" onclick="refreshData()">
                        <span class="nav-icon">🔄</span>
                        <span class="nav-text">Actualiser</span>
                    </a>
                </li>
            </ul>
        </nav>

        <div class="sidebar-footer">
            <button class="logout-btn" onclick="logout()">
                <span class="nav-icon">🚪</span>
                <span class="nav-text">Déconnexion</span>
            </button>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Header -->
        <header class="main-header">
            <div class="header-left">
                <h1 class="page-title">Tableau de Bord Administrateur</h1>
                <p class="page-subtitle">Système de Correction en Ligne - Gestion Complète</p>
            </div>
            <div class="header-right">
                <div class="header-actions">
                    <button class="action-btn" onclick="generateReport()">
                        <span>📊</span> Rapport
                    </button>
                    <button class="action-btn primary" onclick="openPublishModal()">
                        <span>📤</span> Publier
                    </button>
                </div>
            </div>
        </header>

        <!-- Content Area -->
        <div class="content-area">
            <!-- Status Alerts -->
            <div id="connectionStatus" class="alert alert-info" style="display: none;">
                <strong>Connexion établie :</strong> Données synchronisées avec le système.
            </div>

            <div id="noDataAlert" class="alert alert-warning" style="display: none;">
                <strong>Aucune donnée trouvée :</strong> Veuillez d'abord accéder aux pages étudiants et correcteurs pour initialiser les données.
            </div>

            <!-- Stats Cards -->
            <div class="stats-section">
                <div class="stats-grid">
                    <div class="stat-card copies">
                        <div class="stat-icon">📄</div>
                        <div class="stat-content">
                            <div class="stat-number" id="totalCopies">0</div>
                            <div class="stat-label">Copies Totales</div>
                        </div>
                    </div>

                    <div class="stat-card correctors">
                        <div class="stat-icon">👨‍🏫</div>
                        <div class="stat-content">
                            <div class="stat-number" id="totalCorrectors">0</div>
                            <div class="stat-label">Correcteurs Actifs</div>
                        </div>
                    </div>

                    <div class="stat-card corrected">
                        <div class="stat-icon">✅</div>
                        <div class="stat-content">
                            <div class="stat-number" id="correctedCopies">0</div>
                            <div class="stat-label">Copies Corrigées</div>
                        </div>
                    </div>


                </div>
            </div>



            <!-- Tables Section -->
            <div class="tables-section">
                <!-- Copies Management -->
                <div class="section">
                    <div class="section-header">
                        <h2 class="section-title">📋 Gestion des Copies</h2>

                    </div>
                    <div class="section-content">
                        <div class="table-container">
                            <table class="data-table">
                                <thead>
                                <tr>
                                    <th>ID Copie</th>
                                    <th>ID Étudiant</th>
                                    <th>ID correcteur</th>
                                    <th>Statut</th>
                                    <th>Note</th>
                                    <th>Date de depot</th>
                                </tr>
                                </thead>
                                <tbody id="copiesTable">
                                <tr>
                                    <td colspan="6" class="no-data">
                                        Aucune copie trouvée. Chargement des données...
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Correctors Management -->
                <div class="section">
                    <div class="section-header">
                        <h2 class="section-title">👨‍🏫 Correcteurs</h2>
                        <div class="section-actions">
                            <button class="btn btn-success" onclick="openNotificationModal()">Ajouter</button>
                        </div>
                    </div>
                    <div class="section-content">
                        <div class="table-container">
                            <table class="data-table">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nom</th>
                                    <th>Prenom</th>
                                    <th>Email</th>
                                    <th>Total_copie</th>
                                    <th>Date inscription</th>
                                </tr>
                                </thead>
                                <tbody id="correctorsTable">
                                <tr>
                                    <td colspan="6" class="no-data">
                                        Aucun correcteur trouvé. Chargement des données...
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Results Section -->
            <div class="section results-section">
                <div class="section-header">
                    <h2 class="section-title">📢 Publication des Résultats</h2>
                </div>
                <div class="section-content">
                    <div class="results-actions">
                        <button class="btn btn-success" onclick="openPublishModal()">
                            <span>📤</span> Publier Résultats
                        </button>
                        <button class="btn btn-primary" onclick="openNotificationModal()">
                            <span>📧</span> Notification Individuelle
                        </button>
                        <button class="btn btn-warning" onclick="generateReport()">
                            <span>📊</span> Générer Rapport
                        </button>
                    </div>

                    <div class="results-content">
                        <h3>Résultats Récemment Publiés</h3>
                        <div id="recentResults" class="recent-results">
                            <p class="no-data">Aucun résultat publié récemment.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Modals -->
<!-- Modal Publication -->
<div id="publishModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>📤 Publication des Résultats</h2>
            <span class="close" onclick="closeModal('publishModal')">&times;</span>
        </div>
        <form id="publishForm">
            <div class="form-group">
                <label for="examSelect">Sélectionner l'Examen:</label>
                <select id="examSelect" required>
                    <option value="">Choisir un examen...</option>
                </select>
            </div>
            <div class="form-group">
                <label for="publicationMode">Mode de Publication:</label>
                <select id="publicationMode" required>
                    <option value="individual">Publication Individuelle</option>
                    <option value="batch">Publication par Lot</option>
                    <option value="all">Publier Tous les Résultats</option>
                </select>
            </div>
            <div class="form-group">
                <label for="notificationMessage">Message aux Étudiants:</label>
                <textarea id="notificationMessage" rows="4" placeholder="Votre message personnalisé..."></textarea>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn btn-secondary" onclick="closeModal('publishModal')">Annuler</button>
                <button type="submit" class="btn btn-success">Publier</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Notification -->
<div id="notificationModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>📧 Notification Individuelle</h2>
            <span class="close" onclick="closeModal('notificationModal')">&times;</span>
        </div>
        <form id="notificationForm">
            <div class="form-group">
                <label for="studentSelect">Sélectionner l'Étudiant:</label>
                <select id="studentSelect" required>
                    <option value="">Choisir un étudiant...</option>
                </select>
            </div>
            <div class="form-group">
                <label for="studentMessage">Message:</label>
                <textarea id="studentMessage" rows="4" placeholder="Message confidentiel pour l'étudiant..." required></textarea>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn btn-secondary" onclick="closeModal('notificationModal')">Annuler</button>
                <button type="submit" class="btn btn-success">Envoyer</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Variables globales pour stocker les données
    let studentsData = [];
    let correctorsData = [];
    let copiesData = [];
    let submissionsData = [];
    let correctionsData = [];

    // Configuration des événements de synchronisation
    const SYNC_EVENTS = {
        STUDENT_ADDED: 'student_added',
        SUBMISSION_ADDED: 'submission_added',
        CORRECTION_COMPLETED: 'correction_completed',
        CORRECTOR_ADDED: 'corrector_added'
    };

    // Fonction pour écouter les changements dans localStorage
    function setupStorageListener() {
        window.addEventListener('storage', function(e) {
            if (e.key === 'submissions' || e.key === 'corrections' || e.key === 'students' || e.key === 'correctors') {
                console.log('Changement détecté dans localStorage:', e.key);
                loadData();
            }
        });

        // Écouter les événements personnalisés pour les changements dans la même page
        window.addEventListener('dataChanged', function(e) {
            console.log('Événement de changement de données:', e.detail);
            loadData();
        });
    }

    // Fonction pour émettre un événement de changement de données
    function emitDataChange(type, data) {
        const event = new CustomEvent('dataChanged', {
            detail: { type, data, timestamp: new Date().toISOString() }
        });
        window.dispatchEvent(event);
    }

    // Fonction pour charger les données depuis localStorage avec synchronisation
    function loadData() {
        try {
            // Charger les étudiants
            const students = localStorage.getItem('students');
            if (students) {
                studentsData = JSON.parse(students);
            }

            // Charger les correcteurs
            const correctors = localStorage.getItem('correctors');
            if (correctors) {
                correctorsData = JSON.parse(correctors);
            }

            // Charger les soumissions (copies déposées)
            const submissions = localStorage.getItem('submissions');
            if (submissions) {
                submissionsData = JSON.parse(submissions);
            }

            // Charger les corrections
            const corrections = localStorage.getItem('corrections');
            if (corrections) {
                correctionsData = JSON.parse(corrections);
            }

            // Traiter les données des copies
            processCopiesData();

            // Mettre à jour l'interface
            updateConnectionStatus();
            updateDashboard();

            console.log('Données chargées:', {
                students: studentsData.length,
                correctors: correctorsData.length,
                submissions: submissionsData.length,
                corrections: correctionsData.length
            });

        } catch (error) {
            console.error('Erreur lors du chargement des données:', error);
            showAlert('error', 'Erreur lors du chargement des données');
        }
    }

    // Fonction pour traiter les données des copies avec synchronisation temps réel
    function processCopiesData() {
        copiesData = [];

        submissionsData.forEach((submission, index) => {
            const student = studentsData.find(s => s.id === submission.studentId);
            const correction = correctionsData.find(c => c.submissionId === submission.id);

            copiesData.push({
                id: `CPY-${String(index + 1).padStart(3, '0')}`,
                submissionId: submission.id,
                studentId: submission.studentId,
                studentName: student ? student.name : 'Étudiant inconnu',
                subject: submission.subject,
                status: correction ? 'corrected' : 'pending',
                score: correction ? correction.score : null,
                correctorId: correction ? correction.correctorId : null,
                correctorName: correction ? getCorrectorName(correction.correctorId) : null,
                submittedAt: submission.submittedAt,
                correctedAt: correction ? correction.correctedAt : null,
                filePath: submission.filePath
            });
        });
    }

    // Fonction utilitaire pour obtenir le nom du correcteur
    function getCorrectorName(correctorId) {
        const corrector = correctorsData.find(c => c.id === correctorId);
        return corrector ? corrector.name : 'Correcteur inconnu';
    }

    // Fonction pour mettre à jour le statut de connexion
    function updateConnectionStatus() {
        const hasData = studentsData.length > 0 || correctorsData.length > 0 || submissionsData.length > 0;

        if (hasData) {
            showAlert('success', 'Connexion établie : Données synchronisées avec le système.');
            hideAlert('noDataAlert');
        } else {
            hideAlert('connectionStatus');
            showAlert('warning', 'Aucune donnée trouvée : Veuillez d\'abord accéder aux pages étudiants et correcteurs pour initialiser les données.');
        }
    }

    // Fonction pour afficher les alertes
    function showAlert(type, message) {
        const alertTypes = {
            'success': 'connectionStatus',
            'warning': 'noDataAlert',
            'error': 'errorAlert'
        };

        const alertId = alertTypes[type];
        if (alertId) {
            const alertElement = document.getElementById(alertId);
            if (alertElement) {
                alertElement.style.display = 'block';
                alertElement.innerHTML = `<strong>${type === 'success' ? 'Connexion établie :' : type === 'warning' ? 'Attention :' : 'Erreur :'}</strong> ${message}`;
            }
        }
    }

    // Fonction pour masquer les alertes
    function hideAlert(alertId) {
        const alertElement = document.getElementById(alertId);
        if (alertElement) {
            alertElement.style.display = 'none';
        }
    }

    // Fonction pour mettre à jour le tableau de bord
    function updateDashboard() {
        updateStats();
        updateCopiesTable();
        updateCorrectorsTable();
        updateModalsOptions();
    }

    // Fonction pour calculer et mettre à jour les statistiques en temps réel
    function updateStats() {
        // Calcul des statistiques en temps réel
        const totalCopies = submissionsData.length; // Basé sur les soumissions réelles
        const correctedCopies = correctionsData.length; // Basé sur les corrections réelles
        const pendingCopies = totalCopies - correctedCopies;
        const totalCorrectors = correctorsData.length;

        // Animation des nombres avec vérification
        animateNumber('totalCopies', totalCopies);
        animateNumber('correctedCopies', correctedCopies);
        animateNumber('pendingCopies', Math.max(0, pendingCopies));
        animateNumber('totalCorrectors', totalCorrectors);

        // Calcul et mise à jour de la progression
        const progressPercentage = totalCopies > 0 ? (correctedCopies / totalCopies) * 100 : 0;
        updateProgressBar(progressPercentage, correctedCopies, totalCopies);

        console.log('Statistiques mises à jour:', {
            totalCopies,
            correctedCopies,
            pendingCopies,
            totalCorrectors,
            progressPercentage: progressPercentage.toFixed(1)
        });
    }

    // Fonction pour mettre à jour la barre de progression
    function updateProgressBar(percentage, corrected, total) {
        const progressFill = document.getElementById('progressFill');
        const progressText = document.getElementById('progressText');

        if (progressFill && progressText) {
            progressFill.style.width = percentage + '%';
            progressText.textContent = `${corrected} sur ${total} copies corrigées (${percentage.toFixed(1)}%)`;
        }
    }


    // Fonction pour mettre à jour le tableau des copies
    function updateCopiesTable() {
        const tbody = document.getElementById('copiesTable');
        if (!tbody) return;

        if (copiesData.length === 0) {
            tbody.innerHTML = `
            <tr>
                <td colspan="6" class="no-data">
                    ${submissionsData.length === 0 ?
                'Aucune copie trouvée. Les étudiants n\'ont pas encore soumis de devoirs.' :
                'Chargement des données des copies...'}
                </td>
            </tr>
        `;
            return;
        }

        tbody.innerHTML = copiesData.map(copy => `
        <tr>
            <td>${copy.id}</td>
            <td>${copy.studentName}</td>
            <td>${copy.subject}</td>
            <td>
                <span class="status ${copy.status}">
                    ${copy.status === 'corrected' ? '✅ Corrigée' : '⏳ En attente'}
                </span>
            </td>
            <td>${copy.score !== null ? `${copy.score}/20` : '-'}</td>
            <td>
                <button class="btn btn-sm btn-primary" onclick="viewCopy('${copy.id}')">
                    👁️ Voir
                </button>
                ${copy.status === 'pending' ?
            `<button class="btn btn-sm btn-warning" onclick="assignCopy('${copy.id}')">
                        📝 Assigner
                    </button>` :
            `<span class="corrector-info">Par: ${copy.correctorName}</span>`}
            </td>
        </tr>
    `).join('');
    }

    // Fonction pour mettre à jour le tableau des correcteurs avec statistiques
    function updateCorrectorsTable() {
        const tbody = document.getElementById('correctorsTable');
        if (!tbody) return;

        if (correctorsData.length === 0) {
            tbody.innerHTML = `
            <tr>
                <td colspan="6" class="no-data">
                    Aucun correcteur trouvé. Veuillez ajouter des correcteurs.
                </td>
            </tr>
        `;
            return;
        }

        tbody.innerHTML = correctorsData.map(corrector => {
            // Calculer les statistiques pour chaque correcteur
            const assignedCopies = copiesData.filter(copy => copy.correctorId === corrector.id).length;
            const correctedCopies = correctionsData.filter(correction => correction.correctorId === corrector.id).length;

            return `
            <tr>
                <td>
                    <div class="corrector-info">
                        <strong>${corrector.name}</strong>
                        <small class="text-muted d-block">ID: ${corrector.id}</small>
                    </div>
                </td>
                <td>${corrector.email}</td>
                <td>
                    <div class="subjects-list">
                        ${corrector.subjects ? corrector.subjects.join(', ') : 'Non spécifiées'}
                    </div>
                </td>
                <td>
                    <span class="badge badge-primary">${assignedCopies}</span>
                </td>
                <td>
                    <span class="badge badge-success">${correctedCopies}</span>
                </td>
                <td>
                    <button class="btn btn-sm btn-primary" onclick="viewCorrectorDetails('${corrector.id}')">
                        📊 Détails
                    </button>
                    <button class="btn btn-sm btn-info" onclick="showCorrectorStats('${corrector.id}')">
                        📈 Stats
                    </button>
                </td>
            </tr>
        `;
        }).join('');
    }

    // Fonction pour rediriger vers inscription à jour les options des modales

        function redirectToInscription() {
            if (confirm("Vous allez être redirigé vers la page d'inscription. Voulez-vous continuer ?")) {
                window.location.href = '../views/admin/inscription.php';
            }
        }






    // Fonctions de navigation avec pages dédiées
    function goToStudentPage() {
        // Créer une page dédiée aux étudiants
        showStudentsEffective();
    }

    function goToCorrectorPage() {
        // Créer une page dédiée aux correcteurs
        showCorrectorsEffective();
    }

    // Fonction pour afficher l'effectif des étudiants
    function showStudentsEffective() {
        const modal = createEffectiveModal('Effectif des Étudiants', 'students');
        document.body.appendChild(modal);
        modal.style.display = 'block';
    }

    // Fonction pour afficher l'effectif des correcteurs
    function showCorrectorsEffective() {
        const modal = createEffectiveModal('Effectif des Correcteurs', 'correctors');
        document.body.appendChild(modal);
        modal.style.display = 'block';
    }

    // Fonction pour créer une modale d'effectif
    function createEffectiveModal(title, type) {
        const modal = document.createElement('div');
        modal.className = 'modal';
        modal.id = `${type}EffectiveModal`;

        const data = type === 'students' ? studentsData : correctorsData;
        const icon = type === 'students' ? '👨‍🎓' : '👨‍🏫';

        modal.innerHTML = `
        <div class="modal-content" style="max-width: 800px;">
            <div class="modal-header">
                <h2>${icon} ${title}</h2>
                <span class="close" onclick="closeEffectiveModal('${type}')">&times;</span>
            </div>
            <div class="modal-body" style="padding: 30px;">
                <div class="effective-stats">
                    <div class="stat-card">
                        <div class="stat-icon">${icon}</div>
                        <div class="stat-content">
                            <div class="stat-number">${data.length}</div>
                            <div class="stat-label">Total ${type === 'students' ? 'Étudiants' : 'Correcteurs'}</div>
                        </div>
                    </div>
                </div>

                <div class="effective-table">
                    <h3>Liste Complète</h3>
                    <div class="table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    ${type === 'students' ? `
                                        <th>ID</th>
                                        <th>Nom</th>
                                        <th>Prenom</th>
                                        <th>Email</th>
                                        <th>Total_copie</th>
                                        <th>Date_inscription</th>
                                    ` : `
                                        <th>ID</th>
                                        <th>Nom</th>
                                        <th>Prenom</th>
                                        <th>Email</th>
                                        <th>Total_copie_corrigées</th>
                                        <th>Date_inscription</th>
                                    `}
                                </tr>
                            </thead>
                            <tbody>
                                ${generateEffectiveTableRows(data, type)}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    `;

        return modal;
    }

    // Fonction pour générer les lignes du tableau d'effectif
    function generateEffectiveTableRows(data, type) {
        if (data.length === 0) {
            return `
            <tr>
                <td colspan="6" class="no-data">
                    Aucun ${type === 'students' ? 'étudiant' : 'correcteur'} trouvé.
                </td>
            </tr>
        `;
        }

        return data.map(item => {
            if (type === 'students') {
                const submissions = submissionsData.filter(s => s.studentId === item.id).length;
                const corrected = correctionsData.filter(c => {
                    const submission = submissionsData.find(s => s.id === c.submissionId && s.studentId === item.id);
                    return !!submission;
                }).length;

                return `
                <tr>
                    <td>${item.id}</td>
                    <td><strong>${item.name}</strong></td>
                    <td>${item.email}</td>
                    <td>${item.class || 'Non spécifiée'}</td>
                    <td>
                        <span class="badge badge-info">${submissions}</span>
                        ${corrected > 0 ? `<span class="badge badge-success">${corrected} corrigées</span>` : ''}
                    </td>
                    <td>
                        <span class="status ${submissions > 0 ? 'active' : 'inactive'}">
                            ${submissions > 0 ? '✅ Actif' : '⏳ En attente'}
                        </span>
                    </td>
                </tr>
            `;
            } else {
                const corrected = correctionsData.filter(c => c.correctorId === item.id).length;
                const assigned = copiesData.filter(c => c.correctorId === item.id).length;

                return `
                <tr>
                    <td>${item.id}</td>
                    <td><strong>${item.name}</strong></td>
                    <td>${item.email}</td>
                    <td>${item.subjects ? item.subjects.join(', ') : 'Non spécifiées'}</td>
                    <td>
                        <span class="badge badge-success">${corrected}</span>
                        ${assigned > corrected ? `<span class="badge badge-warning">${assigned - corrected} en cours</span>` : ''}
                    </td>
                    <td>
                        <span class="status ${corrected > 0 ? 'active' : 'inactive'}">
                            ${corrected > 0 ? '✅ Actif' : assigned > 0 ? '📝 Assigné' : '⏳ En attente'}
                        </span>
                    </td>
                </tr>
            `;
            }
        }).join('');
    }

    // Fonction pour fermer la modale d'effectif
    function closeEffectiveModal(type) {
        const modal = document.getElementById(`${type}EffectiveModal`);
        if (modal) {
            modal.style.display = 'none';
            document.body.removeChild(modal);
        }
    }

    // Fonction pour actualiser les données avec synchronisation forcée
    function refreshData() {
        console.log('Actualisation des données...');
        showAlert('info', 'Actualisation des données en cours...');

        // Forcer le rechargement depuis localStorage
        loadData();

        // Simulation d'un délai pour l'effet visuel
        setTimeout(() => {
            showAlert('success', 'Données actualisées avec succès.');
            setTimeout(() => hideAlert('connectionStatus'), 3000);
        }, 500);
    }

    // Fonctions utilitaires améliorées
    function logout() {
        if (confirm('Êtes-vous sûr de vouloir vous déconnecter ? Toutes les données non sauvegardées seront perdues.')) {
            // Émettre un événement de déconnexion
            emitDataChange('LOGOUT', { timestamp: new Date().toISOString() });

            // Nettoyer le localStorage si nécessaire
            // localStorage.clear(); // Décommentez si vous voulez tout effacer

            // Redirection
            window.location.href = '../views/public/logout.php';
        }
    }

    function openPublishModal() {
        updateModalsOptions(); // S'assurer que les options sont à jour
        document.getElementById('publishModal').style.display = 'block';
    }

    function openNotificationModal() {
        if (confirm("Vous allez être redirigé vers la page d'inscription. Voulez-vous continuer ?")) {
            window.location.href = 'CorrecteurAdd.php';
        }
    }

    function openBulkActionModal() {
        // À implémenter : modal pour les actions en lot
        alert('Fonctionnalité des actions en lot à implémenter');
    }

    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = 'none';
        }
    }

    function generateReport() {
        const reportData = {
            totalStudents: studentsData.length,
            totalCorrectors: correctorsData.length,
            totalSubmissions: submissionsData.length,
            totalCorrections: correctionsData.length,
            progressPercentage: submissionsData.length > 0 ? (correctionsData.length / submissionsData.length) * 100 : 0,
            generatedAt: new Date().toLocaleString('fr-FR')
        };

        console.log('Rapport généré:', reportData);
        alert(`Rapport généré:\n\nÉtudiants: ${reportData.totalStudents}\nCorrecteurs: ${reportData.totalCorrectors}\nCopies soumises: ${reportData.totalSubmissions}\nCopies corrigées: ${reportData.totalCorrections}\nProgression: ${reportData.progressPercentage.toFixed(1)}%\n\nGénéré le: ${reportData.generatedAt}`);
    }

    function viewCopy(copyId) {
        const copy = copiesData.find(c => c.id === copyId);
        if (copy) {
            alert(`Détails de la copie ${copyId}:\n\nÉtudiant: ${copy.studentName}\nMatière: ${copy.subject}\nStatut: ${copy.status}\nNote: ${copy.score || 'Non notée'}\nCorrecteur: ${copy.correctorName || 'Non assignée'}`);
        }
    }

    function assignCopy(copyId) {
        // À implémenter : système d'assignation des copies
        alert(`Assignation de la copie ${copyId} - Fonctionnalité à implémenter`);
    }

    function viewCorrectorDetails(correctorId) {
        const corrector = correctorsData.find(c => c.id === correctorId);
        if (corrector) {
            const corrected = correctionsData.filter(c => c.correctorId === correctorId).length;
            const assigned = copiesData.filter(c => c.correctorId === correctorId).length;

            alert(`Détails du correcteur:\n\nNom: ${corrector.name}\nEmail: ${corrector.email}\nMatières: ${corrector.subjects ? corrector.subjects.join(', ') : 'Non spécifiées'}\nCopies assignées: ${assigned}\nCopies corrigées: ${corrected}`);
        }
    }

    function showCorrectorStats(correctorId) {
        // À implémenter : statistiques détaillées du correcteur
        alert(`Statistiques détaillées du correcteur ${correctorId} - Fonctionnalité à implémenter`);
    }

    // Fermer les modales en cliquant à l'extérieur
    window.onclick = function(event) {
        const modals = document.querySelectorAll('.modal');
        modals.forEach(modal => {
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        });
    }

    // Gestion des formulaires
    document.addEventListener('DOMContentLoaded', function() {
        // Gestion du formulaire de publication
        const publishForm = document.getElementById('publishForm');
        if (publishForm) {
            publishForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(e.target);
                const examSelect = formData.get('examSelect');
                const publicationMode = formData.get('publicationMode');

                console.log('Publication des résultats:', { examSelect, publicationMode });
                alert('Publication des résultats en cours...');
                closeModal('publishModal');

                // Émettre un événement de publication
                emitDataChange('RESULTS_PUBLISHED', { examSelect, publicationMode });
            });
        }

        // Gestion du formulaire de notification
        const notificationForm = document.getElementById('notificationForm');
        if (notificationForm) {
            notificationForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(e.target);
                const studentId = formData.get('studentSelect');
                const message = formData.get('studentMessage');

                console.log('Notification envoyée:', { studentId, message });
                alert('Notification envoyée avec succès !');
                closeModal('notificationModal');

                // Émettre un événement de notification
                emitDataChange('NOTIFICATION_SENT', { studentId, message });
            });
        }


        setupStorageListener();


        loadData();


        setInterval(loadData, 30000);
    });

    // Fonction d'initialisation
    function initializeDashboard() {
        console.log('Initialisation du dashboard administrateur...');
        loadData();
        console.log('Dashboard initialisé avec succès.');
    }
</script>
</body>
</html>
