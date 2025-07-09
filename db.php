<?php
// config/database.php
class Database {
    private $host = "localhost";
    private $db_name = "concours_db";
    private $username = "root";
    private $password = "";
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8");
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            echo "Erreur de connexion : " . $exception->getMessage();
        }
        return $this->conn;
    }
}

// ================================
// auth/login.php
<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

// Récupération des données POST
$input = json_decode(file_get_contents('php://input'), true);

$email = trim($input['email'] ?? '');
$mot_de_passe = trim($input['mot_de_passe'] ?? '');
$role = trim($input['role'] ?? '');

// Validation des données
$errors = [];

if (empty($email)) {
    $errors[] = "L'email est obligatoire";
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Format d'email invalide";
}

if (empty($mot_de_passe)) {
    $errors[] = "Le mot de passe est obligatoire";
}

if (empty($role)) {
    $errors[] = "Le rôle est obligatoire";
} elseif (!in_array($role, ['candidat', 'correcteur', 'admin'])) {
    $errors[] = "Rôle non valide";
}

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => implode(', ', $errors)]);
    exit;
}

try {
    // Connexion à la base de données
    $database = new Database();
    $db = $database->getConnection();

    // Recherche de l'utilisateur
    $query = "SELECT id, nom, prenom, email, mot_de_passe, role, date_creation 
              FROM utilisateurs 
              WHERE email = :email AND role = :role";

    $stmt = $db->prepare($query);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':role', $role);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'message' => 'Email, mot de passe ou rôle incorrect'
        ]);
        exit;
    }

    // Vérification du mot de passe
    if (!password_verify($mot_de_passe, $user['mot_de_passe'])) {
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'message' => 'Email, mot de passe ou rôle incorrect'
        ]);
        exit;
    }

    // Connexion réussie - Création de la session
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_role'] = $user['role'];
    $_SESSION['user_nom'] = $user['nom'];
    $_SESSION['user_prenom'] = $user['prenom'];

    // Mise à jour de la dernière connexion (optionnel)
    $updateQuery = "UPDATE utilisateurs SET derniere_connexion = CURRENT_TIMESTAMP WHERE id = :id";
    $updateStmt = $db->prepare($updateQuery);
    $updateStmt->bindParam(':id', $user['id']);
    $updateStmt->execute();

    // Réponse de succès
    echo json_encode([
        'success' => true,
        'message' => 'Connexion réussie',
        'user' => [
            'id' => $user['id'],
            'nom' => $user['nom'],
            'prenom' => $user['prenom'],
            'email' => $user['email'],
            'role' => $user['role']
        ]
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur de base de données : ' . $e->getMessage()
    ]);
}
?>

// ================================
// auth/register.php
<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

// Récupération des données POST
$input = json_decode(file_get_contents('php://input'), true);

$nom = trim($input['nom'] ?? '');
$prenom = trim($input['prenom'] ?? '');
$email = trim($input['email'] ?? '');
$mot_de_passe = trim($input['mot_de_passe'] ?? '');
$role = trim($input['role'] ?? '');

// Validation des données
$errors = [];

// Validation du nom
if (empty($nom)) {
    $errors[] = "Le nom est obligatoire";
} elseif (strlen($nom) > 20) {
    $errors[] = "Le nom ne peut pas dépasser 20 caractères";
} elseif (!preg_match('/^[a-zA-ZÀ-ÿ\s\-\']+$/', $nom)) {
    $errors[] = "Le nom contient des caractères non autorisés";
}

// Validation du prénom
if (empty($prenom)) {
    $errors[] = "Le prénom est obligatoire";
} elseif (strlen($prenom) > 20) {
    $errors[] = "Le prénom ne peut pas dépasser 20 caractères";
} elseif (!preg_match('/^[a-zA-ZÀ-ÿ\s\-\']+$/', $prenom)) {
    $errors[] = "Le prénom contient des caractères non autorisés";
}

// Validation de l'email
if (empty($email)) {
    $errors[] = "L'email est obligatoire";
} elseif (strlen($email) > 50) {
    $errors[] = "L'email ne peut pas dépasser 50 caractères";
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Format d'email invalide";
}

// Validation du mot de passe
if (empty($mot_de_passe)) {
    $errors[] = "Le mot de passe est obligatoire";
} elseif (strlen($mot_de_passe) > 12) {
    $errors[] = "Le mot de passe ne peut pas dépasser 12 caractères";
} elseif (strlen($mot_de_passe) < 6) {
    $errors[] = "Le mot de passe doit contenir au moins 6 caractères";
}

// Validation du rôle
if (empty($role)) {
    $errors[] = "Le rôle est obligatoire";
} elseif (!in_array($role, ['candidat', 'correcteur'])) {
    $errors[] = "Rôle non autorisé pour l'inscription";
}

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => implode(', ', $errors)]);
    exit;
}

try {
    // Connexion à la base de données
    $database = new Database();
    $db = $database->getConnection();

    // Vérifier si l'email existe déjà
    $checkQuery = "SELECT id FROM utilisateurs WHERE email = :email";
    $checkStmt = $db->prepare($checkQuery);
    $checkStmt->bindParam(':email', $email);
    $checkStmt->execute();

    if ($checkStmt->rowCount() > 0) {
        http_response_code(409);
        echo json_encode([
            'success' => false,
            'message' => 'Un compte avec cet email existe déjà'
        ]);
        exit;
    }

    // Hachage du mot de passe
    $mot_de_passe_hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);

    // Insertion du nouvel utilisateur
    $insertQuery = "INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, role) 
                    VALUES (:nom, :prenom, :email, :mot_de_passe, :role)";

    $insertStmt = $db->prepare($insertQuery);
    $insertStmt->bindParam(':nom', $nom);
    $insertStmt->bindParam(':prenom', $prenom);
    $insertStmt->bindParam(':email', $email);
    $insertStmt->bindParam(':mot_de_passe', $mot_de_passe_hash);
    $insertStmt->bindParam(':role', $role);

    if ($insertStmt->execute()) {
        $userId = $db->lastInsertId();

        echo json_encode([
            'success' => true,
            'message' => 'Compte créé avec succès',
            'user_id' => $userId
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Erreur lors de la création du compte'
        ]);
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur de base de données : ' . $e->getMessage()
    ]);
}
?>

// ================================
// auth/logout.php
<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

// Détruire toutes les variables de session
$_SESSION = array();

// Détruire le cookie de session si il existe
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Détruire la session
session_destroy();

echo json_encode([
    'success' => true,
    'message' => 'Déconnexion réussie'
]);
?>

// ================================
// auth/check_session.php
<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

if (isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => true,
        'logged_in' => true,
        'user' => [
            'id' => $_SESSION['user_id'],
            'nom' => $_SESSION['user_nom'],
            'prenom' => $_SESSION['user_prenom'],
            'email' => $_SESSION['user_email'],
            'role' => $_SESSION['user_role']
        ]
    ]);
} else {
    echo json_encode([
        'success' => true,
        'logged_in' => false
    ]);
}
?>

// ================================
// utils/auth_middleware.php
<?php
function requireAuth($allowedRoles = []) {
    session_start();

    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'message' => 'Authentification requise'
        ]);
        exit;
    }

    if (!empty($allowedRoles) && !in_array($_SESSION['user_role'], $allowedRoles)) {
        http_response_code(403);
        echo json_encode([
            'success' => false,
            'message' => 'Accès non autorisé'
        ]);
        exit;
    }

    return $_SESSION;
}

// Exemple d'utilisation :
// require_once '../utils/auth_middleware.php';
// $user = requireAuth(['admin', 'correcteur']); // Seuls admin et correcteur autorisés
?>

// ================================
// SQL pour ajouter une colonne derniere_connexion (optionnel)
/*
ALTER TABLE utilisateurs
ADD COLUMN derniere_connexion DATETIME DEFAULT NULL;
*/