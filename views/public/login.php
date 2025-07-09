<?php
session_start();
global $pdo;
$registration_success = false;
$error = false;
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $user = login($pdo, $email, $password);

    if ($user) {

        $_SESSION['user'] = [
            'id' => $user['id'],
            'nom' => $user['nom'],
            'prenom' => $user['prenom'],
            'email' => $user['email'],
            'role' => $user['role'],
            'date' => $user['date_creation'],
        ];
        switch ($user['role']) {
            case 'admin':
                header('Location: ../../admin/dashboard.php');
                break;
            case 'candidat':
                header('Location: ../../candidat/Profil.php');
                break;
            case 'correcteur':
                header('Location: ../../correcteur/Profil.php');
                break;
            default:
                header('Location: accueil.php');
                break;
        }
        exit;

    } else {

        $_SESSION['form_errors'] = ['global' => 'Email ou mot de passe invalide.'];
        $_SESSION['old_input'] = ['email' => $email];
        header('Location: login.php');
        exit;
    }
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Système de Gestion de Concours</title>
    <link rel="stylesheet" href="../../assets/css/login.css">

</head>
<body>

<div id="loginPage" class="login-container">
    <h2 style="margin-bottom: 30px; color: #2d3748;">Connexion</h2>


    <form id="loginForm" action="" method="post">
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>

        </div>
        <div class="form-group">
            <label for="password">Mot de passe</label>
            <input type="password" id="password" name="password" required>

        </div>
        <button type="submit" class="btn" style="width: 100%;">Se connecter</button>
    </form>
    <div class="footer">
        <p>Pas encore de compte ? <a href="register.php">Inscrivez-vous ici</a></p>
    </div>
</div>



</body>
</html>
