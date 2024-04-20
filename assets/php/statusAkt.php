<?php
//Aktualisiert den Status von Materialien - Für Cronjob.
require_once 'config.php';

date_default_timezone_set('Deine_Zeitzone');

$currentDate = date("Y-m-d");

$sql = "UPDATE material 
        INNER JOIN material_transportkiste_aktion 
        ON material.idMaterial = material_transportkiste_aktion.idMaterial
        INNER JOIN aktionen 
        ON material_transportkiste_aktion.idAktion = aktionen.idAktion
        SET material.status = 1 
        WHERE aktionen.beginn <= ? AND aktionen.ende >= ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $currentDate, $currentDate);
if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo "Materialien erfolgreich aktualisiert für aktive Aktionen.\n";
    } else {
        echo "Keine Materialien gefunden für aktive Aktionen.\n";
    }
} else {
    error_log("Fehler beim Aktualisieren der Materialien: " . $stmt->error);
}

$stmt->close();
$conn->close();
?>
