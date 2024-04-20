<?php 
//Entfernt ein Material aus einer Transportkiste einer Aktion.
require_once '../config.php';

if(isset($_POST['material_id'], $_POST['aktion_id']) && !empty($_POST['material_id']) && !empty($_POST['aktion_id'])){
    $material = $_POST['material_id'];
    $aktion = $_POST['aktion_id'];

    $sql = "DELETE FROM material_transportkiste_aktion WHERE idMaterial = ? AND idAktion = ?";
    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        $stmt->bind_param("ii", $material, $aktion);
        if($stmt->execute()) {
            echo "success";
        } else {
            echo "error";
        }

        $stmt->close();
    } else {
        echo "error";
    }
} else {
    echo "invalid_request";
}

$conn->close();
?>
