<?php
//Eine neue Lagerkiste erstellen.
session_start();
require_once 'config.php';

if (isset($_POST['bezeichnung_input'], $_POST['kistenart_input'], $_POST['lagerort_input'])) {
    $bezeichnung = $_POST['bezeichnung_input'];
    $kistenart = (int) $_POST['kistenart_input'];
    $lagerort = $_POST['lagerort_input'];
    $actDate = date("Y-m-d");
    $user = $_SESSION['username'];

    $sql = "INSERT INTO kisten (bezeichnung, icon, lagerort, hinzugefuegtAm, hinzugefuegtVon)
        VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("siiss", $bezeichnung, $kistenart, $lagerort, $actDate, $user);
        
        if ($stmt->execute()) {
            $id = $stmt->insert_id;
            header("Location: ../../pages/lager/kiste-bearbeiten.php?id=" . urlencode($id));
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
