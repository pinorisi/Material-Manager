<?php
// Fügt ein Material einer Aktion und Transportkiste hinzu.
session_start();
require_once '../config.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: ../login/login.php");
    exit;
}

$material = $_POST['material_id'];
$aktion = $_POST['aktion_id'];
$transportkiste = $_POST['transportkiste_id'];

    $sql = "INSERT INTO material_transportkiste_aktion (idMaterial, idTransportkiste, idAktion)
        VALUES ('$material', '$transportkiste', '$aktion')";

    if ($conn->query($sql) === TRUE) {
        $id = $conn->insert_id;
        //header("Location: ../../pages/ausgabe/bearbeiten.php?id=" . urlencode($id));
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

$conn->close();

?>