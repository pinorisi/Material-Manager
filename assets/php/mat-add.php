<?php
session_start();
require_once 'config.php';

if (isset($_POST['bezeichnung_input']) && isset($_POST['anzahl_input']) && isset($_POST['lagerort_input']) && 
    isset($_POST['kategorie_input']) && isset($_POST['verpackung_input']) && 
    isset($_POST['bemerkung_input'])) {

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
        VALUES ('$bezeichnung', '$anzahl', '$lagerort', '$kategorie', '$verpackung', '$bemerkung', '$status', '$hinzugefuegt', '{$nutzer}')";

    if ($conn->query($sql) === TRUE) {
        $id = $conn->insert_id;
        header("Location: ../../pages/bestand/ansicht-material.php?id=" . urlencode($id));
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
} else {
    echo "Ein oder mehrere Pflichtfelder sind nicht ausgefüllt.";
}

$conn->close();
?>