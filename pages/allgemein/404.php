<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: ../login/login.php");
    exit;
} elseif (isset($_SESSION['wartungsmodus']) && $_SESSION["wartungsmodus"] == true) {
    header("location: ../login/wartungsarbeiten.html");
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
        <a href="dashboard.php"><img id="logo" src="../../assets/icons/logo-small.png"></a>
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
        <h1>404 Server Error</h1>
        <p class="subname">Not Found</p>
        <p style="margin-top: 20px;">Die Seite die du suchst existiert entweder nicht oder wurde woanders hin verschoben.<br><br>Was du tun kannst:</p>
    </main>

    <footer style="grid-template-columns: 1.5fr 1fr 2fr;">
        <ul style="list-style:none; margin-bottom:60px; gap:8px; display:flex; flex-direction:column; width:100%; justify-content:center;">
            <li><a onclick="pageRefresh()" class="footer-button_long"><span data-feather="refresh-ccw"></span>Seite Neuladen</a></li>
            <li><a onclick="siteBack()" class="footer-button_long"><span data-feather="arrow-left"></span>Zur vorherigen Seite</a></li>
            <li><a href="dashboard.php" class="footer-button_long"><span data-feather="home"></span>Zur vorherigen Seite</a></li>
        </ul>
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

function pageRefresh(){
    location.reload();
}

function siteBack(){
    window.history.back(); 
}


</script>
</html>