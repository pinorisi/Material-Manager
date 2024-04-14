<?php
require_once 'config.php';

$currentDate = date("Y.m.d");

$sql = "SELECT idAktion, beginn, ende FROM aktionen WHERE beginn <= '$currentDate' AND ende >= '$currentDate'";
$result = $conn->query($sql);
echo $result->num_rows;

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $aktion_id = $row["idAktion"];
        
        $update_sql = "UPDATE material 
                       INNER JOIN material_transportkiste_aktion 
                       ON material.idMaterial = material_transportkiste_aktion.idMaterial
                       SET material.status = 1 
                       WHERE material_transportkiste_aktion.idAktion = $aktion_id";
        if ($conn->query($update_sql) === TRUE) {
            echo "Material erfolgreich aktualisiert fuer Aktion: " . $aktion_id . "\n";
        } else {
            echo "Fehler beim Aktualisieren des Materials: " . $conn->error . "\n";
        }
    }
} else {
    echo "Keine aktiven Aktionen gefunden.\n";
}

$conn->close();
?>
