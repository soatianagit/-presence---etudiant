<?php
// Activer la mise en mémoire tampon de sortie
ob_start();

// Inclure le fichier d'en-tête
require_once 'includes/header.php';

// Inclure le fichier de connexion à la base de données
require_once 'config/db.php';

$success = '';
$error = '';

// Récupérer les informations de l'utilisateur actuel
$stmt = $pdo->prepare("SELECT id, username FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Traitement du formulaire de modification du nom d'utilisateur
if (isset($_POST['update_username'])) {
    $new_username = trim($_POST['new_username']);
    $password = trim($_POST['password']);
    
    // Vérifier si les champs sont vides
    if (empty($new_username) || empty($password)) {
        $error = "Tous les champs sont obligatoires.";
    } else {
        // Vérifier le mot de passe actuel
        $stmt = $pdo->prepare("SELECT id FROM users WHERE id = ? AND password = SHA1(?)");
        $stmt->execute([$_SESSION['user_id'], $password]);
        
        if ($stmt->rowCount() === 0) {
            $error = "Mot de passe incorrect.";
        } else {
            // Vérifier si le nouveau nom d'utilisateur existe déjà
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
            $stmt->execute([$new_username, $_SESSION['user_id']]);
            
            if ($stmt->rowCount() > 0) {
                $error = "Ce nom d'utilisateur existe déjà.";
            } else {
                // Mettre à jour le nom d'utilisateur
                $stmt = $pdo->prepare("UPDATE users SET username = ? WHERE id = ?");
                
                try {
                    $stmt->execute([$new_username, $_SESSION['user_id']]);
                    $_SESSION['username'] = $new_username;
                    $success = "Nom d'utilisateur modifié avec succès.";
                    
                    // Utiliser JavaScript pour la redirection au lieu de header()
                    echo "<script>
                        setTimeout(function() {
                            window.location.href = 'security.php';
                        }, 2000);
                    </script>";
                } catch (PDOException $e) {
                    $error = "Erreur lors de la modification du nom d'utilisateur: " . $e->getMessage();
                }
            }
        }
    }
}

// Traitement du formulaire de modification du mot de passe
if (isset($_POST['update_password'])) {
    $current_password = trim($_POST['current_password']);
    $new_password = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);
    
    // Vérifier si les champs sont vides
    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $error = "Tous les champs sont obligatoires.";
    } elseif ($new_password !== $confirm_password) {
        $error = "Les nouveaux mots de passe ne correspondent pas.";
    } else {
        // Vérifier le mot de passe actuel
        $stmt = $pdo->prepare("SELECT id FROM users WHERE id = ? AND password = SHA1(?)");
        $stmt->execute([$_SESSION['user_id'], $current_password]);
        
        if ($stmt->rowCount() === 0) {
            $error = "Mot de passe actuel incorrect.";
        } else {
            // Mettre à jour le mot de passe
            $stmt = $pdo->prepare("UPDATE users SET password = SHA1(?) WHERE id = ?");
            
            try {
                $stmt->execute([$new_password, $_SESSION['user_id']]);
                $success = "Mot de passe modifié avec succès.";
            } catch (PDOException $e) {
                $error = "Erreur lors de la modification du mot de passe: " . $e->getMessage();
            }
        }
    }
}
?>

<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Paramètres de Sécurité</h5>
            </div>
            <div class="card-body">
                <?php if (!empty($success)): ?>
                    <div class="alert alert-success" role="alert">
                        <?php echo $success; ?>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">Modifier le nom d'utilisateur</h5>
                            </div>
                            <div class="card-body">
                                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                    <div class="mb-3">
                                        <label for="current_username" class="form-label">Nom d'utilisateur actuel</label>
                                        <input type="text" class="form-control" id="current_username" value="<?php echo htmlspecialchars($user['username']); ?>" disabled>
                                    </div>
                                    <div class="mb-3">
                                        <label for="new_username" class="form-label">Nouveau nom d'utilisateur</label>
                                        <input type="text" class="form-control" id="new_username" name="new_username" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="password" class="form-label">Mot de passe actuel (pour confirmation)</label>
                                        <input type="password" class="form-control" id="password" name="password" required>
                                    </div>
                                    <button type="submit" name="update_username" class="btn btn-dark">Modifier le nom d'utilisateur</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Modifier le mot de passe</h5>
                            </div>
                            <div class="card-body">
                                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                    <div class="mb-3">
                                        <label for="current_password" class="form-label">Mot de passe actuel</label>
                                        <input type="password" class="form-control" id="current_password" name="current_password" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="new_password" class="form-label">Nouveau mot de passe</label>
                                        <input type="password" class="form-control" id="new_password" name="new_password" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="confirm_password" class="form-label">Confirmer le nouveau mot de passe</label>
                                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                    </div>
                                    <button type="submit" name="update_password" class="btn btn-dark">Modifier le mot de passe</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Inclure le fichier de pied de page
require_once 'includes/footer.php';
?>
