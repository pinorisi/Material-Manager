<?php 
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: ../login/login.php");
    exit;
} elseif (isset($_SESSION['wartungsmodus']) && $_SESSION["wartungsmodus"] == true) {
    header("location: ../allgemein/wartungsmodus.html");
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

    <link rel="icon" type="image/x-icon" href="../../../assets/icons/favicon.ico">
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
        <h1>Hinzufügen</h1>
        <p class="subname">Eine neue Transportkiste hinzufügen.</p>
        <p class="subname" style="margin-top:16px">Eine Transportkiste sollte <b>nicht</b> als Lagerkiste genutzt werden! Sie sollte immer geleert im Mat-Keller zur verfügung stehen.</p>
        

        <form class="infoHolder" method="post" action="../../assets/php/transportkiste-add.php">
            <div class="disp-text">
                <input type="text" id="bezeichnung_input" name="bezeichnung_input" class="text-container" autocomplete="off" required/>
                <p class="subname">Bezeichnung</p>
            </div>
            <div class="disp-text">
                <select class="text-container" name="kistenart_input" required>
                    <option selected disabled hidden>Auswählen...</option>
                    <option value="1">Eurobox 32cm</option>
                    <option value="2">Eurobox 17cm</option>
                    <option value="3">Alutec 93l</option>
                    <option value="4">Alutec 142l</option>
                    <option value="5">Karton</option>
                    <option value="0">Sonstiges</option>
                </select>
                <p class="subname">Kistenart</p>
            </div>
            
    </main>

    <footer style="grid-template-columns: 1.5fr 1fr 2fr;">
        <a onclick="siteBack()" class="footer-button_long dark"><span data-feather="arrow-left"></span>Zurück</a>
        <button type="submit" class="footer-button_long" style="Font-size:16px">Hinzufügen</button>
</form>
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
</script>
</html>