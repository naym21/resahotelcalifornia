<?php
require_once '../config/db_connect.php';

$reservation_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Vérifier si l'ID est valide
if ($reservation_id <= 0) {
    header("Location: listReservations.php");
    exit;
}

$conn = openDatabaseConnection();

// Vérifier si la réservation existe
$stmt = $conn->prepare("SELECT r.*, c.nom, c.prenom FROM reservations r JOIN clients c ON r.client_id = c.client_id WHERE r.id = ?");
$stmt->execute([$reservation_id]);
$reservation = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$reservation) {
    header("Location: listReservations.php");
    exit;
}

// Traitement de la suppression si confirmée
if (isset($_POST['confirm']) && $_POST['confirm'] === 'yes') {
    $stmt = $conn->prepare("DELETE FROM reservations WHERE id = ?");
    $stmt->execute([$reservation_id]);

    header("Location: listReservations.php?deleted=1");
    exit;
}

closeDatabaseConnection($conn);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <title>Supprimer une Réservation</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet">
    <style>
        .warning-box {
            background-color: #fff3cd;
            color: #856404;
            padding: 15px;
            margin-bottom: 20px;
            border-left: 5px solid #ffeeba;
        }

        .danger-box {
            background-color: #f8d7da;
            color: #721c24;
            padding: 15px;
            margin-bottom: 20px;
            border-left: 5px solid #f5c6cb;
        }

        .form-check {
            margin: 10px 0;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Supprimer une Réservation</h1>

        <div class="warning-box">
            <p><strong>Attention :</strong> Vous êtes sur le point de supprimer une réservation faite par <strong><?= htmlspecialchars($reservation['nom']) ?> <?= htmlspecialchars($reservation['prenom']) ?></strong>.</p>
        </div>

        <form method="post">
            <p>Êtes-vous sûr de vouloir supprimer cette réservation ?</p>

            <div class="actions">
                <input type="hidden" name="confirm" value="yes">
                <button type="submit" class="btn btn-danger">Confirmer la suppression</button>
                <a href="listReservations.php" class="btn btn-primary">Annuler</a>
            </div>
        </form>
    </div>
</body>

</html>