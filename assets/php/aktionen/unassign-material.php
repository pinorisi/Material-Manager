<?php 
//Entfernt ein Material aus einer Transportkister einer Aktion.
require_once '../config.php';

if(isset($_POST['material_id']) && isset($_POST['aktion_id'])){
    $material = $_POST['material_id'];
    $aktion = $_POST['aktion_id'];

    $sql = "DELETE FROM material_transportkiste_aktion WHERE idMaterial = ? AND idAktion = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $material, $aktion);

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