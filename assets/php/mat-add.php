<?php
require_once 'config.php';

if (isset($_POST['bezeichnung_input']) && isset($_POST['anzahl_input']) && isset($_POST['lagerort_input']) && 
    isset($_POST['kategorie_input']) && isset($_POST['anschaffung_input']) && isset($_POST['verpackung_input']) && 
    isset($_POST['bemerkung_input'])) {

    $bezeichnung = $_POST['bezeichnung_input'];
    $anzahl = (int) $_POST['anzahl_input'];
    $lagerort = $_POST['lagerort_input'];
    $kategorie = $_POST['kategorie_input'];
    $anschaffung = (int) $_POST['anschaffung_input'];
    $einkauf = isset($_POST['einkauf_input']) ? $_POST['einkauf_input'] : '';
    $einkaufText = $_POST['einkaufText_input'];
    $verpackung = $_POST['verpackung_input'];
    $bemerkung = $_POST['bemerkung_input'];
    $status = 0;

    $sql = "INSERT INTO material (bezeichnung, anzahl, lagerort, kategorie, anschaffung, einkauf, einkaufText, verpackung, bemerkung, status)
            VALUES ('$bezeichnung', '$anzahl', '$lagerort', '$kategorie', '$anschaffung', '$einkauf', '$einkaufText', '$verpackung', '$bemerkung', '$status')";

    if ($conn->query($sql) === TRUE) {
        $id = $conn->insert_id; // Abrufen der "id" des neu eingefügten Datensatzes
        header("Location: ../../pages/bestand/ansicht-material.php?id=" . urlencode($id));
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
} else {
    echo "Ein oder mehrere Pflichtfelder sind nicht ausgefüllt.";
}

$conn->close();
?>