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

$sql = "SELECT * FROM kisten WHERE idKiste = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $kiste = $result->fetch_assoc();

    $id = $kiste['idKiste'];
} else {
    echo "Keine Kiste gefunden.";
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
<div class="wrapper">
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
            <h1><?php echo $kiste['bezeichnung']; ?></h1>
            <a onclick="toEditPage('<?php echo $id ?>')"><span style="color: #232527;" data-feather="edit-2"></span></a> <!--Bearbeiten-->
        </div>
        <div class="space-between">
            <p class="subtitle">Lagerkiste</p>
            <p class="subtitle"><span data-feather="map-pin"></span><?php echo $kiste['lagerort']; ?></p>
        </div>

        <div class="space-between" style="margin-top: 12px;">
            <div>
            </div>
            <p><?php echo $kiste['idKiste']; ?></p>
        </div>

        <ul class="bestand-list">
        <?php
            require_once('../../assets/php/config.php');
        
            $sql = "SELECT bezeichnung, idMaterial, anzahl, status FROM material WHERE idKiste = $id";
            $result = $conn->query($sql);
        
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<li class="space-between blli" onclick="toMaterialPage(\'' . $row["idMaterial"] . '\')">
                            <p>' . $row["bezeichnung"] . '</p>
                            <div style="display: flex;flex-direction: row;gap: 8px;align-items: center;">
                                <span style="width: 14px; height: 14px;"></span><div class="vertical-line"></div><p style="text-align: center;">' . $row["anzahl"] . '</p>
                            </div>
                        </li>';
                }
            } else {
                echo "<li>Die Kiste ist leer.</li>";
            }
        
            $conn->close();
            ?>
        </ul>

    </main>

    <div class="modal" id="modal" onclick="closeModal('modal')">
        <div class="modal-content">
            <div class="space-between">
                <a onclick="closeModal('modal')"><span data-feather="arrow-left"></span></a>
                <p style="width: 100%; text-align: center;">Info</p>
            </div>
            <div class="grid-2" style="margin-top: 16px;">
                <div class="disp-text">
                    <p class="text-container center"><?php echo $kiste['hinzugefuegtAm']; ?></p>
                    <p class="subname">Hinzugefügt am</p>
                </div>
                <div class="disp-text">
                    <p class="text-container"><?php echo $kiste['hinzugefuegtVon']; ?></p>
                    <p class="subname">Von</p>
                </div>
            </div>
            <div class="grid-2" style="margin-top: 16px;">
                <div class="disp-text">
                    <p class="text-container center"><?php if($kiste['icon'] === NULL){echo '/';} else {echo $kiste['icon'];} ?></p>
                    <p class="subname">Id Transportkiste</p>
                </div>
            </div>
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
        <a href="lager.php" class="footer-button_long dark"><span data-feather="arrow-left"></span>Zurück</a>
        <div style="display:flex; flex-direction: row; gap: 16px;">
        <a onclick="openModal('modal')" class="footer-button_long light"><span data-feather="info"></span></a>
        <a onclick="generateQrCode('Stamm Gandalf Material Manager - Gas & Licht - <?php echo $id ?>')" class="footer-button"><span data-feather="grid"></span></a>
        </div>
    </footer>
</div>
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
    window.location.href = 'kiste-bearbeiten.php?id=' + encodeURIComponent(id);
}

let canvas;

function generateQrCode(inhalt) {
    openModal('qr-modal');
    const qrCodeElement = document.getElementById("qrcode");
    if (!qrCodeElement.firstChild || qrCodeElement.firstChild.src === "") {
        const qrCode = new QRCode(qrCodeElement, {
            width: 250,
            height: 250,
            colorDark: "#000000",
            colorLight: "#ffffff",
            correctLevel: QRCode.CorrectLevel.H
        });
        qrCode.makeCode(inhalt);

        canvas = document.createElement("canvas");
        canvas.width = qrCode.width;
        canvas.height = qrCode.height;
        const ctx = canvas.getContext("2d");
        for (let y = 0; y < qrCode.height; y++) {
            for (let x = 0; x < qrCode.width; x++) {
                ctx.fillStyle = qrCode.modules[y][x] ? "#000000" : "#ffffff";
                ctx.fillRect(x, y, 1, 1);
            }
        }
    }
}

function downloadCode(name){
    const dataUrl = canvas.toDataURL("image/png");
    const a = document.createElement("a");
    a.href = dataUrl;
    a.download = "QR_<?php echo $kiste['bezeichnung'] ?>.png";
    a.click();
}

</script>
</html>