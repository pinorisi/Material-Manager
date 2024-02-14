<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "materialmanager";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Verbindung fehlgeschlagen: " . $conn->connect_error);
}

?>