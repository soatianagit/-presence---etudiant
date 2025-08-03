<?php
// Activer la mise en mémoire tampon de sortie
ob_start();

// Démarrer la session
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href = 'login.php';</script>";
    exit;
}

// Inclure le fichier de connexion à la base de données
require_once 'config/db.php';

// Vérifier si l'ID est fourni
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<script>window.location.href = 'dashboard.php';</script>";
    exit;
}

$id = intval($_GET['id']);

// Vérifier si la présence existe
$stmt = $pdo->prepare("SELECT id FROM presences WHERE id = ?");
$stmt->execute([$id]);

if ($stmt->rowCount() === 0) {
    echo "<script>window.location.href = 'dashboard.php';</script>";
    exit;
}

// Supprimer la présence
$stmt = $pdo->prepare("DELETE FROM presences WHERE id = ?");

try {
    $stmt->execute([$id]);
    $_SESSION['success_message'] = "Présence supprimée avec succès.";
} catch (PDOException $e) {
    $_SESSION['error_message'] = "Erreur lors de la suppression de la présence: " . $e->getMessage();
}

// Rediriger vers le tableau de bord
echo "<script>window.location.href = 'dashboard.php';</script>";
exit;

// Vider et fermer la mémoire tampon
ob_end_flush();
?>
