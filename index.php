<?php
require_once 'auth/authFunctions.php';
initSession();
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord - Hôtel California</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        body {
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #fff;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .dashboard {
            background-color: #ffffff;
            color: #2c3e50;
            padding: 40px 30px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            width: 90%;
            max-width: 600px;
        }

        h1 {
            text-align: center;
            font-size: 28px;
            margin-bottom: 20px;
        }

        .hotel-icon {
            display: block;
            text-align: center;
            font-size: 50px;
            color: #2980b9;
            margin-bottom: 20px;
        }

        .nav-section {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 15px;
            background-color: #ecf0f1;
            padding: 15px;
            border-radius: 10px;
            text-decoration: none;
            color: #2c3e50;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .nav-link:hover {
            background-color: #d0d7de;
            transform: translateY(-2px);
        }

        .nav-link i {
            font-size: 20px;
            color: #3498db;
        }

        .nav-link .badge {
            margin-left: auto;
            background-color: #3498db;
            color: white;
            padding: 5px 12px;
            border-radius: 50px;
            font-size: 14px;
        }

        .top-right {
            position: absolute;
            top: 20px;
            right: 30px;
        }

        .top-right form,
        .top-right a {
            display: inline-block;
        }

        .btn-auth {
            background-color: #e74c3c;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 8px;
            font-size: 14px;
            cursor: pointer;
            transition: background 0.3s ease;
            text-decoration: none;
        }

        .btn-auth:hover {
            background-color: #c0392b;
        }

        .btn-logout {
            background-color: #e74c3c;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 8px;
            font-size: 14px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .btn-logout:hover {
            background-color: #c0392b;
        }

        .alert {
            text-align: center;
            background-color: #3498db;
            color: white;
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 10px;
            font-weight: 500;
        }
    </style>
</head>

<body>
    <div class="top-right">
        <?php if (!isLoggedIn()): ?>
            <a href="auth/login.php" class="btn-auth" style="background-color: #2980b9;">Connexion</a>
        <?php else: ?>
            <form action="/resaHotelCalifornia/auth/logout.php" method="post" class="d-flex">
                <button type="submit" class="btn btn-logout">
                    <i class="fas fa-sign-out-alt"></i> Déconnexion
                </button>
            </form>
        <?php endif; ?>
    </div>

    <div class="dashboard">
        <?php if (isset($_GET['message'])): ?>
            <div class="alert"><?= htmlspecialchars($_GET['message']) ?></div>
        <?php endif; ?>

        <h1>Tableau de bord</h1>
        <div class="hotel-icon"><i class="fas fa-hotel"></i></div>

        <div class="nav-section">
            <a href="chambres/listChambres.php" class="nav-link">
                <i class="fas fa-bed"></i> Gestion des Chambres
                <span class="badge">100+</span>
            </a>
            <a href="clients/listClients.php" class="nav-link">
                <i class="fas fa-users"></i> Gestion des Clients
                <span class="badge">50+</span>
            </a>
            <a href="reservations/listReservations.php" class="nav-link">
                <i class="fas fa-calendar-check"></i> Gestion des Réservations
                <span class="badge">75</span>
            </a>
        </div>
    </div>
</body>

</html>