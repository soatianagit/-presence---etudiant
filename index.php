<?php
// Activer la mise en mémoire tampon de sortie
ob_start();

// Démarrer la session
session_start();

// Rediriger vers la page de connexion si l'utilisateur n'est pas connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Rediriger vers le tableau de bord
header("Location: dashboard.php");
exit;

// Vider et fermer la mémoire tampon
ob_end_flush();
?>
