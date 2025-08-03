<?php
// Inclure le fichier d'en-tête
require_once 'includes/header.php';

// Inclure le fichier de connexion à la base de données
require_once 'config/db.php';

// Initialiser la variable de recherche
$search_term = isset($_GET['search']) ? $_GET['search'] : '';

// Construire la requête SQL
$sql = "SELECT * FROM etudiants WHERE 1=1";
$params = [];

// Ajouter le filtre de recherche
if (!empty($search_term)) {
    $sql .= " AND (nom_et_prenom LIKE ? OR matricule LIKE ? OR niveau LIKE ?)";
    $search_param = "%$search_term%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
}

// Ajouter l'ordre de tri
$sql .= " ORDER BY nom_et_prenom ASC";

// Exécuter la requête
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$etudiants = $stmt->fetchAll();
?>

<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Gestion des Étudiants</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <form method="get" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="d-flex">
                            <input type="text" class="form-control me-2" name="search" placeholder="Rechercher un étudiant..." value="<?php echo htmlspecialchars($search_term); ?>">
                            <button type="submit" class="btn btn-dark">Rechercher</button>
                        </form>
                    </div>
                    <div class="col-md-6 text-end">
                        <a href="ajouter_etudiant.php" class="btn btn-dark">Ajouter un étudiant</a>
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table id="etudiantsTable" class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Nom et Prénom</th>
                                <th>Matricule</th>
                                <th>Niveau</th>
                                <th>Date de création</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($etudiants)): ?>
                                <tr>
                                    <td colspan="6" class="text-center">Aucun étudiant trouvé</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($etudiants as $etudiant): ?>
                                    <tr>
                                        <td><?php echo $etudiant['id']; ?></td>
                                        <td><?php echo htmlspecialchars($etudiant['nom_et_prenom']); ?></td>
                                        <td><?php echo htmlspecialchars($etudiant['matricule']); ?></td>
                                        <td><?php echo htmlspecialchars($etudiant['niveau']); ?></td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($etudiant['created_at'])); ?></td>
                                        <td>
                                            <a href="modifier_etudiant.php?id=<?php echo $etudiant['id']; ?>" class="btn btn-sm btn-dark">Modifier</a>
                                            <a href="supprimer_etudiant.php?id=<?php echo $etudiant['id']; ?>" class="btn btn-sm btn-outline-dark" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet étudiant? Toutes ses présences seront également supprimées.')">Supprimer</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#etudiantsTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/fr-FR.json'
            },
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": true,
            "pageLength": 10
        });
    });
</script>

<?php
// Inclure le fichier de pied de page
require_once 'includes/footer.php';
?>
