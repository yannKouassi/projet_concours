<?php
session_start();
require_once __DIR__ . '/../../includes/auth.php';

unset($_SESSION['user']);


header('Location: accueil.php');
exit;
