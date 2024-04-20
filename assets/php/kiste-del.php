<?php
//Entfernt eine Lagerkiste aus der Datenbank.
require_once 'config.php';

if (isset($_POST['delete_id']) && !empty($_POST['delete_id'])) {
    $id = $_POST['delete_id'];

    $sql = "DELETE FROM kisten WHERE idKiste = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            header("Location: ../../pages/lager/lager.php");
            exit();
        } else {
            echo "Error: Ein Fehler ist beim Löschen der Daten aufgetreten.";
        }

        $stmt->close();
    } else {
        echo "Error: Ein Fehler ist beim Vorbereiten des SQL-Statements aufgetreten.";
    }
} else {
    echo "Ungültige Anfrage: Löscht ID nicht angegeben oder leer.";
}

$conn->close();
?>
