<?php
// Inclusion du fichier de connexion à la base de données
require_once '../config/db_connect.php';

// Méthode GET : on recherche la chambre demandée
$chambre_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Vérifier si l'ID est valide
if ($chambre_id <= 0) {
    header("Location: listChambres.php");
    exit;
}

$conn = openDatabaseConnection();

// Méthode POST : Traitement du formulaire si soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $numero = $_POST['numero'];
    $capacite = (int)$_POST['capacite'];

    // Validation des données
    $errors = [];

    if (empty($numero)) {
        $errors[] = "Le numéro de chambre est obligatoire.";
    }

    if ($capacite <= 0) {
        $errors[] = "La capacité doit être un nombre positif.";
    }

    // Si pas d'erreurs, mettre à jour les données
    if (empty($errors)) {
        $stmt = $conn->prepare("UPDATE chambres SET numero = ?, capacite = ? WHERE chambre_id = ?");
        $stmt->execute([$numero, $capacite, $chambre_id]);

        // Rediriger vers la liste des chambres
        header("Location: listChambres.php?success=1");
        exit;
    }
} else {
    // Méthode GET : Récupérer les données de la chambre
    $stmt = $conn->prepare("SELECT * FROM chambres WHERE chambre_id = ?");
    $stmt->execute([$chambre_id]);
    $chambre = $stmt->fetch(PDO::FETCH_ASSOC);

    // Si la chambre n'existe pas, rediriger
    if (!$chambre) {
        header("Location: listChambres.php");
        exit;
    }
}

closeDatabaseConnection($conn);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier une Chambre</title>
    <!-- Lien vers Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Lien vers Font Awesome pour les icônes -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
</head>

<body>
    <?php include '../assets/navbar.php'; ?>

    <div class="container mt-5">
        <h1 class="mb-4">Modifier une Chambre</h1>

        <?php if (isset($errors) && !empty($errors)): ?>
            <div class="alert alert-danger" role="alert">
                <?php foreach ($errors as $error): ?>
                    <p><?= $error ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="post">
            <!-- Champ Numéro de Chambre -->
            <div class="row mb-3 align-items-center">
                <label for="numero" class="col-2 col-form-label text-end">Numéro de Chambre</label>
                <div class="col-4">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-hashtag"></i></span>
                        <input type="text" id="numero" name="numero" class="form-control" value="<?= htmlspecialchars($chambre['numero']) ?>" required>
                    </div>
                </div>
            </div>
            <!-- Champ Capacité -->
            <div class="row mb-3 align-items-center">
                <label for="capacite" class="col-2 col-form-label text-end">Capacité</label>
                <div class="col-4">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-users"></i></span>
                        <input type="number" id="capacite" name="capacite" class="form-control" value="<?= $chambre['capacite'] ?>" min="1" required>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-2 text-end">
                    <!-- Bouton Annuler -->
                    <a href="listChambres.php" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Annuler
                    </a>
                </div>
                <div class="col-4 text-end">
                    <!-- Bouton Enregistrer -->
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-check"></i> Enregistrer les modifications
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
