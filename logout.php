<?php
// Activer la mise en mémoire tampon de sortie
ob_start();

// Démarrer la session
session_start();

// Supprimer toutes les variables de session
$_SESSION = array();

// Supprimer le cookie de session
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Supprimer le cookie de mémorisation
setcookie('remember_user', '', time() - 3600, '/');

// Détruire la session
session_destroy();

// Rediriger vers la page de connexion
echo "<script>window.location.href = 'login.php';</script>";
exit;

// Vider et fermer la mémoire tampon
ob_end_flush();
?>
