<?php
require_once 'config.php';

if (isset($_POST['delete_id'])) {
    $id = $_POST['delete_id'];

    // Delete the entry in the material_transportkiste_aktion table
    $sql1 = "DELETE FROM material_transportkiste_aktion WHERE idMaterial =?";
    $stmt1 = $conn->prepare($sql1);
    $stmt1->bind_param("i", $id);

    // Delete the entry in the material table
    $sql2 = "DELETE FROM material WHERE idMaterial =?";
    $stmt2 = $conn->prepare($sql2);
    $stmt2->bind_param("i", $id);

    if ($stmt1->execute() && $stmt2->execute()) {
        header("Location:../../pages/bestand/bestand.php");
    } else {
        echo "Error: ". $sql1. "<br>". $conn->error;
        echo "Error: ". $sql2. "<br>". $conn->error;
    }

    $stmt1->close();
    $stmt2->close();
    $conn->close();
}
?>