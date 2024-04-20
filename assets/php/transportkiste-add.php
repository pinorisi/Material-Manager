<?php
//Fügt eine Transportkiste der Datenbank hinzu.
session_start();
require_once 'config.php';

if (isset($_POST['bezeichnung_input'], $_POST['kistenart_input'])) {
    $bezeichnung = $_POST['bezeichnung_input'];
    $kistenart = (int) $_POST['kistenart_input'];

    $sql = "INSERT INTO transportkisten (bezeichnung, icon) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("si", $bezeichnung, $kistenart);
        
        if ($stmt->execute()) {
            $id = $stmt->insert_id;
            header("Location: ../../pages/allgemein/dashboard.php");
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
