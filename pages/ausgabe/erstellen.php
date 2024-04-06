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
    <header>
        <!-- Logo und Benutzer -->
        <a href="../allgemein/dashboard.php"><img id="logo" src="../../assets/icons/logo-small.png"></a>
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
        <h1>Erstellen</h1>
        <p class="subname">Eine neue Aktion erstellen um Material auszugeben.</p>

        <form class="infoHolder" method="post" action="../../assets/php/aktionen/create.php">
            <div class="disp-text">
                <input type="text" id="bezeichnung_input" name="bezeichnung_input" class="text-container" autocomplete="off" required/>
                <p class="subname">Bezeichnung</p>
            </div>
            <div class="grid-2">
                <div class="disp-text">
                    <input type="date" id="beginn_input" name="beginn_input" class="text-container" autocomplete="off" style="font-size: 14px;" required/>
                     <p class="subname">Ausgabe</p>
                </div>
                <div class="disp-text">
                    <input type="date" id="ende_input" name="ende_input" class="text-container" autocomplete="off" style="font-size: 14px;" required/>
                     <p class="subname">Rücknahme</p>
                </div>
            </div>
            <div class="disp-text">
                <input type="text" id="verantwortlicher_input" name="verantwortlicher_input" class="text-container" autocomplete="off" required/>
                <p class="subname">Verantwortliche:r</p>
            </div>
            <p class="subname" style="margin-top:40px;">Material kann nach dem erstellen der Aktion hinzugefügt werden.</p>
    </main>

    <footer>
        <a onclick="siteBack()" class="footer-button_long dark"><span data-feather="arrow-left"></span>Zurück</a>
        <button type="submit" class="footer-button_long" style="font-size:16px;">Speichern</button>
        </form>
    </footer>

    
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

function toAktionspage(id){
    window.location.href = '../ausgabe/ansicht-aktion.php?id=' + encodeURIComponent(id);
}

function searchBestand() {
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
</script>
</html>