<?php
//Fügt ein loses Material einer Aktion/Ausgabe hinzu.
session_start();
require_once '../config.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: ../login/login.php");
    exit;
}

if(isset($_POST['material_id'], $_POST['aktion_id'])){
    $material = $_POST['material_id'];
    $aktion = $_POST['aktion_id'];

    $stmt = $conn->prepare("INSERT INTO material_transportkiste_aktion (idMaterial, idAktion) VALUES (?, ?)");

    if ($stmt) {
        $stmt->bind_param("ii", $material, $aktion);

        if ($stmt->execute()) {
            $id = $stmt->insert_id;
        } else {
            echo "Ein Fehler ist aufgetreten. Bitte versuchen Sie es später erneut.";
        }

        $stmt->close();
    } else {
        echo "Ein Fehler ist aufgetreten. Bitte versuchen Sie es später erneut.";
    }
} else {
    echo "Nicht alle erforderlichen Daten übermittelt.";
}

$conn->close();
?>
