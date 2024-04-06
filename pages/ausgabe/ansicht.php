<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: ../login/login.php");
    exit;
} elseif (isset($_SESSION['wartungsmodus']) && $_SESSION["wartungsmodus"] == true) {
    header("location: ../login/logout.html");
    exit;
}

$id = $_GET['id'] ?? '';

require_once '../../assets/php/config.php';

$sql = "SELECT * FROM aktionen WHERE idAktion = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $aktion = $result->fetch_assoc();

    $id = $aktion['idAktion'];
} else {
    echo "Keine Aktion gefunden.";
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gandalf Material Manager</title>
    <meta name="description" content="Der Gandalf Material Manager ist eine Web-App um das Stammes-Material zu organisieren und zu verwalten.">
    <meta name="author" content="Maurice Peltzer">

    <link rel="icon" type="image/x-icon" href="../../assets/icons/favicon.ico">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lexend">
    <script src="https://unpkg.com/feather-icons"></script>

    <link rel="stylesheet" type="text/css" href="../../assets/css/standard.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
</head>
<body>
    <header>
        <a href="../allgemein/dashboard.php"><img id="logo" src="../../assets/icons/logo-small.png"></a>
        <div id="user-header">
            <p id="username">Benutzername</p>
            <a onclick="toggleMenu()"><img id="user-image" src="../../assets/images/placeholders/Portrait_Placeholder.png"></a>
        </div>
    </header>
    <div id="user-menu">
        <ul>
            <li><a class="menu-link">Profil</a></li>
            <li><a class="menu-link">Einstellungen</a></li>
            <li><a class="menu-link">Abmelden</a></li>
        </ul>
    </div>

    <main>
        <div class="space-between">
            <h1><?php echo $aktion['bezeichnung']; ?></h1>
            <a onclick="toEditPage('<?php echo $id ?>')"><span style="color: #232527;" data-feather="edit-2"></span></a> <!--Bearbeiten-->
        </div>
        <div class="space-between">
            <p class="subtitle"><?php echo date("d.m.Y", strtotime($aktion['beginn'])) . ' - ' . date("d.m.Y", strtotime($aktion['ende'])) ?></p>
        </div>

        <div class="grid-2" style="margin-top:40px;">
            <div class="disp-text">
                <p class="text-container center"><?php echo date("d.m.Y", strtotime($aktion['beginn'])); ?></p>
                <p class="subname">Ausgabe</p>
            </div>
            <div class="disp-text">
                <p class="text-container center"><?php echo date("d.m.Y", strtotime($aktion['ende'])); ?></p>
                <p class="subname">Rückgabe</p>
            </div>
        </div>
        <div class="disp-text" style="margin-top:16px;">
                <p class="text-container"><?php echo $aktion['ansprechpartner']; ?></p>
                <p class="subname">Verantwortliche:r</p>
            </div>

        <h3 style="margin-top: 16px;">Material</h3>
        <ul class="bestand-list">
        <?php
        require_once('../../assets/php/config.php');
    
        $sql = "SELECT tk.idTransportkiste, tk.bezeichnung AS Transportkiste, m.bezeichnung AS Material
                FROM transportkisten tk
                JOIN material_transportkiste_aktion mta ON tk.idTransportkiste = mta.idTransportkiste
                JOIN material m ON mta.idMaterial = m.idMaterial
                JOIN aktionen a ON mta.idAktion = a.idAktion
                WHERE a.idAktion = $id
                GROUP BY tk.idTransportkiste";
        $result = $conn->query($sql);
    
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<li style='height:auto; background-color:white;' class='listDetail'><details>
                        <summary>
                            <p style='width:80%'>$row[Transportkiste]</p>
                            <img style='margin-right:8px;align-items:center;' src='../../assets/icons/full_box.svg'/>
                        </summary>
                        <ul>";
                        $sql2 = "SELECT m.bezeichnung AS Material, m.anzahl AS Anzahl, m.idMaterial AS MaterialId
                                 FROM material_transportkiste_aktion mta
                                 JOIN material m ON mta.idMaterial = m.idMaterial
                                 WHERE mta.idTransportkiste = $row[idTransportkiste]";
                            $result2 = $conn->query($sql2);
                            if ($result2->num_rows > 0) {
                                
                                while ($row2 = $result2->fetch_assoc()) {
                                    echo "<li class='subname space-between' style='padding-right:13px;' onclick='toMaterialPage($row2[MaterialId])'><p>$row2[Material]</p><p>$row2[Anzahl]</p></li>";
                                }
                            }
                echo "</ul>
                      </details></li>";
            }
        } else {
            echo "<li style='height:auto;'><p class='subtitle'>Der Aktion wurde kein Material zugeordnet</p></li>";
        }
    
        $conn->close();
    ?>
        </ul>

    </main>

    <div class="modal" id="img-modal" onclick="closeModal('img-modal')">
        <div class="modal-content">
            <div class="space-between">
                <a onclick="closeModal('img-modal')"><span data-feather="arrow-left"></span></a>
                <p style="width: 100%; text-align: center;">Bild</p>
            </div>
            <!--<img class="modal-img" src="../../assets/images/uploads/<?php echo $aktion['bezeichnung']; ?>.jpg">-->
        </div>
    </div>

    <div class="modal" id="qr-modal">
		<div class="modal-content">
			<div class="space-between">
				<a onclick="closeModal('qr-modal')"><span data-feather="arrow-left"></span></a>
				<p style="width: 100%; text-align: center;">QR-Code</p>
			</div>
			<div id="qrcode"></div>
			<div class="space-between" style="margin-top: 16px;">
				<a onClick="closeModal('qr-modal')" class="footer-button_long dark"><span data-feather="arrow-left"></span>Zurück</a>
				<a onclick="downloadCode()" class="footer-button_long" download>Herunterladen</a>
			</div>
		</div>
	</div>

    <footer style="grid-template-columns: 2fr 1fr;">
        <a href="uebersicht.php" class="footer-button_long dark"><span data-feather="arrow-left"></span>Zurück</a>
    </footer>
</body>

<script>
feather.replace();

function toggleMenu(){
    var menu = document.getElementById('user-menu');
    if (menu.classList.contains('open')){
        menu.classList.remove('open');
    } else {
        menu.classList.add('open');
    }
}

function closeModal(modal){
    var x = document.getElementById(modal);
    x.classList.remove('open');
}

function openModal(modal){
    var x = document.getElementById(modal);
    x.classList.add('open');
}

function siteBack(){
    window.history.back();
}

function toMaterialPage(id){
    window.location.href = '../bestand/ansicht-material.php?id=' + encodeURIComponent(id);
}

function toEditPage(id){
    window.location.href = 'bearbeiten.php?id=' + encodeURIComponent(id);
}
</script>
</html>