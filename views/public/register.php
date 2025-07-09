<?php
session_start();
global $pdo;
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/auth.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'] ?? '';
    $prenom = $_POST['prenom'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirmPassword'] ?? '';
    $role = 'candidat';


    $result = register(
        $pdo,
        $nom,
        $prenom,
        $email,
        $password,
        $confirmPassword,
        $role
    );

    if ($result['success']) {
        // 🟢 Succès → redirection vers login
        $_SESSION['flash_message'] = "Compte créé avec succès. Vous pouvez maintenant vous connecter.";
        header('Location: login.php');
        exit;
    } else {

        $_SESSION['form_errors'] = $result['errors'];
        $_SESSION['old_input'] = [
            'nom' => $nom,
            'prenom' => $prenom,
            'email' => $email
        ];
        header('Location: register.php');
        exit;
    }
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
    <link rel="stylesheet" href="../../assets/css/register.css">

</head>
<body>
<div class="register-container">
    <h2>Inscription</h2>

    <?php if (!empty($_SESSION['form_errors'])): ?>
        <ul class="alert-danger">
            <?php foreach ($_SESSION['form_errors'] as $err): ?>
                <li class="alert-danger"><?= htmlspecialchars($err) ?></li>
            <?php endforeach; ?>
        </ul>
        <?php unset($_SESSION['form_errors']); ?>
    <?php endif; ?>

<!---->
<!--    <input type="text" name="nom" value="--><?php //= htmlspecialchars($_SESSION['old_input']['email'] ?? '') ?><!--">-->
<!--    --><?php //unset($_SESSION['old_input']); ?>


    <form id="registerForm" action="" method="POST">
        <div class="form-group">
            <label for="nom">Nom *</label>
            <input type="text" id="nom" name="nom" required value="<?php echo isset($_POST['nom']) ? htmlspecialchars($_POST['nom']) : ''; ?>">
            <div class="error-message" id="nomError"><?php echo isset($errors['nom']) ? $errors['nom'] : ''; ?></div>
        </div>

        <div class="form-group">
            <label for="prenom">Prénom *</label>
            <input type="text" id="prenom" name="prenom" required value="<?php echo isset($_POST['prenom']) ? htmlspecialchars($_POST['prenom']) : ''; ?>">
            <div class="error-message" id="prenomError"><?php echo isset($errors['prenom']) ? $errors['prenom'] : ''; ?></div>
        </div>

        <div class="form-group">
            <label for="email">Email *</label>
            <input type="email" id="email" name="email" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
            <div class="error-message" id="emailError"><?php echo isset($errors['email']) ? $errors['email'] : ''; ?></div>
            <div class="success-message" id="emailSuccess"></div>
        </div>

        <div class="form-group">
            <label for="password">Mot de passe *</label>
            <input type="password" id="password" name="password" required>
            <div class="error-message" id="passwordError"><?php echo isset($errors['password']) ? $errors['password'] : ''; ?></div>
            <div class="password-strength" id="passwordStrength"></div>
        </div>

        <div class="form-group">
            <label for="confirmPassword">Confirmer le mot de passe *</label>
            <input type="password" id="confirmPassword" name="confirmPassword" required>
            <div class="error-message" id="confirmPasswordError"><?php echo isset($errors['confirmPassword']) ? $errors['confirmPassword'] : ''; ?></div>
            <div class="success-message" id="confirmPasswordSuccess"></div>
        </div>

        <button type="submit" class="register-btn" id="registerBtn">S'inscrire</button>
    </form>

    <div class="form-response" id="formResponse"></div>

    <div class="footer">
        <p>Déjà un compte ? <a href="login.php">Connectez-vous ici</a></p>
    </div>
</div>

<script src="../../assets/js/register.js"></script>



</body>
</html>
