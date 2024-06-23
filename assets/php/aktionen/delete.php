<?php 
// Löscht eine Aktion.


require_once '../config.php';

if (isset($_POST['delete_id'])) {
    $aktion = $_POST['delete_id'];

    $conn->begin_transaction();

    $sqlMta = "DELETE FROM material_transportkiste_aktion WHERE idAktion = ?";
    $stmtMta = $conn->prepare($sqlMta);
    if ($stmtMta) {
        $stmtMta->bind_param("i", $aktion);
        if (!$stmtMta->execute()) {
            echo "error: " . $stmtMta->error;
            $conn->rollback();
            exit();
        }
        $stmtMta->close();
    } else {
        echo "error: " . $conn->error;
        $conn->rollback();
        exit();
    }

    $sqlAktion = "DELETE FROM aktionen WHERE idAktion = ?";
    $stmtAktion = $conn->prepare($sqlAktion);
    if ($stmtAktion) {
        $stmtAktion->bind_param("i", $aktion);
        if (!$stmtAktion->execute()) {
            echo "error: " . $stmtAktion->error;
            $conn->rollback();
            exit();
        }
        $stmtAktion->close();
    } else {
        echo "error: " . $conn->error;
        $conn->rollback();
        exit();
    }

    // Transaktion abschließen
    $conn->commit();
    echo "success";
} else {
    echo "invalid_request";
}

$conn->close();
?>
