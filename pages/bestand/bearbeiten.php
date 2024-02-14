<?php
$bezeichnung = $_GET['bezeichnung'] ?? '';

// Connect to the database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "materialmanager";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query the database for the information corresponding to the given bezeichnung
$sql = "SELECT * FROM material WHERE bezeichnung = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $bezeichnung);
$stmt->execute();
$result = $stmt->get_result();

// Check if a row was returned
if ($result->num_rows > 0) {
    $material = $result->fetch_assoc();

    // Fill in the information on the page using the $material array
    // For example:
    $bezeichnung = $material['bezeichnung'];
    $anzahl = $material['anzahl'];
    // ... and so on
} else {
    // Handle the case where no row was returned
    echo "No material found with the given bezeichnung.";
}

// Close the database connection
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <link rel="stylesheet" type="text/css" href="../../assets/css/standard.css">
</head>
<body>
    <header>
        <!-- Logo und Benutzer -->
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
        <!-- Inhalt der Seite -->
        <div class="space-between">
            <h1><?php echo $material['bezeichnung']; ?></h1> <!--Bezeichnung-->
            <a onclick="openDel()"><span style="color: #232527;" data-feather="trash"></span></a>
        </div>
        <div class="space-between">
            <p class="subtitle"><?php echo $material['kategorie']; ?></p> <!--Kategorie-->
            <p class="subtitle"><span data-feather="map-pin"></span><?php echo $material['lagerort']; ?></p> <!--Lagerort-->
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

        <form class="infoHolder" method="post" action="../../assets/php/mat-edit.php">
        <input type="hidden" name="id" value="<?php echo $material['id']; ?>">
            <div class="disp-text">
                <input type="text" name="bezeichnung_input" class="text-container" value="<?php echo $material['bezeichnung']; ?>" autocomplete="off" required/>
                <p class="subname">Bezeichnung</p>
            </div>
            <div class="grid-2">
                <div class="disp-text">
                    <input type="number" name="anzahl_input" class="text-container" style="text-decoration: none; color: #232527;" value="<?php echo $material['anzahl']; ?>" autocomplete="off" required/>
                    <p class="subname">Anzahl</p>
                </div>
                <div class="disp-text">
                    <input type="text" name="lagerort_input" class="text-container" value="<?php echo $material['lagerort']; ?>" autocomplete="off" required/>
                    <p class="subname">Lagerort</p>
                </div>
            </div>
            <div class="grid-2">
                <div class="disp-text">
                    <select class="text-container" name="kategorie_input" required>
                        <option <?php if($material['kategorie'] == 'Zeltmaterial')echo 'selected'; ?> value="Zeltmaterial">Zeltmaterial</option>
                        <option <?php if($material['kategorie'] == 'Freizeit')echo 'selected'; ?> value="Freizeit">Freizeit</option>
                        <option <?php if($material['kategorie'] == 'Kuechenmaterial')echo 'selected'; ?> value="Kuechenmaterial">Küchenmaterial</option>
                        <option <?php if($material['kategorie'] == 'Werkzeug')echo 'selected'; ?> value="Werkzeug">Werkzeug</option>
                        <option <?php if($material['kategorie'] == 'Ersatzteil')echo 'selected'; ?> value="Ersatzteil">Ersatzteil</option>
                        <option <?php if($material['kategorie'] == 'Sonstiges')echo 'selected'; ?> value="Sonstiges">Sonstiges</option>
                    </select>
                    <p class="subname">Kategorie</p>
                </div>
                <div class="disp-text">
                    <input type="number" name="anschaffung_input" class="text-container" style="text-decoration: none; color: #232527;" value="<?php echo $material['anschaffung']; ?>" autocomplete="off" required/>
                    <p class="subname">Anschaffung</p>
                </div>
            </div>
            <div class="grid-2">
                <div class="disp-text">                        
                    <input type="text" name="einkaufText_input" class="text-container" value="<?php echo $material['einkaufText']; ?>" autocomplete="off" required/>
                    <p class="subname">Einkauf-Text<span data-feather="link"></span></p>
                </div>
                <div class="disp-text">
                    <select class="text-container" name="verpackung_input" required>
                        <option <?php if($material['verpackung'] == 'Packsack')echo 'selected'; ?> value="Packsack">Packsack</option>
                        <option <?php if($material['verpackung'] == 'Kiste')echo 'selected'; ?> value="Kiste">Kiste</option>
                        <option <?php if($material['verpackung'] == 'Tasche')echo 'selected'; ?> value="Tasche">Tasche</option>
                        <option <?php if($material['verpackung'] == 'Sonstiges')echo 'selected'; ?> value="Sonstiges">Sonstiges</option>
                        <option <?php if($material['verpackung'] == 'Keine')echo 'selected'; ?> value="Keine">Keine</option>
                    </select>
                    <p class="subname">Verpackung</p>
                </div>
            </div>
            <div class="disp-text">
                <textarea contenteditable="true" name="bemerkung_input" maxlength="255" class="text-bem" autocomplete="off"><?php echo $material['bemerkung']; ?></textarea>
                <p class="subname">Bemerkungen</p>
            </div>
            <div style="display: flex;flex-direction: row;">
                <input type="checkbox" name="status_input" class="check" style="margin-right: 8px;" <?php if( $material['status'] == 1) echo 'checked'; ?>>
                <p>Als defekt markieren</p>
            </div>
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
        <a onclick="openModal()" id="getPhoto" class="footer-button light"><span data-feather="camera"></span></a>
</form>
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