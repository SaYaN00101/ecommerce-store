<?php
/**
 * Admin session guard.
 * Include at the top of every admin sub-page (included or standalone).
 * Redirects to login if no admin session is active.
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['admin_name'])) {
    header("Location: admin_login.php");
    exit();
}
