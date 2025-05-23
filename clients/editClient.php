<?php
require_once '../config/db_connect.php';

if (!isset($_GET['id'])) {
    header("Location: listClients.php");
    exit;
}

$id = $_GET['id'];
$conn = openDatabaseConnection();
$stmt = $conn->prepare("SELECT * FROM clients WHERE client_id = ?");
$stmt->execute([$id]);
$client = $stmt->fetch();

if (!$client) {
    header("Location: listClients.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $telephone = $_POST['telephone'];

    $stmt = $conn->prepare("UPDATE clients SET nom = ?, prenom = ?, email = ?, telephone = ? WHERE client_id = ?");
    $stmt->execute([$nom, $prenom, $email, $telephone, $id]);

    closeDatabaseConnection($conn);
    header("Location: listClients.php");
    exit;
}

closeDatabaseConnection($conn);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier un Client</title>
    <!-- Lien vers Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Lien vers Font Awesome pour les icônes -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
</head>

<body>
<?php include_once '../assets/gestionMessage.php'; ?>
<?php include '../assets/navbar.php'; ?>

    <div class="container mt-5">
        <h1 class="mb-4">Modifier un Client</h1>
        <form method="post">
            <!-- Champ Nom -->
            <div class="row mb-3 align-items-center">
                <label for="nom" class="col-2 col-form-label text-end">Nom</label>
                <div class="col-4">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                        <input type="text" id="nom" name="nom" class="form-control" value="<?= htmlspecialchars($client['nom']) ?>" required>
                    </div>
                </div>
            </div>
            <!-- Champ Prénom -->
            <div class="row mb-3 align-items-center">
                <label for="prenom" class="col-2 col-form-label text-end">Prénom</label>
                <div class="col-4">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                        <input type="text" id="prenom" name="prenom" class="form-control" value="<?= htmlspecialchars($client['prenom']) ?>" required>
                    </div>
                </div>
            </div>
            <!-- Champ Email -->
            <div class="row mb-3 align-items-center">
                <label for="email" class="col-2 col-form-label text-end">Email</label>
                <div class="col-4">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        <input type="email" id="email" name="email" class="form-control" value="<?= htmlspecialchars($client['email']) ?>" required>
                    </div>
                </div>
            </div>
            <!-- Champ Téléphone -->
            <div class="row mb-3 align-items-center">
                <label for="telephone" class="col-2 col-form-label text-end">Téléphone</label>
                <div class="col-4">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-phone"></i></span>
                        <input type="text" id="telephone" name="telephone" class="form-control" value="<?= htmlspecialchars($client['telephone']) ?>" required>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-2 text-end">
                    <!-- Bouton de retour -->
                    <a href="listClients.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Retour
                    </a>
                </div>
                <div class="col-4 text-end">
                    <!-- Bouton validation -->
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-check"></i> Enregistrer
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
