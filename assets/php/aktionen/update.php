<?php
// Aktualisiert eine Aktion in der Datenbank.
session_start();
require_once '../config.php';

if (isset($_POST['bezeichnung_input'], $_POST['beginn_input'], $_POST['ende_input'], $_POST['verantwortlicher_input'], $_POST['idAktion']) && !empty($_POST['idAktion'])) {
    $bezeichnung = $_POST['bezeichnung_input'];
    $beginn = $_POST['beginn_input'];
    $ende = $_POST['ende_input'];
    $verantwortlicher = $_POST['verantwortlicher_input'];
    $idAktion = $_POST['idAktion'];

    $sql = "UPDATE aktionen SET bezeichnung = ?, beginn = ?, ende = ?, ansprechpartner = ? WHERE idAktion = ?";
    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        $stmt->bind_param("ssssi", $bezeichnung, $beginn, $ende, $verantwortlicher, $idAktion);
        
        if ($stmt->execute()) {
            header("Location: ../../pages/ausgabe/ansicht.php?id=" . urlencode($idAktion));
            exit();
        } else {
            echo "Error: Ein Fehler ist beim Aktualisieren der Aktion aufgetreten.";
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
