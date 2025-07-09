<?php


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

global $pdo;

require_once __DIR__ . '/../config/db.php';

//$_files pour les fichiers ,c'est une variable Superglobale
//var_dump($_FILES); //voir si c'est envoyé

//on vérifie d'abord si notre fichier a été envoyé
if(isset($_FILES['fichier']) && $_FILES['fichier']["error"]===0){
    //on a reçu l'image
    //le error permets de savoir si c'est parti  ça doit etre 0 on fait un vardump pour voir
    //on procède aux vérifications
    //on vérifie toujours l'extention et le type MIME
    $allowed_extensions = ['pdf'=>"application/pdf",
        'zip'=>"application/zip",

    ];

    //on recupere le nom du fichier
    $filename = $_FILES['fichier']['name'];
    //on recupere le type MIME
    $type = $_FILES['fichier']['type'];
    //on récupere egalement la taille
    $size = $_FILES['fichier']['size'];
    //on verifie l'extension
    $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION)); //et on recupere le type(zip ou pdf par exemple)
    //on vérifie l'absence de l'extension dans les clés de $allowed ou de l'absence du type NIME
    // $type !== $allowed_extensions[$extension] et !in_array($type, $allowed_extensions) sont les memes
    if(!array_key_exists($extension, $allowed_extensions) || $type !== $allowed_extensions[$extension] ){
        //ici soit l'extension soit le type est incorrect
        die('Erreur : extension ou type incorrect');
    }
    //ici le type est correct
    //on limite la taille
    if($size > 10000000000000000){
        die('Erreur : ficher volumineux');
    }
    //on va généré un nom anonyme(unique) pour le fichier question sécurité
    //on genere un nom unique
    $new_name = 'copie_' .substr(md5(uniqid('copie', true)) , 0, 16); //md5 sur 32 bits et uniqid sur 23
    //on genere un chemin unique
    $new_filename = __DIR__ . '/../data/' . $new_name . '.' . $extension;




    echo $new_filename;
    //on déplace le fichier dans le repertoire
    //move_uploaded_file — Déplace un fichier téléchargé   move_uploaded_file(string $from, string $to): bool
    if(!move_uploaded_file($_FILES['fichier']['tmp_name'], $new_filename)){
        //tp_name corresponds au chemin d'avant
        //si ce n'est pas renommé
        die("Erreur lors de l'envoi du fichier");
    }



    if (!isset($_SESSION['user']['id'])) {
        die("Erreur : utilisateur non connecté.");
    }

    $id_candidat = $_SESSION['user']['id'];


    $insert = $pdo->prepare("INSERT INTO copies (id_candidat,  identifiant_anonyme, fichier,taille_fichier,format_fichier) VALUES (?, ?, ?,?,?)");

    $insert->execute([
        $id_candidat,
        $new_name,
        $filename,
        $size,
        $extension,
    ]);

    //on interdit l'excécution genre on restrain
    //chmod — Change le mode du fichier
    //trois valeurs octales qui spécifient les droits pour le propriétaire, le groupe du propriétaire et les autres, respectivement
    //Le chiffre 1 donne les droits d'exécution, le chiffre 2 les droits d'écriture et le chiffre 4 les droits de lecture. Ajoutez simplement ces nombres pour spécifier les droits voulus
    //dans nous notre cas Lecture et écriture pour le propriétaire, lecture pour les autres
    chmod($new_filename, 0644);
    //Lisible/écrivable par le serveur (propriétaire)
    //Lisible par les autres (pour téléchargement)
    //Pas modifiable par l’extérieur

    header('Location: Profil.php');
    exit;


}
?>
