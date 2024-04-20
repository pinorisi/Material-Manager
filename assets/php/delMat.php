<?php 
//Entfernt das Material aus einer Lagerkiste
require_once 'config.php';

if(isset($_POST['id']) && !empty($_POST['id'])){
    $matId = $_POST['id'];

    $sql = "UPDATE material SET idkiste = NULL WHERE idMaterial = ?";
    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        $stmt->bind_param("i", $matId);
        
        if($stmt->execute()) {
            echo "success";
        } else {
            echo "error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "error: Vorbereiten des SQL-Statements fehlgeschlagen.";
    }
} else {
    echo "invalid_request: Nicht alle erforderlichen Daten übermittelt.";
}

$conn->close();
?>
