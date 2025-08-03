<?php
// Inclure le fichier d'en-tête
require_once 'includes/header.php';

// Inclure le fichier de connexion à la base de données
require_once 'config/db.php';

// Initialiser les variables de filtrage
$date_filter = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
$cours_filter = isset($_GET['cours']) ? $_GET['cours'] : '';
$niveau_filter = isset($_GET['niveau']) ? $_GET['niveau'] : '';
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$search_term = isset($_GET['search']) ? $_GET['search'] : '';

// Construire la requête SQL de base
$sql = "SELECT p.id, p.etudiant_name, p.status, p.date_presence, p.heure_debut, p.heure_fin, p.cours, e.niveau 
        FROM presences p 
        JOIN etudiants e ON p.etudiant_id = e.id 
        WHERE 1=1";
$params = [];

// Ajouter les filtres à la requête
if (!empty($date_filter)) {
    $sql .= " AND p.date_presence = ?";
    $params[] = $date_filter;
}

if (!empty($cours_filter)) {
    $sql .= " AND p.cours = ?";
    $params[] = $cours_filter;
}

if (!empty($niveau_filter)) {
    $sql .= " AND e.niveau = ?";
    $params[] = $niveau_filter;
}

if (!empty($status_filter)) {
    $sql .= " AND p.status = ?";
    $params[] = $status_filter;
}

if (!empty($search_term)) {
    $sql .= " AND (p.etudiant_name LIKE ? OR p.cours LIKE ? OR e.niveau LIKE ?)";
    $search_param = "%$search_term%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
}

// Ajouter l'ordre de tri
$sql .= " ORDER BY p.date_presence DESC, p.heure_debut ASC";

// Exécuter la requête
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$presences = $stmt->fetchAll();

// Récupérer la liste des cours pour le filtre
$stmt = $pdo->query("SELECT DISTINCT cours FROM presences ORDER BY cours");
$cours_list = $stmt->fetchAll();

// Récupérer la liste des niveaux pour le filtre
$stmt = $pdo->query("SELECT DISTINCT niveau FROM etudiants ORDER BY niveau");
$niveaux_list = $stmt->fetchAll();

// Statistiques
$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM presences WHERE date_presence = ?");
$stmt->execute([$date_filter]);
$total_presences = $stmt->fetch()['total'];

$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM presences WHERE date_presence = ? AND status = 'PRESENT'");
$stmt->execute([$date_filter]);
$total_presents = $stmt->fetch()['total'];

$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM presences WHERE date_presence = ? AND status = 'ABSENT'");
$stmt->execute([$date_filter]);
$total_absents = $stmt->fetch()['total'];

$taux_presence = $total_presences > 0 ? round(($total_presents / $total_presences) * 100) : 0;
?>

<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Tableau de Bord - Gestion des Présences</h5>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card text-center">
                            <div class="card-body">
                                <h5 class="card-title">Total des Présences</h5>
                                <p class="card-text display-4"><?php echo $total_presents; ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-center">
                            <div class="card-body">
                                <h5 class="card-title">Total des Absences</h5>
                                <p class="card-text display-4"><?php echo $total_absents; ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-center">
                            <div class="card-body">
                                <h5 class="card-title">Taux de Présence</h5>
                                <p class="card-text display-4"><?php echo $taux_presence; ?>%</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <form method="get" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="mb-4">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label for="date" class="form-label">Date</label>
                            <input type="date" class="form-control" id="date" name="date" value="<?php echo $date_filter; ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="cours" class="form-label">Cours</label>
                            <select class="form-select" id="cours" name="cours">
                                <option value="">Tous les cours</option>
                                <?php foreach ($cours_list as $cours): ?>
                                    <option value="<?php echo htmlspecialchars($cours['cours']); ?>" <?php echo $cours_filter === $cours['cours'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($cours['cours']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="niveau" class="form-label">Niveau</label>
                            <select class="form-select" id="niveau" name="niveau">
                                <option value="">Tous les niveaux</option>
                                <?php foreach ($niveaux_list as $niveau): ?>
                                    <option value="<?php echo htmlspecialchars($niveau['niveau']); ?>" <?php echo $niveau_filter === $niveau['niveau'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($niveau['niveau']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="status" class="form-label">Statut</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">Tous</option>
                                <option value="PRESENT" <?php echo $status_filter === 'PRESENT' ? 'selected' : ''; ?>>Présent</option>
                                <option value="ABSENT" <?php echo $status_filter === 'ABSENT' ? 'selected' : ''; ?>>Absent</option>
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-dark w-100">Filtrer</button>
                        </div>
                    </div>
                </form>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <form method="get" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="d-flex">
                            <input type="hidden" name="date" value="<?php echo $date_filter; ?>">
                            <input type="hidden" name="cours" value="<?php echo $cours_filter; ?>">
                            <input type="hidden" name="niveau" value="<?php echo $niveau_filter; ?>">
                            <input type="hidden" name="status" value="<?php echo $status_filter; ?>">
                            <input type="text" class="form-control me-2" name="search" placeholder="Rechercher..." value="<?php echo htmlspecialchars($search_term); ?>">
                            <button type="submit" class="btn btn-dark">Rechercher</button>
                        </form>
                    </div>
                    <div class="col-md-6 text-end">
                        <a href="ajouter_presence.php" class="btn btn-dark">Ajouter une présence</a>
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table id="presenceTable" class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Étudiant</th>
                                <th>Niveau</th>
                                <th>Statut</th>
                                <th>Date</th>
                                <th>Heure Début</th>
                                <th>Heure Fin</th>
                                <th>Cours</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($presences)): ?>
                                <tr>
                                    <td colspan="9" class="text-center">Aucune présence trouvée</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($presences as $presence): ?>
                                    <tr>
                                        <td><?php echo $presence['id']; ?></td>
                                        <td><?php echo htmlspecialchars($presence['etudiant_name']); ?></td>
                                        <td><?php echo htmlspecialchars($presence['niveau']); ?></td>
                                        <td>
                                            <span class="badge <?php echo $presence['status'] === 'PRESENT' ? 'bg-success' : 'bg-danger'; ?>">
                                                <?php echo $presence['status']; ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('d/m/Y', strtotime($presence['date_presence'])); ?></td>
                                        <td><?php echo date('H:i', strtotime($presence['heure_debut'])); ?></td>
                                        <td><?php echo date('H:i', strtotime($presence['heure_fin'])); ?></td>
                                        <td><?php echo htmlspecialchars($presence['cours']); ?></td>
                                        <td>
                                            <a href="modifier_presence.php?id=<?php echo $presence['id']; ?>" class="btn btn-sm btn-dark">Modifier</a>
                                            <a href="supprimer_presence.php?id=<?php echo $presence['id']; ?>" class="btn btn-sm btn-outline-dark" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette présence?')">Supprimer</a>
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
        $('#presenceTable').DataTable({
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
