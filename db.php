<?php
// Paramètres de connexion à la base de données
$host = '127.0.0.1';
$dbname = 'gestion_presence';
$username = 'root';
$password = '';

try {
    // Création d'une nouvelle connexion PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    
    // Configuration des options PDO
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch (PDOException $e) {
    // En cas d'erreur, afficher le message et arrêter le script
    die("Erreur de connexion à la base de données: " . $e->getMessage());
}
?>
