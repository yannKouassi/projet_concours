<?php global $pdo;
require_once __DIR__ . '/../config/db.php';



function nettoyer(string $donnee) {
    return trim(htmlspecialchars($donnee, ENT_QUOTES, 'UTF-8'));
}

function register($pdo, $nom, $prenom, $email, $password, $confirmPassword, $role) {
    $errors = [];

    // 🔒 Nettoyage
    $nom = nettoyer($nom);
    $prenom = nettoyer($prenom);
    $email = nettoyer($email);
    $role = nettoyer($role);

    // 🔎 Nom
    if (empty($nom) || strlen($nom) < 2 || !preg_match('/^[a-zA-ZÀ-ÿ\s-]+$/u', $nom)) {
        $errors['nom'] = 'Le nom est invalide (minimum 2 lettres, sans caractères spéciaux).';
    }

    // 🔎 Prénom
    if (empty($prenom) || strlen($prenom) < 2 || !preg_match('/^[a-zA-ZÀ-ÿ\s-]+$/u', $prenom)) {
        $errors['prenom'] = 'Le prénom est invalide (minimum 2 lettres, sans caractères spéciaux).';
    }

    // 📧 Email
    if (empty($email)) {
        $errors['email'] = 'L’email est requis.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Le format de l’email est invalide.';
    } else {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        if ($stmt->fetchColumn() > 0) {
            $errors['email'] = 'Cet email est déjà utilisé.';
        }
    }

    // 🔐 Mot de passe
    if (empty($password)) {
        $errors['password'] = 'Le mot de passe est requis.';
    } elseif (strlen($password) < 8 ||
        !preg_match('/[A-Z]/', $password) ||
        !preg_match('/[a-z]/', $password) ||
        !preg_match('/\d/', $password)) {
        $errors['password'] = 'Il doit contenir 8 caractères, une majuscule, une minuscule, un chiffre.';
    }

    // 🔁 Confirmation
    if (empty($confirmPassword)) {
        $errors['confirmPassword'] = 'Merci de confirmer le mot de passe.';
    } elseif ($password !== $confirmPassword) {
        $errors['confirmPassword'] = 'Les mots de passe ne correspondent pas.';
    }

    // 🚀 Si tout est OK
    if (empty($errors)) {
        $passHash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("INSERT INTO users (nom, prenom, email, password, role) VALUES (:nom, :prenom, :email, :password, :role)");

        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':prenom', $prenom);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $passHash);
        $stmt->bindParam(':role', $role);

        $stmt->execute();

        return ['success' => true];
    }

    return ['success' => false, 'errors' => $errors];
}
function login($pdo, string $email, string $password) {
    $email = nettoyer($email);

    if (empty($email) || empty($password)) {
        return false;
    }

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        return $user;
    }

    return false;
}









