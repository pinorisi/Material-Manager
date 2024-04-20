<?php
//Erstellt eine Aktion für eine Ausgabe.
session_start();
require_once '../config.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: ../login/login.php");
    exit;
}

if (isset($_POST['bezeichnung_input'], $_POST['beginn_input'], $_POST['ende_input'], $_POST['verantwortlicher_input'])) {
    $bezeichnung = $_POST['bezeichnung_input'];
    $beginn = $_POST['beginn_input'];
    $ende = $_POST['ende_input'];
    $verantwortlicher = $_POST['verantwortlicher_input'];
    $ersteller = $_SESSION['username'];
    $date = date("Y-m-d");

    $stmt = $conn->prepare("INSERT INTO aktionen (bezeichnung, beginn, ende, ansprechpartner, ersteller, hinzugefuegtAm) VALUES (?, ?, ?, ?, ?, ?)");

    if ($stmt) {
        $stmt->bind_param("ssssss", $bezeichnung, $beginn, $ende, $verantwortlicher, $ersteller, $date);

        if ($stmt->execute()) {
            $id = $stmt->insert_id;
            header("Location: ../../../pages/ausgabe/bearbeiten.php?id=" . urlencode($id));
            exit;
        } else {
            echo "Ein Fehler ist aufgetreten. Bitte versuchen Sie es später erneut.";
        }

        $stmt->close();
    } else {
        echo "Ein Fehler ist aufgetreten. Bitte versuchen Sie es später erneut.";
    }
} else {
    echo "Nicht alle erforderlichen Daten übermittelt.";
}

$conn->close();
?>
