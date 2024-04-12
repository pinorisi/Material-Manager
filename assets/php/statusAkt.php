<?php
require_once 'config.php';

// Aktuelle Zeit abrufen
$currentDate = date("Y.m.d");

// SQL-Abfrage, um aktive Aktionen zu finden
$sql = "SELECT idAktion, beginn, ende FROM aktionen WHERE beginn <= '$currentDate' AND ende >= '$currentDate'";
$result = $conn->query($sql);
echo $result->num_rows;

// Wenn eine aktive Aktion gefunden wurde
if ($result->num_rows > 0) {
    // Durch jede gefundene Aktion iterieren
    while($row = $result->fetch_assoc()) {
        $aktion_id = $row["idAktion"];
        
        // SQL-Abfrage, um Material zu aktualisieren
        $update_sql = "UPDATE material 
                       INNER JOIN material_transportkiste_aktion 
                       ON material.idMaterial = material_transportkiste_aktion.idMaterial
                       SET material.status = 1 
                       WHERE material_transportkiste_aktion.idAktion = $aktion_id";
        if ($conn->query($update_sql) === TRUE) {
            echo "Material erfolgreich aktualisiert für Aktion: " . $aktion_id . "\n";
        } else {
            echo "Fehler beim Aktualisieren des Materials: " . $conn->error . "\n";
        }
    }
} else {
    echo "Keine aktiven Aktionen gefunden.\n";
}

// Verbindung schließen
$conn->close();
?>
