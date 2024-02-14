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
            <a onclick="toEditPage('<?php echo $bezeichnung ?>')"><span style="color: #232527;" data-feather="edit-2"></span></a> <!--Bearbeiten-->
        </div>
        <div class="space-between">
            <p class="subtitle"><?php echo $material['kategorie']; ?></p>
            <p class="subtitle"><span data-feather="map-pin"></span>MK - WR</p>
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

        <div class="infoHolder">
            <div class="disp-text">
                <p class="text-container"><?php echo $material['bezeichnung']; ?></p>
                <p class="subname">Bezeichnung</p>
            </div>
            <div class="grid-2">
                <div class="disp-text">
                    <p class="text-container center"><?php echo $material['anzahl']; ?></p>
                    <p class="subname">Anzahl</p>
                </div>
                <div class="disp-text">
                    <p class="text-container"><?php echo $material['lagerort']; ?></p>
                    <p class="subname">Lagerort</p>
                </div>
            </div>
            <div class="grid-2">
                <div class="disp-text">
                    <p class="text-container"><?php echo $material['kategorie']; ?></p>
                    <p class="subname">Kategorie</p>
                </div>
                <div class="disp-text">
                    <p class="text-container center"><?php echo $material['anschaffung']; ?></p>
                    <p class="subname">Anschaffung</p>
                </div>
            </div>
            <div class="grid-2">
                <div class="disp-text">
                    <a href="<?php echo $material['einkauf']; ?>" style="text-decoration: none; color: #232527;">
                        <p class="text-container"><?php echo $material['einkaufText']; ?></p>
                        <p class="subname">Einkauf<span data-feather="link"></span></p>
                    </a>
                </div>
                <div class="disp-text">
                    <p class="text-container"><?php echo $material['verpackung']; ?></p>
                    <p class="subname">Verpackung</p>
                </div>
            </div>
            <div class="disp-text">
                <p class="text-bem"><?php echo $material['bemerkung']; ?></p>
                <p class="subname">Bemerkungen</p>
            </div>
        </div>
    </main>

    <div class="modal" id="modal" onclick="closeModal()">
        <div class="modal-content">
            <div class="space-between">
                <a onclick="closeModal()"><span data-feather="arrow-left"></span></a>
                <p style="width: 100%; text-align: center;">Bild</p>
            </div>
            <img class="modal-img" src="../../assets/images/uploads/<?php echo $material['bezeichnung']; ?>.jpg">
        </div>
    </div>

    <footer style="grid-template-columns: 1.5fr 1fr 2fr;">
        <a href="bestand.php" class="footer-button_long dark"><span data-feather="arrow-left"></span>Zurück</a>
        <a onclick="openModal()" class="footer-button_long light"><span data-feather="image"></span>Bild</a>
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

function closeModal(){
    var x = document.getElementById('modal');
    x.classList.remove('open');
}

function openModal(){
    var x = document.getElementById('modal');
    x.classList.add('open');
}

function siteBack(){
    window.history.back();
}

function toEditPage(bezeichnung){
    window.location.href = 'bearbeiten.php?bezeichnung=' + encodeURIComponent(bezeichnung);
}


</script>
</html>