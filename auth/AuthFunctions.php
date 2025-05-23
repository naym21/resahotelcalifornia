<?php

function initSession()
{
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
}

function isLoggedIn()
{
    initSession();
    return isset($_SESSION['user_id']);
}

function hasRole($required_role)
{
    initSession();

    if (!isLoggedIn()) return false;

    $role_levels = [
        'admin' => 1,
        'directeur' => 2,
        'manager' => 3,
        '-reserve-' => 4,
        'standard' => 5,
        'interimaire' => 7,
        'client' => 8
    ];

    $required_level = $role_levels[$required_role] ?? 10;
    $user_role = $_SESSION['role'] ?? '';
    $user_level = $role_levels[$user_role] ?? 10;

    return $user_level <= $required_level;
}

function authenticateUser($username, $password, $conn)
{
    try {
        $query = "SELECT id, username, password, role FROM employes WHERE username = ? AND password = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$username, $password]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            initSession();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            return true;
        }

        return false;
    } catch (PDOException $e) {
        error_log("Erreur d'authentification : " . $e->getMessage());
        return false;
    }
}


function logoutUser()
{
    initSession();
    $_SESSION = [];
    session_destroy();

    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }
}

function requireRole($role = null)
{
    initSession();

    if (!isLoggedIn()) {
        header("Location: /Employe/connexionEmploye.php");
        exit;
    }

    if ($role !== null && !hasRole($role)) {
        $encodedMessage = urlencode("ERREUR : Accès refusé.");
        header("Location: /index.php?message=$encodedMessage");
        exit;
    }
}
