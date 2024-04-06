<?php 
require_once 'config.php';

if(isset($_POST['id'])){
    $matId = $_POST['id'];

    $sql = "UPDATE material SET idkiste = NULL WHERE idMaterial = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $matId);

    if($stmt->execute()) {
        echo "success";
    } else {
        echo "error";
    }
} else {
    echo "invalid_request";
}

$conn->close();
?>