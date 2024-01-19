<?php
session_start();
if (!isset($_SESSION["id"])) {
    header("Location: login.php");
    exit();
}

session_unset();
session_destroy();

// Redirect to login page with a success parameter
header("Location: /PORTAL_PHP/login.php?logout=success");
?>
