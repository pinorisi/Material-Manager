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

$sql = "SELECT * FROM material WHERE idMaterial = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $material = $result->fetch_assoc();
    $id = $material['idMaterial'];
    $anzahl = $material['anzahl'];
} else {
    echo "Kein Material mit der ID gefunden";
}

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
            <h1><?php echo $material['bezeichnung']; ?></h1>
            <a onclick="toEditPage('<?php echo $id ?>')"><span style="color: #232527;" data-feather="edit-2"></span></a> <!--Bearbeiten-->
        </div>
        <div class="space-between">
            <p class="subtitle"><?php echo $material['kategorie']; ?></p>
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
            <p><?php echo $material['idMaterial']; ?></p>
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
                    <p class="text-container center"><?php if($material['idKiste'] === NULL){echo '/';} else {echo $material['idKiste'];} ?></p>
                    <p class="subname">Nr. Lagerkiste</p>
                </div>
            </div>
            <div class="grid-2">
                <div class="disp-text">
                    <p class="text-container center"><?php echo $material['hinzugefuegtAm']; ?></p>
                    <p class="subname">Hinzugefügt am</p>
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
                <p style="width: 100%; text-align: center;">Info</p>
            </div>
            <div class="grid-2" style="margin-top: 16px;">
                <div class="disp-text">
                    <p class="text-container center"><?php echo $material['hinzugefuegtAm']; ?></p>
                    <p class="subname">Hinzugefügt am</p>
                </div>
                <div class="disp-text">
                    <p class="text-container"><?php echo $material['hinzugefuegtVon']; ?></p>
                    <p class="subname">Von</p>
                </div>
            </div>
            <div class="grid-2" style="margin-top: 16px;">
                <div class="disp-text">
                    <p class="text-container center"><?php if($material['transportkisteId'] === NULL){echo '/';} else {echo $material['transportkisteId'];} ?></p>
                    <p class="subname">Id Transportkiste</p>
                </div>
                <div class="disp-text" style="margin-bottom: 16px;">
                    <p class="text-container center"><?php if($material['idKiste'] === NULL){echo '/';} else {echo $material['idKiste'];} ?></p>
                    <p class="subname">Id Kiste</p>
                </div>
            </div>
        </div>
    </div>

    <footer style="grid-template-columns: 1.5fr 1fr 2fr;">
        <a onclick="siteBack()" class="footer-button_long dark"><span data-feather="arrow-left"></span>Zurück</a>
        <a onclick="openModal()" class="footer-button_long light"><span data-feather="info"></span></a>
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

function toEditPage(id){
    window.location.href = 'bearbeiten.php?id=' + encodeURIComponent(id);
}


</script>
</html>