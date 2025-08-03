<?php
// Activer la mise en mémoire tampon de sortie
ob_start();

// Inclure le fichier d'en-tête
require_once 'includes/header.php';

// Inclure le fichier de connexion à la base de données
require_once 'config/db.php';

$success = '';
$error = '';

// Traitement du formulaire d'ajout d'étudiant
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom_et_prenom = trim($_POST['nom_et_prenom']);
    $matricule = trim($_POST['matricule']);
    $niveau = trim($_POST['niveau']);
    
    // Vérifier si les champs sont vides
    if (empty($nom_et_prenom) || empty($matricule) || empty($niveau)) {
        $error = "Tous les champs sont obligatoires.";
    } else {
        // Vérifier si le matricule existe déjà
        $stmt = $pdo->prepare("SELECT id FROM etudiants WHERE matricule = ?");
        $stmt->execute([$matricule]);
        
        if ($stmt->rowCount() > 0) {
            $error = "Ce matricule existe déjà.";
        } else {
            // Insérer le nouvel étudiant
            $stmt = $pdo->prepare("INSERT INTO etudiants (nom_et_prenom, matricule, niveau) VALUES (?, ?, ?)");
            
            try {
                $stmt->execute([$nom_et_prenom, $matricule, $niveau]);
                $success = "Étudiant ajouté avec succès.";
                
                // Utiliser JavaScript pour la redirection au lieu de header()
                echo "<script>
                    setTimeout(function() {
                        window.location.href = 'etudiants.php';
                    }, 2000);
                </script>";
            } catch (PDOException $e) {
                $error = "Erreur lors de l'ajout de l'étudiant: " . $e->getMessage();
            }
        }
    }
}

// Récupérer la liste des niveaux existants
$stmt = $pdo->query("SELECT DISTINCT niveau FROM etudiants ORDER BY niveau");
$niveaux = $stmt->fetchAll();
?>

<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Ajouter un Étudiant</h5>
                <a href="etudiants.php" class="btn btn-sm btn-outline-light">Retour à la liste</a>
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
                
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <div class="mb-3">
                        <label for="nom_et_prenom" class="form-label">Nom et Prénom</label>
                        <input type="text" class="form-control" id="nom_et_prenom" name="nom_et_prenom" required>
                    </div>
                    <div class="mb-3">
                        <label for="matricule" class="form-label">Matricule</label>
                        <input type="text" class="form-control" id="matricule" name="matricule" required>
                    </div>
                    <div class="mb-3">
                        <label for="niveau" class="form-label">Niveau</label>
                        <select class="form-select" id="niveau" name="niveau" required>
                            <option value="">Sélectionner un niveau</option>
                            <?php foreach ($niveaux as $niveau): ?>
                                <option value="<?php echo htmlspecialchars($niveau['niveau']); ?>">
                                    <?php echo htmlspecialchars($niveau['niveau']); ?>
                                </option>
                            <?php endforeach; ?>
                            <option value="Licence 1">Licence 1</option>
                            <option value="Licence 2">Licence 2</option>
                            <option value="Licence 3">Licence 3</option>
                            <option value="Master 1">Master 1</option>
                            <option value="Master 2">Master 2</option>
                            <option value="Doctorat">Doctorat</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-dark">Ajouter</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
// Inclure le fichier de pied de page
require_once 'includes/footer.php';
?>
