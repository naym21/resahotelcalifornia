<?php
require_once '../config/db_connect.php';

// Fonction pour formater les dates
function formatDate($date) {
    $timestamp = strtotime($date);
    return $timestamp ? date('d/m/Y', $timestamp) : 'Date invalide';
}

// Récupération des réservations avec les informations des clients et des chambres
try {
    $conn = openDatabaseConnection();
    $stmt = $conn->prepare("
        SELECT r.id, r.date_reservation, r.date_depart, r.nombre_personnes,
               c.nom AS client_nom, c.telephone AS client_telephone, c.email AS client_email,
               ch.numero AS chambre_numero, ch.capacite AS chambre_capacite
        FROM reservations r
        JOIN clients c ON r.client_id = c.client_id
        JOIN chambres ch ON r.chambre_id = ch.chambre_id
        ORDER BY r.date_reservation DESC
    ");
    $stmt->execute();
    $reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Erreur de base de données : " . htmlspecialchars($e->getMessage()));
} finally {
    closeDatabaseConnection($conn);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Liste des Réservations</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Lien vers la feuille de style externe -->
    <link rel="stylesheet" href="../assets/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet">
    <style>
        .error { color: red; padding: 10px; margin: 10px 0; }
        .success { color: green; padding: 10px; margin: 10px 0; }
        .status-past { color: #dc3545; }
        .status-active { color: #28a745; }
    </style>
</head>
<body>
    <?php include '../assets/navbar.php'; ?>

    <?php if(isset($_GET['error'])): ?>
        <div class="error"><?= htmlspecialchars($_GET['error']) ?></div>
    <?php endif; ?>

    <div class="container">
        <h1>Liste des Réservations</h1>

        <div class="actions mb-3">
            <a href="createReservation.php" class="btn btn-success">Nouvelle Réservation</a>
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Client</th>
                    <th>Contact</th>
                    <th>Chambre</th>
                    <th>Personnes</th>
                    <th>Arrivée</th>
                    <th>Départ</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($reservations as $reservation): ?>
                    <?php
                        $aujourd_hui = date('Y-m-d');
                        $statut = '';

                        if ($reservation['date_depart'] < $aujourd_hui) {
                            $statut_class = 'status-past';
                            $statut = 'Terminée';
                        } elseif ($reservation['date_reservation'] <= $aujourd_hui && $reservation['date_depart'] >= $aujourd_hui) {
                            $statut_class = 'status-active';
                            $statut = 'En cours';
                        } else {
                            $statut_class = '';
                            $statut = 'À venir';
                        }
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($reservation['id']) ?></td>
                        <td><?= htmlspecialchars($reservation['client_nom']) ?></td>
                        <td>
                            <strong>Tél:</strong> <?= htmlspecialchars($reservation['client_telephone']) ?><br>
                            <strong>Email:</strong> <?= htmlspecialchars($reservation['client_email']) ?>
                        </td>
                        <td>N° <?= htmlspecialchars($reservation['chambre_numero']) ?> (<?= $reservation['chambre_capacite'] ?> pers.)</td>
                        <td><?= htmlspecialchars($reservation['nombre_personnes']) ?></td>
                        <td><?= formatDate($reservation['date_reservation']) ?></td>
                        <td><?= formatDate($reservation['date_depart']) ?></td>
                        <td class="<?= $statut_class ?>"><?= $statut ?></td>
                        <td>
                            <a href="viewReservation.php?id=<?= htmlspecialchars($reservation['id']) ?>"><i class="fas fa-eye"></i></a>
                            <a href="editReservation.php?id=<?= htmlspecialchars($reservation['id']) ?>"><i class="fas fa-pen"></i></a>
                            <a href="deleteReservation.php?id=<?= htmlspecialchars($reservation['id']) ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette réservation?');"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
