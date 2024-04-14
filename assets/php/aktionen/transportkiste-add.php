<?php
session_start();
require_once 'config.php';

if (isset($_POST['bezeichnung_input']) && isset($_POST['kistenart_input'])) {

    $bezeichnung = $_POST['bezeichnung_input'];
    $kistenart = (int) $_POST['kistenart_input'];

    $sql = "INSERT INTO transportkisten (bezeichnung, icon)
        VALUES ('$bezeichnung', '$kistenart')";

    if ($conn->query($sql) === TRUE) {
        $id = $conn->insert_id;
        header("Location: ../../pages/allgemein/dashboard.php");
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
} else {
    echo "Ein oder mehrere Pflichtfelder sind nicht ausgefüllt.";
}

$conn->close();
?>