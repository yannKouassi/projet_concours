<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'candidat') {
    header('Location: ../views/public/accueil.php');
    exit;


}
require 'Upload.php';

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <link rel="stylesheet" href="../assets/css/reseau.css">
    
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
    
    <main class="main">
       
        <section class="monmain">
           
            <div class="dashboard">
            <div class="card">
                <div class="card-icon icon-upload">📥</div>
                <h3>Dépôt de copie</h3>
                <p>Déposez votre copie anonymisée au format PDF ou ZIP. Assurez-vous que le fichier ne contient aucune information personnelle.</p>
            </div>
            <div class="card">
                <div class="card-icon icon-track">📊</div>
                <h3>Suivi des copies</h3>
                <p>Consultez le statut de vos copies déposées et téléchargez vos rapports de correction dès qu'ils sont disponibles.</p>
            </div>
        </div>
           
         <div class="confidentiality-notice">
            <h4>🔒 Rappel important sur la confidentialité</h4>
            <p>Votre copie sera traitée de manière anonyme. Veillez à ne pas inclure votre nom, prénom ou toute autre information permettant de vous identifier dans le contenu de votre document.</p>
        </div>
            <div class="section">
            <h2 class="section-title">Dépôt de copie</h2>

            <div class="alert alert-info">
                <strong>Instructions :</strong> Seuls les fichiers PDF et ZIP sont acceptés. Taille maximale : 10 MB. Assurez-vous que votre copie est anonymisée.
            </div>

            <form id="uploadForm"  action="Upload.php" method="post" enctype="multipart/form-data" >
                                    
                        <div class="container2"> 
                        <div class="header2"> 
                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> 
                            <path d="M7 10V9C7 6.23858 9.23858 4 12 4C14.7614 4 17 6.23858 17 9V10C19.2091 10 21 11.7909 21 14C21 15.4806 20.1956 16.8084 19 17.5M7 10C4.79086 10 3 11.7909 3 14C3 15.4806 3.8044 16.8084 5 17.5M7 10C7.43285 10 7.84965 10.0688 8.24006 10.1959M12 12V21M12 12L15 15M12 12L9 15" stroke="#000000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path> </g></svg> <p>📂 Choisir un fichier à envoyer</p>
                        </div> 

                         <div class="file-info" id="fileInfo">
                                <strong>Fichier sélectionné :</strong> <span id="fileName"></span><br>
                                <strong>Taille :</strong> <span id="fileSize"></span>
                                <div class="progress-bar">
                                    <div class="progress-fill" id="uploadProgress"></div>
                                </div>
                         </div>
                        <label for="file" class="footer"> 
                            <svg fill="#000000" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"><path d="M15.331 6H8.5v20h15V14.154h-8.169z"></path><path d="M18.153 6h-.009v5.342H23.5v-.002z"></path></g></svg> 
                            <p >Aucun fichier sélectioné</p>
                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M5.16565 10.1534C5.07629 8.99181 5.99473 8 7.15975 8H16.8402C18.0053 8 18.9237 8.9918 18.8344 10.1534L18.142 19.1534C18.0619 20.1954 17.193 21 16.1479 21H7.85206C6.80699 21 5.93811 20.1954 5.85795 19.1534L5.16565 10.1534Z" stroke="#000000" stroke-width="2"></path> <path d="M19.5 5H4.5" stroke="#000000" stroke-width="2" stroke-linecap="round"></path> <path d="M10 3C10 2.44772 10.4477 2 11 2H13C13.5523 2 14 2.44772 14 3V5H10V3Z" stroke="#000000" stroke-width="2"></path> </g></svg>
                        </label> 
                        <input id="file" type="file" name="fichier" accept=".pdf, .zip" required>
                        </div>
                        <input type="submit" value=" Déposer" class="btn btn-primary">
                        
            
            </form>
        </div>
         <div class="section">
            <h2 class="section-title">Suivi de mes copies</h2>

            <div class="table-container">
                <table>
                    <thead>
                    <tr>
                        <th>Nom du fichier</th>
                        <th>Date de Dépôt</th>
                        <th>Statut</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody id="copiesTable">
                    <?php
                    global $pdo;
                    if (session_status() === PHP_SESSION_NONE) {
                        session_start();
                    }

                    require_once __DIR__ . '/../config/db.php';

                    $stmt = $pdo->prepare(" SELECT fichier, date_depot, statut FROM copies WHERE id_candidat = ? ORDER BY date_depot DESC");
                    $stmt->execute([$_SESSION['user']['id']]);
                    $copies = $stmt->fetchAll();

                    foreach ($copies as $copie):
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($copie['fichier']) ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($copie['date_depot'])) ?></td>
                            <td><?= ucfirst(str_replace('_', ' ', $copie['statut'])) ?></td>
                            <td>
                                <a href="#" class="btn-download" onclick="downloadReport('')">
                                    📥 Télécharger rapport
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>


                    </tbody>
                </table>
            </div>
        </div>

        </section>
        

       
      

        <section class="aside">
              
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


            <article class="suggestion">

            </article>

        </section>
    </main>
</body>
<script src="../assets/js/candidat.js"></script>

</html>














