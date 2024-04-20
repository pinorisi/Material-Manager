<?php
//Entfernt ein Material aus der Datenbank.
require_once 'config.php';

if (isset($_POST['delete_id']) && !empty($_POST['delete_id'])) {
    $id = $_POST['delete_id'];

    $conn->begin_transaction();

    try {
        $sql1 = "DELETE FROM material_transportkiste_aktion WHERE idMaterial = ?";
        $stmt1 = $conn->prepare($sql1);
        $stmt1->bind_param("i", $id);
        
        $sql2 = "DELETE FROM material WHERE idMaterial = ?";
        $stmt2 = $conn->prepare($sql2);
        $stmt2->bind_param("i", $id);

        if ($stmt1->execute() && $stmt2->execute()) {
            $conn->commit();
            header("Location: ../../pages/bestand/bestand.php");
            exit();
        } else {
            throw new Exception("Fehler beim Löschen der Einträge.");
        }
    } catch (Exception $e) {
        $conn->rollback();
        echo "Error: " . $e->getMessage();
    }

    $stmt1->close();
    $stmt2->close();
}

$conn->close();
?>
