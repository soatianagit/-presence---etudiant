<?php
// Activer la mise en mémoire tampon de sortie
ob_start();

// Inclure le fichier d'en-tête
require_once 'includes/header.php';

// Inclure le fichier de connexion à la base de données
require_once 'config/db.php';

$success = '';
$error = '';

// Vérifier si l'ID est fourni
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<script>window.location.href = 'dashboard.php';</script>";
    exit;
}

$id = intval($_GET['id']);

// Récupérer les informations de la présence
$stmt = $pdo->prepare("SELECT * FROM presences WHERE id = ?");
$stmt->execute([$id]);
$presence = $stmt->fetch();

// Vérifier si la présence existe
if (!$presence) {
    echo "<script>window.location.href = 'dashboard.php';</script>";
    exit;
}

// Récupérer la liste des étudiants
$stmt = $pdo->query("SELECT id, nom_et_prenom FROM etudiants ORDER BY nom_et_prenom");
$etudiants = $stmt->fetchAll();

// Récupérer la liste des cours existants
$stmt = $pdo->query("SELECT DISTINCT cours FROM presences ORDER BY cours");
$cours_list = $stmt->fetchAll();

// Traitement du formulaire de modification
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $etudiant_id = intval($_POST['etudiant_id']);
    $status = $_POST['status'];
    $date_presence = $_POST['date_presence'];
    $heure_debut = $_POST['heure_debut'];
    $heure_fin = $_POST['heure_fin'];
    $cours = trim($_POST['cours']);
    
    // Vérifier si les champs sont vides
    if (empty($etudiant_id) || empty($status) || empty($date_presence) || empty($heure_debut) || empty($heure_fin) || empty($cours)) {
        $error = "Tous les champs sont obligatoires.";
    } else {
        // Récupérer le nom de l'étudiant
        $stmt = $pdo->prepare("SELECT nom_et_prenom FROM etudiants WHERE id = ?");
        $stmt->execute([$etudiant_id]);
        $etudiant = $stmt->fetch();
        
        if (!$etudiant) {
            $error = "Étudiant non trouvé.";
        } else {
            // Mettre à jour la présence
            $stmt = $pdo->prepare("UPDATE presences SET etudiant_id = ?, etudiant_name = ?, status = ?, date_presence = ?, heure_debut = ?, heure_fin = ?, cours = ? WHERE id = ?");
            
            try {
                $stmt->execute([$etudiant_id, $etudiant['nom_et_prenom'], $status, $date_presence, $heure_debut, $heure_fin, $cours, $id]);
                $success = "Présence modifiée avec succès.";
                
                // Utiliser JavaScript pour la redirection au lieu de header()
                echo "<script>
                    setTimeout(function() {
                        window.location.href = 'dashboard.php';
                    }, 2000);
                </script>";
            } catch (PDOException $e) {
                $error = "Erreur lors de la modification de la présence: " . $e->getMessage();
            }
        }
    }
}
?>

<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Modifier une Présence</h5>
                <a href="dashboard.php" class="btn btn-sm btn-outline-light">Retour au tableau de bord</a>
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
                
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?id=" . $id); ?>">
                    <div class="mb-3">
                        <label for="etudiant_id" class="form-label">Étudiant</label>
                        <select class="form-select" id="etudiant_id" name="etudiant_id" required>
                            <option value="">Sélectionner un étudiant</option>
                            <?php foreach ($etudiants as $etudiant): ?>
                                <option value="<?php echo $etudiant['id']; ?>" <?php echo $presence['etudiant_id'] == $etudiant['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($etudiant['nom_et_prenom']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Statut</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="">Sélectionner un statut</option>
                            <option value="PRESENT" <?php echo $presence['status'] === 'PRESENT' ? 'selected' : ''; ?>>Présent</option>
                            <option value="ABSENT" <?php echo $presence['status'] === 'ABSENT' ? 'selected' : ''; ?>>Absent</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="date_presence" class="form-label">Date</label>
                        <input type="date" class="form-control" id="date_presence" name="date_presence" value="<?php echo $presence['date_presence']; ?>" required>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="heure_debut" class="form-label">Heure de début</label>
                            <input type="time" class="form-control" id="heure_debut" name="heure_debut" value="<?php echo $presence['heure_debut']; ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="heure_fin" class="form-label">Heure de fin</label>
                            <input type="time" class="form-control" id="heure_fin" name="heure_fin" value="<?php echo $presence['heure_fin']; ?>" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="cours" class="form-label">Cours/Séance</label>
                        <input type="text" class="form-control" id="cours" name="cours" list="cours_list" value="<?php echo htmlspecialchars($presence['cours']); ?>" required>
                        <datalist id="cours_list">
                            <?php foreach ($cours_list as $c): ?>
                                <option value="<?php echo htmlspecialchars($c['cours']); ?>">
                            <?php endforeach; ?>
                        </datalist>
                    </div>
                    <button type="submit" class="btn btn-dark">Enregistrer les modifications</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
// Inclure le fichier de pied de page
require_once 'includes/footer.php';
?>
