<?php
session_start();
require_once 'config.php';

if (isset($_POST['bezeichnung_input']) && isset($_POST['kistenart_input']) && isset($_POST['lagerort_input'])) {

    $bezeichnung = $_POST['bezeichnung_input'];
    $kistenart = (int) $_POST['kistenart_input'];
    $lagerort = $_POST['lagerort_input'];
    $actDate = date("Y.m.d");
    $user = $_SESSION['username'];

    $sql = "INSERT INTO kisten (bezeichnung, icon, lagerort, hinzugefuegtAm, hinzugefuegtVon)
        VALUES ('$bezeichnung', '$kistenart', '$lagerort', '$actDate', '$user')";

    if ($conn->query($sql) === TRUE) {
        $id = $conn->insert_id;
        header("Location: ../../pages/lager/kiste-bearbeiten.php?id=" . urlencode($id));
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
} else {
    echo "Ein oder mehrere Pflichtfelder sind nicht ausgefüllt.";
}

$conn->close();
?>