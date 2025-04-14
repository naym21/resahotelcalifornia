<?php
require_once '../config/db_connect.php';

try {
    $conn = openDatabaseConnection();

    // Vérification des colonnes dans la table reservations
    $stmt = $conn->query("DESCRIBE reservations");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $required_columns = ['client_id', 'date_reservation', 'heure_reservation', 'chambre_id', 'date_depart', 'nombre_personnes'];
    $existing_columns = array_map(fn($col) => $col['Field'], $columns);
    foreach ($required_columns as $column) {
        if (!in_array($column, $existing_columns)) {
            $type = $column === 'nombre_personnes' ? "INT" : "VARCHAR(255)";
            $conn->exec("ALTER TABLE reservations ADD COLUMN $column $type");
        }
    }

    // Vérification colonne "capacite" dans la table chambres
    $stmt = $conn->query("DESCRIBE chambres");
    $chambre_columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $chambre_fields = array_map(fn($col) => $col['Field'], $chambre_columns);
    if (!in_array('capacite', $chambre_fields)) {
        $conn->exec("ALTER TABLE chambres ADD COLUMN capacite INT DEFAULT 1");
    }

    // Récupération des clients
    $clients = $conn->query("SELECT client_id, CONCAT(nom, ' ', prenom) AS nom_complet FROM clients ORDER BY nom, prenom")->fetchAll(PDO::FETCH_ASSOC);

    // Récupérer les chambres disponibles
    $date_reservation = isset($_POST['date_reservation']) ? $_POST['date_reservation'] : null;
    $date_depart = isset($_POST['date_depart']) ? $_POST['date_depart'] : null;

    if ($date_reservation && $date_depart) {
        // Vérification des chambres disponibles
        $stmt = $conn->prepare("
            SELECT c.chambre_id, c.numero, c.capacite
            FROM chambres c
            LEFT JOIN reservations r ON c.chambre_id = r.chambre_id
            WHERE (r.date_arrivee > :date_depart OR r.date_depart < :date_reservation OR r.date_arrivee IS NULL)
            GROUP BY c.chambre_id
        ");
        $stmt->execute(['date_reservation' => $date_reservation, 'date_depart' => $date_depart]);
        $chambres = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        // Récupérer toutes les chambres si aucune date n'est sélectionnée
        $chambres = $conn->query("SELECT chambre_id, numero, capacite FROM chambres")->fetchAll(PDO::FETCH_ASSOC);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $client_id = $_POST['client_id'];
        $date_reservation = $_POST['date_reservation'];
        $heure_reservation = $_POST['heure_reservation'];
        $date_depart = $_POST['date_depart'];
        $chambre_id = $_POST['chambre_id'];
        $nombre_personnes = (int) $_POST['nombre_personnes'];

        if (strtotime($date_depart) < strtotime($date_reservation)) {
            echo "Erreur : la date de départ ne peut pas être antérieure à la date de réservation.";
            exit;
        }

        // Vérifier la capacité de la chambre
        $stmt = $conn->prepare("SELECT capacite FROM chambres WHERE chambre_id = ?");
        $stmt->execute([$chambre_id]);
        $chambre = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$chambre) {
            echo "Erreur : la chambre sélectionnée n'existe pas.";
            exit;
        }

        if ($nombre_personnes > $chambre['capacite']) {
            echo "Erreur : cette chambre peut accueillir au maximum {$chambre['capacite']} personnes.";
            exit;
        }

        // Insertion
        $stmt = $conn->prepare("INSERT INTO reservations (client_id, date_reservation, heure_reservation, chambre_id, date_depart, nombre_personnes)
                                VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$client_id, $date_reservation, $heure_reservation, $chambre_id, $date_depart, $nombre_personnes]);

        header("Location: listReservations.php");
        exit;
    }

    closeDatabaseConnection($conn);

} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter une Réservation</title>
    <!-- Lien vers Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Lien vers Font Awesome pour les icônes -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
</head>

<body>
    <?php include '../assets/navbar.php'; ?>

    <div class="container mt-5">
        <h1 class="mb-4">Ajouter une Réservation</h1>
        <form method="post">
            <!-- Champ Client -->
            <div class="row mb-3 align-items-center">
                <label for="client_id" class="col-2 col-form-label text-end">Client</label>
                <div class="col-4">
                    <select id="client_id" name="client_id" class="form-control" required>
                        <?php foreach($clients as $client): ?>
                            <option value="<?= htmlspecialchars($client['client_id']) ?>">
                                <?= htmlspecialchars($client['nom_complet']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <!-- Champ Date de Réservation -->
            <div class="row mb-3 align-items-center">
                <label for="date_reservation" class="col-2 col-form-label text-end">Date de Réservation</label>
                <div class="col-4">
                    <input type="date" id="date_reservation" name="date_reservation" class="form-control" required>
                </div>
            </div>
            <!-- Champ Date de Départ -->
            <div class="row mb-3 align-items-center">
                <label for="date_depart" class="col-2 col-form-label text-end">Date de Départ</label>
                <div class="col-4">
                    <input type="date" id="date_depart" name="date_depart" class="form-control" required>
                </div>
            </div>
            <!-- Champ Heure de Réservation -->
            <div class="row mb-3 align-items-center">
                <label for="heure_reservation" class="col-2 col-form-label text-end">Heure de Réservation</label>
                <div class="col-4">
                    <input type="time" id="heure_reservation" name="heure_reservation" class="form-control" required>
                </div>
            </div>
            <!-- Champ Nombre de Personnes -->
            <div class="row mb-3 align-items-center">
                <label for="nombre_personnes" class="col-2 col-form-label text-end">Nombre de Personnes</label>
                <div class="col-4">
                    <input type="number" id="nombre_personnes" name="nombre_personnes" class="form-control" min="1" required>
                </div>
            </div>
            <!-- Champ Choisir une Chambre -->
            <div class="row mb-3 align-items-center">
                <label for="chambre_id" class="col-2 col-form-label text-end">Choisir une Chambre</label>
                <div class="col-4">
                    <select id="chambre_id" name="chambre_id" class="form-control" required>
                        <?php foreach ($chambres as $chambre): ?>
                            <option value="<?= htmlspecialchars($chambre['chambre_id']) ?>">
                                Chambre <?= htmlspecialchars($chambre['numero']) ?> (max <?= $chambre['capacite'] ?> pers)
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
                        <i class="fas fa-check"></i> Réserver
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
