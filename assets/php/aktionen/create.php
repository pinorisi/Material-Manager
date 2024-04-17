<?php
//Erstellt eine neue Aktion in der Datenbank
session_start();
require_once '../config.php';

if (isset($_POST['bezeichnung_input']) && isset($_POST['beginn_input']) && isset($_POST['ende_input']) && isset($_POST['verantwortlicher_input'])) {

    $bezeichnung = $_POST['bezeichnung_input'];
    $beginn = $_POST['beginn_input'];
    $ende = $_POST['ende_input'];
    $verantwortlicher = $_POST['verantwortlicher_input'];
    $ersteller = $_SESSION['username'];
    $date = date("Y.m.d");

    $sql = "INSERT INTO aktionen (bezeichnung, beginn, ende, ansprechpartner, ersteller, hinzugefuegtAm)
        VALUES ('$bezeichnung', '$beginn', '$ende', '$verantwortlicher', '$ersteller', '$date')";

    if ($conn->query($sql) === TRUE) {
        $id = $conn->insert_id;
        header("Location: ../../../pages/ausgabe/bearbeiten.php?id=" . urlencode($id));
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>