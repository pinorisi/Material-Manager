<?php 
//Entfernt eine Transportkiste (mit ihrem Inhalt) aus einer Aktion.
require_once '../config.php';

if(isset($_POST['transportkiste_id']) && isset($_POST['aktion_id'])){
    $transportkiste = $_POST['transportkiste_id'];
    $aktion = $_POST['aktion_id'];

    $sql = "DELETE FROM material_transportkiste_aktion WHERE idTransportkiste = ? AND idAktion = ?";
    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        $stmt->bind_param("ii", $transportkiste, $aktion);
        
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
