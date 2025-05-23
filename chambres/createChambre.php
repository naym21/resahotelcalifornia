<?php
require_once '../config/db_connect.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $numero = $_POST['numero'];
    $capacite = $_POST['capacite'];

    $conn = openDatabaseConnection();
    $stmt = $conn->prepare("INSERT INTO chambres (numero, capacite) VALUES (?, ?)");
    $stmt->execute([$numero, $capacite]);
    closeDatabaseConnection($conn);

    header("Location: listChambres.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter une Chambre</title>
    <!-- Ajouter Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Ajouter Font Awesome pour les icônes -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
</head>

<body>
<?php include_once '../assets/gestionMessage.php'; ?>
<?php include '../assets/navbar.php'; ?>

    <div class="container mt-5">
        <h1 class="mb-4">Ajouter une Chambre</h1>
        <form method="post">
            <!-- Champ Numéro -->
            <div class="row mb-3 align-items-center">
                <label for="numero" class="col-2 col-form-label text-end">Numéro</label>
                <div class="col-4">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-hashtag"></i></span>
                        <input type="text" id="numero" name="numero" class="form-control" placeholder="Entrez le numéro" required>
                    </div>
                </div>
            </div>
            <!-- Champ Capacité -->
            <div class="row mb-3 align-items-center">
                <label for="capacite" class="col-2 col-form-label text-end">Capacité</label>
                <div class="col-4">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-users"></i></span>
                        <input type="number" id="capacite" name="capacite" class="form-control" placeholder="Entrez la capacité" required>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-2 text-end">
                    <!-- Bouton de retour -->
                    <a href="listChambres.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Retour
                    </a>
                </div>
                <div class="col-4 text-end">
                    <!-- Bouton validation -->
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-check"></i> Valider
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
