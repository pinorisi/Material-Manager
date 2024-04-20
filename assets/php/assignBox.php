<?php
//Material zu einer Lagerkiste hinzufügen.
session_start();
require_once '../../assets/php/config.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: ../login/login.php");
    exit;
}

if (isset($_POST['box_id'], $_POST['material_id']) && !empty($_POST['box_id']) && !empty($_POST['material_id'])) {
    $box_id = $_POST['box_id'];
    $material_id = $_POST['material_id'];

    $sql = "UPDATE material SET idkiste = ? WHERE idMaterial = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("ii", $box_id, $material_id);

        if ($stmt->execute()) {
            echo "Material erfolgreich hinzugefügt.";
        } else {
            echo "Error: Ein Fehler ist beim Aktualisieren des Materials aufgetreten.";
        }

        $stmt->close();
    } else {
        echo "Error: Ein Fehler ist beim Vorbereiten des SQL-Statements aufgetreten.";
    }
} else {
    echo "Error: Nicht alle erforderlichen Daten übermittelt.";
}

$conn->close();
?>
