<?php
session_start();
require_once '../../assets/php/config.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: ../login/login.php");
    exit;
}

$sql = "UPDATE material SET idkiste = ? WHERE idMaterial = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $_POST['box_id'], $_POST['material_id']);
$stmt->execute();

echo "Material erfolgreich hinzugefügt.";

$conn->close();

?>