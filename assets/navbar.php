<?php
require_once '../auth/authFunctions.php';
initSession();
?>

<style>
    .custom-navbar {
        background: linear-gradient(to right, #203a43, #2c5364);
        padding: 12px 30px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
    }

    .custom-navbar .navbar-brand {
        color: #ffffff;
        font-weight: bold;
        font-size: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .custom-navbar .navbar-nav .nav-link {
        color: #ecf0f1;
        font-weight: 500;
        transition: color 0.3s ease;
        padding: 8px 16px;
        border-radius: 8px;
    }

    .custom-navbar .navbar-nav .nav-link:hover {
        background-color: rgba(255, 255, 255, 0.1);
        color: #ffffff;
    }

    .custom-navbar .btn-logout {
        background-color: #e74c3c;
        color: white;
        font-weight: 500;
        border: none;
        border-radius: 6px;
        padding: 6px 12px;
        transition: background 0.3s;
    }

    .custom-navbar .btn-logout:hover {
        background-color: #c0392b;
    }

    @media (max-width: 992px) {
        .custom-navbar .navbar-nav {
            margin-top: 15px;
        }
    }
</style>

<nav class="navbar navbar-expand-lg custom-navbar">
    <div class="container-fluid">
        <a class="navbar-brand" href="/resaHotelCalifornia/index.php">
            <i class="fas fa-hotel"></i> resaHotel California
        </a>
        <button class="navbar-toggler bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse justify-content-between" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="/resaHotelCalifornia/chambres/listChambres.php">
                        <i class="fas fa-bed"></i> Chambres
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/resaHotelCalifornia/clients/listClients.php">
                        <i class="fas fa-user"></i> Clients
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/resaHotelCalifornia/reservations/listReservations.php">
                        <i class="fas fa-calendar-alt"></i> Réservations
                    </a>
                </li>
            </ul>

            <?php if (isLoggedIn()): ?>
                <form action="/resaHotelCalifornia/auth/logout.php" method="post" class="d-flex">
                    <button type="submit" class="btn btn-logout">
                        <i class="fas fa-sign-out-alt"></i> Déconnexion
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</nav>