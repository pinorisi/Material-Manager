<?php 
//Fügt ein Material einer Aktion in einer Transportkiste hinzu.
require_once '../config.php';

if(isset($_POST['idMaterial']) && isset($_POST['idAktion'])){
    $matId = $_POST['idMaterial'];
    $AkId = $_POST['idAktion'];

    $sql = "DELETE FROM material_transportkiste_aktion WHERE idMaterial = ? AND idAktion = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $matId, $AkId);

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