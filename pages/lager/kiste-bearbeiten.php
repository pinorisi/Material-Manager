<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: ../login/login.php");
    exit;
}

$id = $_GET['id'] ?? '';

require_once '../../assets/php/config.php';

$sql = "SELECT * FROM lager WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $material = $result->fetch_assoc();
} else {
    echo "No material found with the given bezeichnung.";
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <link rel="stylesheet" type="text/css" href="../../assets/css/standard.css">
</head>
<body>
    <header>
        <a href="#dashboard"><img id="logo" src="../../assets/icons/logo-small.png"></a>
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
            <h1><?php echo $material['bezeichnung']; ?></h1>
            <a onclick="openDel()"><span style="color: #232527;" data-feather="trash"></span></a>
        </div>
        <div class="space-between">
            <p class="subtitle">Lagerkiste</p>
            <p class="subtitle"><span data-feather="map-pin"></span><?php echo $material['lagerort']; ?></p>
        </div>
        <div class="space-between" style="margin-top: 12px;">
            <div>
                <div class="status-container <?php if($material['status'] == '3')echo 'show'; ?>" id="status-verliehen" style="background-color: #A6B9CD;">
                    <span data-feather="briefcase"></span>
                    <p class="subtitle">Verliehen</p>
                </div>
                <div class="status-container <?php if($material['status'] == '1')echo 'show'; ?>" id="status-defekt" style="background-color: #B2000D;">
                    <span style="color: white;" data-feather="tool"></span>
                    <p class="subtitle" style="color: white;">Defekt</p>
                </div>
                <div class="status-container <?php if($material['status'] == '2')echo 'show'; ?>" id="status-ausgegeben" style="background-color: #6D8788;">
                    <span style="color: white;" data-feather="chevrons-right"></span>
                    <p style="color: white;" class="subtitle">Ausgegeben</p>
                </div>
            </div>
            <p><?php echo $material['id']; ?></p>
        </div>

        <form class="infoHolder" method="post">
            <div class="disp-text">
                <input type="text" name="bezeichnung_input" class="text-container" value="<?php echo $material['bezeichnung']; ?>" autocomplete="off" required/>
                <p class="subname">Bezeichnung</p>
            </div>
            <div class="grid-2">
                <div class="disp-text">
                    <select class="text-container" name="icon_input" required>
                        <option <?php if($material['icon'] == '1')echo 'selected'; ?> value="1">Eurobox 32cm</option>
                        <option <?php if($material['icon'] == '2')echo 'selected'; ?> value="2">Eurobox 17cm</option>
                        <option <?php if($material['icon'] == '3')echo 'selected'; ?> value="3">Gitterbox 32cm</option>
                        <option <?php if($material['icon'] == '4')echo 'selected'; ?> value="4">Gitterbox 17cm</option>
                        <option <?php if($material['icon'] == '5')echo 'selected'; ?> value="5">Karton</option>
                        <option <?php if($material['icon'] == '0')echo 'selected'; ?> value="0">Sonstiges</option>
                    </select>
                    <p class="subname">Kistenart</p>
                </div>
                <div class="disp-text">
                    <input type="text" name="lagerort_input" class="text-container" value="<?php echo $material['lagerort']; ?>" autocomplete="off" required/>
                    <p class="subname">Lagerort</p>
                </div>
            </div>
        </form>
            <h3 style="margin-top: 16px;">Inhalt</h3>
            <ul class="bestand-list" style="margin-top:0; height:55%;">
            <?php
                require_once('../../assets/php/config.php');

                $sql = "SELECT bezeichnung, id, anzahl, status FROM material WHERE kiste = $id";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        switch ($row["status"]) {
                            case "1":
                                $statusIcon = 'data-feather="tool"';
                                break;
                            case "2":
                                $statusIcon = 'data-feather="chevrons-right"';
                                break;
                            case "3":
                                $statusIcon = 'data-feather="briefcase"';
                                break;
                            default:
                                $statusIcon = "";
                        }
                        echo '<li class="space-between">
                                <p>' . $row["bezeichnung"] . '</p>
                                <div style="display: flex;flex-direction: row;gap: 8px;align-items: center;">
                                    <p style="text-align: center;">' . $row["anzahl"] . '</p><div class="vertical-line"></div><a class="delMatBtn" onclick="delMat()"><span style="margin-top:1px;" data-feather="x"></span></a>
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
				<p style="width: 100%; text-align: center;">Bild Aufnehmen</p>
			</div>
			<video autoplay="" class="modal-img" id="imagePrev" width="100%" height="100%"></video>
			<div class="space-between" style="margin-top: 16px;">
				<a onClick="closeModal('modal')" class="footer-button_long dark"><span data-feather="arrow-left"></span>Zurück</a>
				<a id="takePhoto" class="footer-button_long">Speichern</a>
			</div>
		</div>
	</div>

    <div class="modal" id="delModal">
		<div class="modal-content">
			<div class="space-between">
				<a onclick="closeModal('delModal')"><span data-feather="arrow-left"></span></a>
				<p style="width: 100%; text-align: center; font-weight: 600;">Löschen</p>
			</div>
			<p>Möchtest du wirklich den Material-Eintrag aus der Datenbank löschen? Der Eintrag kann nicht wiederhergestellt werden.</p>
			<div class="space-between" style="margin-top: 16px;">
                <a id="delBtn" onclick="deleteMaterial(<?php echo $material['id']; ?>)" class="footer-button_long" style="background-color:#9B3535;">Löschen</a>
				<a onClick="closeModal('delModal')" class="footer-button_long light">Abbrechen</a>
			</div>
		</div>
	</div>

    <footer style="grid-template-columns: 1.5fr 1fr 2fr;">
        <!-- Aktionsknöpfe -->
        <a onclick="siteBack()" class="footer-button_long dark"><span data-feather="arrow-left"></span>Zurück</a>
        <button type="submit" class="footer-button_long" style="font-size:16px;">Speichern</button>
        <a onclick="openModal(material)" class="footer-button"><span data-feather="plus"></span></a>
    </footer>
</body>

<script>
feather.replace();
	
let camera = null;
let stream = null;

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

function openDel(){
    document.getElementById('delModal').classList.add('open');
}

function deleteMaterial(id) {
        $.ajax({
            url: "../../assets/php/mat-del.php",
            type: "POST",
            data: { delete_id: id },
            success: function() {
                window.location.href="bestand.php"
            }
        });
}

function closeModal(modal){ 
    var x = document.getElementById(modal); 
    x.classList.remove('open'); 
    stopCamera(); 
}

function openModal(){ 
    var x = document.getElementById('modal'); 
    x.classList.add('open'); 
    startCamera(); 
}

async function startCamera(){ 
    try { camera = await navigator.mediaDevices.getUserMedia({ video: { facingMode: "environment" } }); 
        stream = new MediaStream(); 
        for (const track of camera.getVideoTracks()) {
             stream.addTrack(track); 
        } 
        const video = document.getElementById("imagePrev"); 
        video.srcObject = stream; 
    } 
    catch (error) { console.error("Error starting camera:", error); } 
}

function stopCamera(){ 
    if (stream){ 
        stream.getTracks().forEach(track => track.stop()); 
        stream = null; 
    }

    if (camera){ 
        camera.getTracks().forEach(track => track.stop()); 
        camera = null; 
    } 
}

document.getElementById("takePhoto").addEventListener("click", async () => { 
    const video = document.getElementById("imagePrev"); 
    const canvas = document.createElement("canvas"); 
    canvas.width = video.videoWidth; 
    canvas.height = video.videoHeight; 
    canvas.getContext("2d").drawImage(video, 0, 0, canvas.width, canvas.height); 
    const imageData = canvas.toDataURL("image/png"); 
    const a = document.createElement("a"); 
    a.href = imageData; 
    a.download = "<?php echo $material['id']; ?>.png"; 
    a.click(); 
    closeModal(); 
});

</script>
</html>