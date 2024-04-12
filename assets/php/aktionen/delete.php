<?php 
//Entfernt eine Transportkiste aus einer Aktion.
require_once '../config.php';

if(isset($_POST['delete_id'])){
    $aktion = $_POST['delete_id'];

    $sql = "DELETE mta, a
        FROM material_transportkiste_aktion AS mta
        INNER JOIN aktionen AS a ON mta.idAktion = a.idAktion
        WHERE a.idAktion = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $aktion);

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