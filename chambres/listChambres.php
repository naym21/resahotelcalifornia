<?php
// Inclusion du fichier de connexion à la base de données
require_once '../config/db_connect.php';
require_once '../auth/AuthFunctions.php';
if (!hasRole("directeur")) {
    $encodedMessage = urlencode("ERREUR : Vous n'avez pas les bonnes permissions.");
    header("Location: /resaHotelCalifornia/auth/login.php?message=$encodedMessage");
    exit;
    }
// Fonction pour vérifier la disponibilité d'une chambre
function checkAvailability($chambre_id) {
    try {
        $conn = openDatabaseConnection();
        
        // Vérifier s'il y a des réservations pour cette chambre
        $query = "
            SELECT COUNT(*) FROM reservations 
            WHERE chambre_id = :chambre_id 
            AND (date_arrivee <= CURDATE() AND date_depart >= CURDATE())";
        
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':chambre_id', $chambre_id, PDO::PARAM_INT);
        $stmt->execute();
        
        // Si le résultat est plus grand que 0, cela signifie que la chambre est réservée
        $count = $stmt->fetchColumn();
        
        // Si aucune réservation n'est trouvée, la chambre est disponible
        return $count == 0 ? 'Disponible' : 'Indisponible';
    } catch (PDOException $e) {
        echo "Erreur de connexion ou d'exécution de la requête : " . $e->getMessage();
        return 'Erreur';
    } finally {
        closeDatabaseConnection($conn);
    }
}

try {
    // Récupération des chambres depuis la base de données
    $conn = openDatabaseConnection();

    $query = "SELECT * FROM chambres ORDER BY numero";
    $stmt = $conn->query($query);
    $chambres = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fermer la connexion à la base de données
    closeDatabaseConnection($conn);
} catch (PDOException $e) {
    // Affichage d'une erreur si la connexion échoue ou si une requête échoue
    echo "Erreur de connexion ou d'exécution de la requête : " . $e->getMessage();
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <title>Liste des Chambres</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Lien vers la feuille de style externe -->
    <link rel="stylesheet" href="../assets/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet">
</head>

<body>
<?php include_once '../assets/gestionMessage.php'; ?>
<?php include '../assets/navbar.php'; ?>
    

    <div class="container">
        <h1>Liste des Chambres</h1>

        <div class="actions">
            <a href="createChambre.php" class="btn btn-success">Ajouter une Chambre</a>
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Numéro</th>
                    <th>Capacité</th>
                    <th>Disponibilité</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($chambres) > 0): ?>
                    <?php foreach ($chambres as $chambre): ?>
                        <tr>
                            <td><?= htmlspecialchars($chambre['chambre_id']) ?></td>
                            <td><?= htmlspecialchars($chambre['numero']) ?></td>
                            <td><?= htmlspecialchars($chambre['capacite']) ?></td>
                            <td>
                                <?php
                                    // Vérifier la disponibilité de la chambre
                                    $availability = checkAvailability($chambre['chambre_id']);
                                    echo $availability;
                                ?>
                            </td>
                            <td>
                                <a href="editChambre.php?id=<?= $chambre['chambre_id'] ?>" aria-label="Modifier la chambre">
                                    <i class="fas fa-pen" aria-hidden="true"></i>
                                </a>
                                <a href="deleteChambre.php?id=<?= $chambre['chambre_id'] ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette chambre?');" aria-label="Supprimer la chambre">
                                    <i class="fas fa-trash" aria-hidden="true"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">Aucune chambre trouvée.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
</body>

</html>
