<?php
//Fügt eine Transportkiste einer Aktion hinzu.
session_start();
require_once '../config.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: ../login/login.php");
    exit;
}

if(isset($_POST['transportkiste_id'], $_POST['aktion_id'])){
    $transportkiste = $_POST['transportkiste_id'];
    $aktion = $_POST['aktion_id'];

    $stmt = $conn->prepare("INSERT INTO material_transportkiste_aktion (idTransportkiste, idAktion) VALUES (?, ?)");

    if ($stmt) {
        $stmt->bind_param("ii", $transportkiste, $aktion);

        if ($stmt->execute()) {
            $id = $stmt->insert_id;
            //header("Location: ../../pages/ausgabe/bearbeiten.php?id=" . urlencode($id));
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
