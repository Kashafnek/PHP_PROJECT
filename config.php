<?php
$server = "localhost";
$username = "root";
$password = "";
$database = "PHP_PORTAL";
$insert = false;

$con = mysqli_connect($server, $username, $password, $database);

if (!$con) {
    die("Connection Failed: " . mysqli_connect_error());
}
?>