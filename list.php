<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "materialmanager";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT bezeichnung, anzahl, status FROM material";
$result = $conn->query($sql);
?>

<ul class="bestand-list">
    <?php
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $statusIcon = ($row["status"] == "defekt") ? "tool" : "star";
            echo '<li class="space-between" onclick="toMaterialPage()">
                    <p>' . $row["bezeichnung"] . '</p>
                    <div style="display: flex;flex-direction: row;gap: 8px;align-items: center;">
                        <span style="width: 14px; height: 14px;" data-feather="' . $statusIcon . '"></span>|
                        <p style="text-align: center;">' . $row["anzahl"] . '</p>
                    </div>
                </li>';
        }
    } else {
        echo "<li>Keine Daten gefunden</li>";
    }
    $conn->close();
    ?>

<script src="https://unpkg.com/feather-icons"></script>
    <script>feather.replace();</script>
</ul>