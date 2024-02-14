<?php
require_once 'config.php';

// Werte aus dem Formular abrufen
if (isset($_POST['id'])) {
    $id = $_POST['id'];
} else {
    die("Fehler: Die ID wurde nicht gefunden.");
}
$bezeichnung = $_POST['bezeichnung_input'];
$anzahl = $_POST['anzahl_input'];
$kategorie = $_POST['kategorie_input'];
$lagerort = $_POST['lagerort_input'];
$anschaffung = $_POST['anschaffung_input'];
$einkaufText = $_POST['einkaufText_input'];
$verpackung = $_POST['verpackung_input'];
$bemerkung = $_POST['bemerkung_input'];
$status = isset($_POST['status_input']) ? true : false;

// Status-Parameter festlegen
$status_param = ($status == true) ? 1 : 0;

// Aktualisierung in der Datenbank durchführen
$sql = "UPDATE material SET bezeichnung=?, anzahl=?, kategorie=?, lagerort=?, anschaffung=?, einkaufText=?, verpackung=?, bemerkung=?, status=? WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("siiissssi", $bezeichnung, $anzahl, $kategorie, $lagerort, $anschaffung, $einkaufText, $verpackung, $bemerkung, $status_param, $id);
$stmt->execute();

// Anzahl der betroffenen Zeilen bestimmen
$numAffectedRows = $stmt->affected_rows;

// Ressourcen freigeben
$stmt->close();
$conn->close();

// Zurück zum Material-Bestand leiten (das kannst du entsprechend deiner Web-App anpassen)
header("Location: bestand.php");
exit();
?>