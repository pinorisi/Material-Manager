<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: ../login/login.php");
    exit;
}

require_once '../../assets/php/config.php';

$id = $_SESSION['id'];

$sql = "SELECT * FROM users WHERE id = ?";
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

$stmtIl = $conn->prepare("SELECT COUNT(*) FROM material WHERE status = 0");
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gandalf Material Manager</title>
    <meta name="description" content="Der Gandalf Material Manager ist eine Web-App um das Stammes-Material zu organisieren und zu verwalten.">
    <meta name="author" content="Maurice Peltzer">

    <link rel="icon" type="image/x-icon" href="../../assets/icons/favicon.ico">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lexend">
    <script src="https://unpkg.com/feather-icons"></script>

    <link rel="stylesheet" type="text/css" href="../../assets/css/standard.css">
</head>
<body>
    <header>
        <!-- Logo und Benutzer -->
        <a href="#dashboard"><img id="logo" src="../../assets/icons/logo-small.png"></a>
        <div id="user-header" title="Account">
            <p id="username"><?php echo $_SESSION['username'] ?></p>
            <a onclick="toggleMenu()"><img id="user-image" src="../../assets/images/placeholders/Portrait_Placeholder.png"></a>
        </div>
    </header>
    <div id="user-menu">
        <ul>
            <li><a class="menu-link">Profil</a></li>
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
            <p>Willkomen zurück <b><?php echo $users['first_name'] ?></b>,</p>
            <p class="subname">Letzter Login war <span style="color: #6D8788;"><?php echo $users['last-login'] ?></span>.</p>
            
            <div class="grid-2" style="margin-top:40px;">
                <div class="count-container" style="border-left:8px solid #B51A1A;">
                    <p style="font-size:40px;"><?php echo $defektCount ?></p>
                    <p>Defekt</p>
                </div>
                <div class="count-container" style="border-left:8px solid #71A462;">
                    <p style="font-size:40px;"><?php echo $lagerCount ?></p>
                    <p>Im Lager</p>
                </div>
                <div class="count-container" style="border-left:8px solid #FFC504;">
                    <p style="font-size:40px;"><?php echo $verliehenCount ?></p>
                    <p>Verliehen</p>
                </div>
                <div class="count-container" style="border-left:8px solid #6D8788;">
                    <p style="font-size:40px;"><?php echo $ausgegebenCount ?></p>
                    <p>Ausgegeben</p>
                </div>
            </div>

            <div class="infoHolder">
                <div class="grid-2">
                    <a href="../bestand/bestand.php" class="footer-button_long">Bestand</a>
                    <a href="../lager/kisten.php" class="footer-button_long">Lager</a>
                </div>
                <a href="#" class="footer-button_long" style="width:100%;">Ausgeben</a>
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
            <video id="qrVideo" width="100%" style="aspect-ratio: 1/1;"></video>
			<div class="space-between" style="margin-top: 16px;">
				<a onClick="closeModal('modal')" class="footer-button_long dark"><span data-feather="arrow-left"></span>Zurück</a>
                <p id="qrContent">...</p>
			</div>
		</div>
	</div>

    <footer style="grid-template-columns: 1.5fr 1fr 2fr;">
        <button id="scanBtn" class="footer-button_long"><span data-feather="camera"></span>Scannen</button>
        <a class="footer-button"><span data-feather="plus"></span></a>
</form>
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

function siteBack(){
    window.history.back(); 
}

function closeModal(modal){ 
    var x = document.getElementById(modal); 
    x.classList.remove('open');  
    stopScanning(); // Deaktiviere die Kamera
}

let scanBtn =document.getElementById('scanBtn');
let qrVideo = document.getElementById('qrVideo');
let qrContent =document.getElementById('qrContent');

function startScanning(){
    if(navigator.mediaDevices && navigator.mediaDevices.getUserMedia){
        navigator.mediaDevices.getUserMedia({video: {facingMode: 'environment'} })
        .then(function(stream){
            qrVideo.srcObject = stream;
            qrVideo.setAttribute('playsinline', true);
            qrVideo.play();
            scanQRCode();
        })
        .catch(function(error){
            console.log("Fehler bei zugriff auf Kamera: ", error);
        });
    }else{
        console.log('Die getUserMedia API wird in diesem Browser/auf diesem Gerät nicht unterstützt.');
    }
}

function stopScanning(){
    qrVideo.srcObject = null;
    qrVideo.load();
}

function scanQRCode(){
    qrVideo.pause();
    let canvas = document.createElement('canvas');
    canvas.width = qrVideo.videoWidth;
    canvas.height =qrVideo.videoHeigth;
    canvas.getContext('2d').drawImage(qrVideo, 0, 0, canvas.width, canvas.height);
    let imageData = canvas.getContext('2d').getImageData(0, 0, canvas.width, canvas.heigth);
    let code = jsQR(imageData.data, imageData.width, imageData.heigth);
    if(code){
        console.log('QR-Code entschlüsselt: ', code.data);
        qrContent.innerText = code.data;
        qrContent.hidden = false;
        stopScanning(); // Deaktiviere die Kamera
    }else{
        setTimeout(scanQRCode, 100);
    }
    qrVideo.play();
}

scanBtn.addEventListener('click', function() {
    document.getElementById('modal').classList.add('open');
    startScanning(); // Aktiviere die Kamera
});
</script>
</html>