<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: ../login/login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['iduser'])) {
        require_once '../config.php';

        $iduser = intval($_POST['iduser']);
        $stmt = $conn->prepare("DELETE FROM benutzer WHERE idbenutzer = ?");
        $stmt->bind_param("i", $iduser);

        if ($stmt->execute()) {
            echo "Benutzer erfolgreich gelöscht.";
        } else {
            echo "Fehler beim Löschen des Benutzers.";
        }

        $stmt->close();
        $conn->close();
    } else {
        echo "Ungültige Anfrage.";
    }
} else {
    echo "Ungültige Anfrage.";
}
?>
