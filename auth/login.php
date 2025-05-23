<?php
require_once '../config/db_connect.php';
require_once 'authFunctions.php';

session_start();
$error = '';

// Rediriger si déjà connecté
if (isLoggedIn()) {
    header("Location: /resaHotelCalifornia/index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $conn = openDatabaseConnection();

    if (authenticateUser($username, $password, $conn)) {
        // Forcer le rôle admin
        $_SESSION['role'] = 'admin';
        $_SESSION['username'] = $username;

        $encodedMessage = urlencode("SUCCÈS : Bienvenue $username");
        header("Location: /resaHotelCalifornia/index.php?message=$encodedMessage");
        exit;
    } else {
        $encodedMessage = urlencode("ERREUR : Identifiants incorrects ($username)");
        header("Location: /resaHotelCalifornia/index.php?message=$encodedMessage");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Connexion - Hôtel California</title>
    <style>
        * {
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: linear-gradient(to right, #0f2027, #203a43, #2c5364);
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }

        .login-container {
            background: #ffffff;
            padding: 40px 30px;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 420px;
        }

        .login-container h2 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 30px;
            font-size: 28px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #34495e;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 8px;
            background: #ecf0f1;
            transition: background 0.3s, box-shadow 0.3s;
            outline: none;
        }

        input[type="text"]:focus,
        input[type="password"]:focus {
            background: #ffffff;
            box-shadow: 0 0 0 3px rgba(41, 128, 185, 0.3);
        }

        button[type="submit"] {
            width: 100%;
            padding: 12px;
            background: #3498db;
            border: none;
            color: #fff;
            font-size: 16px;
            font-weight: bold;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.3s;
        }

        button[type="submit"]:hover {
            background: #2980b9;
        }

        .error {
            background-color: #ffebee;
            color: #c0392b;
            padding: 10px 15px;
            border-left: 4px solid #e74c3c;
            border-radius: 5px;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <h2>Connexion</h2>
        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="post" action="">
            <div class="form-group">
                <label for="username">Identifiant</label>
                <input type="text" id="username" name="username" placeholder="Entrez votre identifiant" required>
            </div>
            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password" placeholder="Entrez votre mot de passe" required>
            </div>
            <button type="submit">Se connecter</button>
        </form>
    </div>
</body>

</html>