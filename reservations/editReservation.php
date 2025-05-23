<?php
require_once '../config/db_connect.php';

$reservation_id = $_GET['id'] ?? null;

if (!$reservation_id) {
    die("ID de réservation non spécifié.");
}

try {
    $conn = openDatabaseConnection();

    // Récupérer les informations de la réservation
    $stmt = $conn->prepare("SELECT r.id, r.date_arrivee, r.date_depart, r.chambre_id,
                                   c.client_id, CONCAT(c.nom, ' ', c.prenom) AS client_nom
                            FROM reservations r
                            JOIN clients c ON r.client_id = c.client_id
                            WHERE r.id = ?");
    $stmt->execute([$reservation_id]);
    $reservation = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$reservation) {
        die("Réservation non trouvée.");
    }

    // Récupérer les clients pour le menu déroulant
    $stmt = $conn->query("SELECT client_id, CONCAT(nom, ' ', prenom) AS nom_complet FROM clients ORDER BY nom, prenom");
    $clients = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Récupérer les chambres disponibles
    $stmt = $conn->query("SELECT chambre_id, numero FROM chambres");
    $chambres = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Récupérer les données du formulaire
        $client_id = $_POST['client_id'];
        $date_arrivee = $_POST['date_arrivee'];
        $date_depart = $_POST['date_depart'];
        $chambre_id = $_POST['chambre_id'];

        // Mettre à jour la réservation
        $stmt = $conn->prepare("UPDATE reservations
                               SET client_id = ?, date_arrivee = ?, date_depart = ?, chambre_id = ?
                               WHERE id = ?");
        $stmt->execute([$client_id, $date_arrivee, $date_depart, $chambre_id, $reservation_id]);

        // Rediriger après la mise à jour
        header("Location: listReservations.php");
        exit;
    }

    closeDatabaseConnection($conn);

} catch (PDOException $e) {
    die("Erreur de connexion ou d'exécution de la requête : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier une Réservation</title>
    <!-- Lien vers Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Lien vers Font Awesome pour les icônes -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
</head>

<body>
<?php include_once '../assets/gestionMessage.php'; ?>
<?php include '../assets/navbar.php'; ?>

    <div class="container mt-5">
        <h1 class="mb-4">Modifier une Réservation</h1>
        <form method="post">
            <!-- Champ Client -->
            <div class="row mb-3 align-items-center">
                <label for="client_id" class="col-2 col-form-label text-end">Client</label>
                <div class="col-4">
                    <select id="client_id" name="client_id" class="form-control" required>
                        <?php foreach($clients as $client): ?>
                            <option value="<?= htmlspecialchars($client['client_id']) ?>"
                                <?= $client['client_id'] == $reservation['client_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($client['nom_complet']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <!-- Champ Date d'Arrivée -->
            <div class="row mb-3 align-items-center">
                <label for="date_arrivee" class="col-2 col-form-label text-end">Date d'Arrivée</label>
                <div class="col-4">
                    <input type="date" id="date_arrivee" name="date_arrivee" class="form-control" value="<?= htmlspecialchars($reservation['date_arrivee']) ?>" required>
                </div>
            </div>
            <!-- Champ Date de Départ -->
            <div class="row mb-3 align-items-center">
                <label for="date_depart" class="col-2 col-form-label text-end">Date de Départ</label>
                <div class="col-4">
                    <input type="date" id="date_depart" name="date_depart" class="form-control" value="<?= htmlspecialchars($reservation['date_depart']) ?>" required>
                </div>
            </div>
            <!-- Champ Choisir une Chambre -->
            <div class="row mb-3 align-items-center">
                <label for="chambre_id" class="col-2 col-form-label text-end">Choisir une Chambre</label>
                <div class="col-4">
                    <select id="chambre_id" name="chambre_id" class="form-control" required>
                        <?php foreach($chambres as $chambre): ?>
                            <option value="<?= htmlspecialchars($chambre['chambre_id']) ?>"
                                <?= $chambre['chambre_id'] == $reservation['chambre_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($chambre['numero']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-2 text-end">
                    <!-- Bouton de retour -->
                    <a href="listReservations.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Retour
                    </a>
                </div>
                <div class="col-4 text-end">
                    <!-- Bouton validation -->
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-check"></i> Enregistrer les Modifications
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Ajouter Bootstrap JS et ses dépendances -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
