<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: ../login/login.php");
    exit;
} elseif (isset($_SESSION['wartungsmodus']) && $_SESSION["wartungsmodus"] == true) {
    header("location: ../login/logout.html");
    exit;
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
    <script src="https://cdn.jsdelivr.net/npm/jsqr@1.3.1/dist/jsQR.min.js"></script>
</head>
<body>
<div class="wrapper">
    <header>
        <a href="../allgemein/dashboard.php"><img id="logo" src="../../assets/icons/logo-small.png"></a>
        <div id="user-header" title="Account">
            <p id="username"><?php echo $_SESSION['username'] ?></p>
            <a onclick="toggleMenu()"><img id="user-image" src="../../assets/images/placeholders/Portrait_Placeholder.png"></a>
        </div>
    </header>
    <div id="user-menu">
        <ul>
            <li><a href="../allgemein/profil.php" class="menu-link">Profil</a></li>
            <li><a class="menu-link">Einstellungen</a></li>
            <li>
                <form method="post" action="../../assets/php/users/logout.php">
                    <input type="submit" name="logout" value="Logout">
                </form>
            </li>
        </ul>
    </div>

    <main>
        <h1>Lager</h1>
        <form class="search-container">
            <input type="search" id="search-bar" placeholder="Suchen..." onkeyup="searchLager()">
            <button type="button"><span data-feather="search"></span></button>
        </form>
        <p class="subname" style="margin-top: 2px;" id="resultCount">0 Kisten gefunden</p>
    
        <ul class="bestand-list">
        <?php
            require_once('../../assets/php/config.php');
        
            $sql = "SELECT bezeichnung, idKiste, icon, lagerort FROM kisten";
            $result = $conn->query($sql);
        
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    switch ($row["icon"]) {
                        case "1":
                            $chestIcon = "../../assets/icons/half_box.svg";
                            break;
                        case "2":
                            $chestIcon = "../../assets/icons/full_box.svg";
                            break;
                        case "3":
                            $chestIcon = "../../assets/icons/grid_box.svg";
                            break;
                        case "4":
                            $chestIcon = "../../assets/icons/half_grid_box.svg";
                            break;
                        default:
                            $chestIcon = "../../assets/icons/half_box.svg";
                    }
                    echo '<li class="space-between blli" onclick="toLagerPage(\'' . $row["idKiste"] . '\')">
                            <p>' . $row["bezeichnung"] . '</p>
                            <div style="display: flex;flex-direction: row;gap: 8px;align-items: center;">
                                <span style="width: 14px; height: 14px;"></span><div class="vertical-line"></div><img src="' . $chestIcon . '" style="height: 16px; aspect-ratio: 1/1;">
                            </div>
                        </li>';
                }
            } else {
                echo "<li>Keine Daten gefunden</li>";
            }
        
            $conn->close();
            ?>
        </ul>
    </main>

    <div class="modal" id="modal">
		<div class="modal-content">
			<div class="space-between">
				<a onclick="closeModal('modal')"><span data-feather="arrow-left"></span></a>
				<p style="width: 100%; text-align: center;">QR-Code scannen</p>
			</div>
			<video autoplay="" class="modal-img" id="imagePrev" width="100%" height="100%"></video>
			<div class="space-between" style="margin-top: 16px;">
				<a onClick="closeModal('modal')" class="footer-button_long dark"><span data-feather="arrow-left"></span>Zurück</a>
			</div>
		</div>
	</div>

    <footer style="grid-template-columns: 1.5fr 1fr 2fr;">
        <a href="../allgemein/dashboard.php" class="footer-button_long dark"><span data-feather="arrow-left"></span>Zurück</a>
        <button onclick="scanCode()" class="footer-button_long"><span data-feather="camera"></span>Scannen</button>
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



function siteBack(){
    window.history.back(); 
}

function closeModal(){
    var x = document.getElementById('modal');
    x.classList.remove('open');
}

function openModal(){
    var x = document.getElementById('modal');
    x.classList.add('open');
}

function pageRefresh(){
    location.reload();
}

function toLagerPage(id){
    window.location.href = 'ansicht-kiste.php?id=' + encodeURIComponent(id);
}

function searchLager() {
    var input, filter, ul, li, txtValue, searchResults;
    input = document.getElementById('search-bar');
    filter = input.value.toUpperCase().replace(/\s+/g, '');
    li = document.getElementsByClassName("blli");
    searchResults = 0;

    for (var i = 0; i < li.length; i++){
        li[i].style.display = 'none';
        txtValue = li[i].textContent || li[i].innerText;
        if(txtValue.toUpperCase().indexOf(filter) > -1){
            li[i].style.display = "flex"
            searchResults++;
        }
    }
    document.getElementById('resultCount').textContent = (searchResults + " Ergebnisse gefunden");
}

function scanCode() {
    openModal();

    const video = document.getElementById('imagePrev');
    navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } })
        .then(function(stream) {
            video.srcObject = stream;
            video.play();
            video.addEventListener('canplay', function() {
                scanQR(stream);
            });
        })
        .catch(function(err) {
            console.error('Error accessing the camera: ', err);
            alert('Die Kamer konnte nicht geöffnet werden.');
            closeModal('modal');
        });
}

function scanQR(stream) {
    const canvas = document.createElement('canvas');
    const context = canvas.getContext('2d');
    const video = document.getElementById('imagePrev');

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
    const video = document.getElementById('imagePrev');
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