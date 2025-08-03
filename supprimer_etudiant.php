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
    echo "<script>window.location.href = 'etudiants.php';</script>";
    exit;
}

$id = intval($_GET['id']);

// Vérifier si l'étudiant existe
$stmt = $pdo->prepare("SELECT id FROM etudiants WHERE id = ?");
$stmt->execute([$id]);

if ($stmt->rowCount() === 0) {
    echo "<script>window.location.href = 'etudiants.php';</script>";
    exit;
}

// Supprimer l'étudiant (les présences associées seront supprimées automatiquement grâce à la contrainte ON DELETE CASCADE)
$stmt = $pdo->prepare("DELETE FROM etudiants WHERE id = ?");

try {
    $stmt->execute([$id]);
    $_SESSION['success_message'] = "Étudiant supprimé avec succès.";
} catch (PDOException $e) {
    $_SESSION['error_message'] = "Erreur lors de la suppression de l'étudiant: " . $e->getMessage();
}

// Rediriger vers la liste des étudiants
echo "<script>window.location.href = 'etudiants.php';</script>";
exit;

// Vider et fermer la mémoire tampon
ob_end_flush();
?>
