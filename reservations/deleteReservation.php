<?php
require_once '../config/db_connect.php';

$reservation_id = $_GET['id'] ?? null;

if (!$reservation_id) {
    die("ID de réservation non spécifié.");
}

try {
    $conn = openDatabaseConnection();

    // Supprimer la réservation
    $stmt = $conn->prepare("DELETE FROM reservations WHERE id = ?");
    $stmt->execute([$reservation_id]);

    closeDatabaseConnection($conn);

    // Rediriger après la suppression
    header("Location: listReservations.php");
    exit;

} catch (PDOException $e) {
    die("Erreur de connexion ou d'exécution de la requête : " . $e->getMessage());
}
?>
