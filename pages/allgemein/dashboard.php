<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: ../login/login.php");
    exit;
} elseif (isset($_SESSION['wartungsmodus']) && $_SESSION["wartungsmodus"] == true) {
    header("location: ../login/logout.html");
    exit;
}

require_once '../../assets/php/config.php';

$id = $_SESSION['id'];

$sql = "SELECT * FROM benutzer WHERE idbenutzer = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $users = $result->fetch_assoc();
} else {
    echo "Kein Benutzer mit der Id gefunden.";
}

$stmtDfkt = $conn->prepare("SELECT COUNT(*) FROM material WHERE status = 1");
$stmtDfkt->execute();
$resultDfkt = $stmtDfkt->get_result();
$rowDfkt = $resultDfkt->fetch_row();
$defektCount = $rowDfkt[0];

$stmtIl = $conn->prepare("SELECT COUNT(*) FROM material");
$stmtIl->execute();
$resultIl = $stmtIl->get_result();
$rowIl = $resultIl->fetch_row();
$lagerCount = $rowIl[0];

$stmtVl = $conn->prepare("SELECT COUNT(*) FROM material WHERE status = 3");
$stmtVl->execute();
$resultVl = $stmtVl->get_result();
$rowVl = $resultVl->fetch_row();
$verliehenCount = $rowVl[0];

$stmtAsg = $conn->prepare("SELECT COUNT(*) FROM material WHERE status = 2");
$stmtAsg->execute();
$resultAsg = $stmtAsg->get_result();
$rowAsg = $resultAsg->fetch_row();
$ausgegebenCount = $rowAsg[0];


$conn->close();
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gandalf Material Manager</title>
    <meta name="description" content="Der Gandalf Material Manager ist eine Web-App um das Stammes-Material zu organisieren und zu verwalten.">
    <meta name="author" content="Maurice Peltzer">

    <link rel="icon" type="image/x-icon" href="../../assets/icons/favicon.ico">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lexend">
    <script src="https://unpkg.com/feather-icons"></script>

    <link rel="stylesheet" type="text/css" href="../../assets/css/standard.css">
    <script src="https://cdn.jsdelivr.net/npm/jsqr@1.3.1/dist/jsQR.min.js"></script>
</head>
<body>
<div class="wrapper">
    <header>
        <!-- Logo und Benutzer -->
        <a href="#dashboard"><img id="logo" src="../../assets/icons/logo-small.png"></a>
        <div id="user-header" title="Account">
            <p id="username"><?php echo $_SESSION['username'] ?></p>
            <a onclick="toggleMenu('user-menu')"><img id="user-image" src="../../assets/images/placeholders/Portrait_Placeholder.png"></a>
        </div>
    </header>
    <div id="user-menu">
        <ul>
            <li><a href="profil.php" class="menu-link">Profil</a></li>
            <li><a class="menu-link">Einstellungen</a></li>
            <li>
                <form method="post" action="../../assets/php/users/logout.php">
                    <input type="submit" name="logout" value="Logout">
                </form>
            </li>
        </ul>
    </div>

    <main>
        <h1>Dashboard</h1>
        <div class="infoHolder" style="gap:2px;">
            <p>Willkomen zurück <b><?php echo $users['vorname'] ?></b>,</p>
            <p class="subname">Letzter Login war am <span style="color: #6D8788;"><?php echo date("d.m.Y \u\m H:i", strtotime($users['letzterLogin'])) ?></span>.</p>
            
            <div class="grid-2" style="margin-top:40px;">
                <a class="count-container" style="border-left:8px solid #B51A1A;" href="../bestand/defekt.php">
                    <p style="font-size:40px;"><?php echo $defektCount ?></p>
                    <p>Defekt</p>
                </a>
                <a class="count-container" style="border-left:8px solid #71A462;" href="../bestand/bestand.php">
                    <p style="font-size:40px;"><?php echo $lagerCount ?></p>
                    <p>Im Bestand</p>
                </a>
                <a class="count-container" style="border-left:8px solid #71A462;" href="../bestand/ausgegeben.php">
                    <p style="font-size:40px;"><?php echo $ausgegebenCount ?></p>
                    <p>Ausgeben</p>
                </a>  
                <div class="count-container" style="border-left:8px solid #FFC504;">
                    <p style="font-size:40px;"><?php echo $verliehenCount ?></p>
                    <p>Verliehen</p>
                </div>
            </div>

            <div class="infoHolder">
                <div class="grid-2">
                    <a href="../bestand/bestand.php" class="footer-button_long">Bestand</a>
                    <a href="../lager/lager.php" class="footer-button_long">Lager</a>
                </div>
                <a href="../ausgabe/uebersicht.php" class="footer-button_long" style="width:100%;">Ausgeben</a>
                <a href="#" class="footer-button_long" style="width:100%;">Verleihen</a>
            </div>
        </div>
    </main>

    <div class="modal" id="modal">
		<div class="modal-content">
			<div class="space-between">
				<a onclick="closeModal('modal')"><span data-feather="arrow-left"></span></a>
				<p style="width: 100%; text-align: center;">QR-Code scannen</p>
			</div>
            <video id="qrVideo" width="100%" style="aspect-ratio: 1/1;" hidden></video>
			<div class="space-between" style="margin-top: 16px;">
				<a onClick="closeModal('modal')" class="footer-button_long dark"><span data-feather="arrow-left"></span>Zurück</a>
                <p id="qrContent">...</p>
			</div>
		</div>
	</div>

    <div class="modal" id="add-menu" onclick="closeModal('add-menu')">
        <div class="modal-menu">
            <ul>
                <li><a href="../bestand/erstellen.php" class="menu-link">Material</a></li>
                <li><a href="../lager/kiste-erstellen.php" class="menu-link">Lagerkiste</a></li>
                <li><a href="../allgemein/transportkiste-erstellen.php" class="menu-link">Transportkiste</a></li>
            </ul>
        </div>
    </div>

    <footer style="grid-template-columns: 1.5fr 1fr 2fr;">
        <button onclick="scanCode()" class="footer-button_long"><span data-feather="camera"></span>Scannen</button>
        <a id="add-Btn" onclick="toggleMenu('add-menu')" class="footer-button"><span data-feather="plus"></span></a>
</form>
    </footer>
</div>
</body>

<script>
feather.replace();

function toggleMenu(id){
    var menu = document.getElementById(id);
    if (menu.classList.contains('open')){
        menu.classList.remove('open');
    } else {
        menu.classList.add('open');
    }
}

function siteBack(){
    window.history.back(); 
}

function openModal(){
    var x = document.getElementById('modal');
    x.classList.add('open');
}

function toLagerPage(id){
    window.location.href = '../lager/ansicht-kiste.php?id=' + encodeURIComponent(id);
}

function scanCode() {
openModal();
const video = document.getElementById('qrVideo');
navigator.getUserMedia = navigator.getUserMedia || navigator.webkitGetUserMedia || navigator.mozGetUserMedia;

if (navigator.getUserMedia) {
    navigator.getUserMedia({ video: { facingMode: 'environment' } },
        function(stream) {
            video.srcObject = stream;
            video.onloadedmetadata = function() {
                video.play();
                video.removeAttribute('hidden');
                scanQR(stream);
            };
        },
        function(err) {
            console.error('Error accessing the camera: ', err);
            alert('Die Kamera konnte nicht geöffnet werden. Ist eine Kamera vorhanden?');
           closeModal('modal');
        }
    );
} else {
    console.error('getUserMedia is not supported in this browser.');
    alert('Die Kamera konnte nicht geöffnet werden.%0A Stelle sicher, dass die Verbindung verschlüsselt ist. (https://)');
    closeModal('modal');
}
}

function scanQR(stream) {
    const canvas = document.createElement('canvas');
    const context = canvas.getContext('2d');
    const video = document.getElementById('qrVideo');

    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;

    const scanInterval = setInterval(function() {
        context.drawImage(video, 0, 0, canvas.width, canvas.height);
        const imageData = context.getImageData(0, 0, canvas.width, canvas.height);
        const code = jsQR(imageData.data, canvas.width, canvas.height);
        if (code) {
            const qrContent = code.data;
            const kisteId = extractId(qrContent);
            //alert('QR-Code gescannt: ' + qrContent);
            toLagerPage(kisteId);

            if (navigator.vibrate) {
                navigator.vibrate(100);
            }

            clearInterval(scanInterval);
            closeModal('modal');
            if (stream) {
                const tracks = stream.getTracks();
                tracks.forEach(track => track.stop());
            }
        }
    }, 1000);
}

function closeModal(id) {
    var modal = document.getElementById(id);
    modal.classList.remove('open');
    const video = document.getElementById('qrVideo');
    if (video.srcObject) {
        const stream = video.srcObject;
        const tracks = stream.getTracks();
        tracks.forEach(track => track.stop());
        video.srcObject = null;
    }
}
	
function extractId(qrContent){
	const parts = qrContent.split('-');
    const idPart = parts[parts.length - 1];
    const id = idPart.trim();
    return id;
}
</script>
</html>