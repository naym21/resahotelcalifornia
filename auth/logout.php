<?php
require_once 'authFunctions.php'; // ✅ Corrigé

initSession();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    logoutUser();
    error_log("Déconnexion réussie");

    $encodedMessage = urlencode("SUCCÈS : Vous êtes maintenant déconnecté.");
    header("Location: ../index.php?message=$encodedMessage"); // ✅ Remontée d’un dossier
    exit;
} else {
    header("Location: ../index.php");
    exit;
}
