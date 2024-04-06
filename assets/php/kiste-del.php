<?php
require_once 'config.php';

if (isset($_POST['delete_id'])) {
    $id = $_POST['delete_id'];

    $sql = "DELETE FROM kisten WHERE idKiste = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: ../../pages/lager/lager.php");
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $stmt->close();
    $conn->close();
}
?>