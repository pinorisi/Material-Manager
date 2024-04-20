<?php
//Fügt ein neues Material zur Datenbank hinzu.
session_start();
require_once 'config.php';

if (isset($_POST['bezeichnung_input'], $_POST['anzahl_input'], $_POST['lagerort_input'], 
          $_POST['kategorie_input'], $_POST['verpackung_input'], $_POST['bemerkung_input'])) {
    
    $bezeichnung = $_POST['bezeichnung_input'];
    $anzahl = (int) $_POST['anzahl_input'];
    $lagerort = $_POST['lagerort_input'];
    $kategorie = $_POST['kategorie_input'];
    $verpackung = $_POST['verpackung_input'];
    $bemerkung = $_POST['bemerkung_input'];
    $status = 0;
    $hinzugefuegt = date('Y-m-d');
    $nutzer = $_SESSION['username'];

    $sql = "INSERT INTO material (bezeichnung, anzahl, lagerort, kategorie, verpackung, bemerkung, status, hinzugefuegtAm, hinzugefuegtVon)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        $stmt->bind_param("sissssis", $bezeichnung, $anzahl, $lagerort, $kategorie, $verpackung, $bemerkung, $status, $hinzugefuegt, $nutzer);
        
        if ($stmt->execute()) {
            $id = $stmt->insert_id;
            header("Location: ../../pages/bestand/ansicht-material.php?id=" . urlencode($id));
            exit();
        } else {
            echo "Error: Ein Fehler ist beim Einfügen der Daten aufgetreten.";
        }

        $stmt->close();
    } else {
        echo "Error: Ein Fehler ist beim Vorbereiten des SQL-Statements aufgetreten.";
    }
} else {
    echo "Ein oder mehrere Pflichtfelder sind nicht ausgefüllt.";
}

$conn->close();
?>
