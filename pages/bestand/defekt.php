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
        <h1>Defekt</h1>
        <form class="search-container">
            <input type="search" id="search-bar" placeholder="Suchen..." onkeyup="searchBestand()">
            <button type="button"><span data-feather="search"></span></button>
        </form>
        <p class="subname" style="margin-top: 2px;" id="resultCount">0 Ergebnisse gefunden</p>
    
        <ul class="bestand-list">
        <?php
            require_once('../../assets/php/config.php');

            $sql = "SELECT bezeichnung, idMaterial, anzahl, status FROM material WHERE status = 1";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $statusIcon = 'data-feather="tool"';
                    echo '<li class="space-between blli" onclick="toMaterialPage(\'' . $row["idMaterial"] . '\')">
                            <p>' . $row["bezeichnung"] . '</p>
                            <div style="display: flex;flex-direction: row;gap: 8px;align-items: center;">
                                <span style="width: 14px; height: 14px;" ' . $statusIcon . '></span><div class="vertical-line"></div><p style="text-align: center;">' . $row["anzahl"] . '</p>
                            </div>
                        </li>';
                }
            } else {
                echo "<li>Keine Einträge gefunden.</li>";
            }
        
            $conn->close();
        ?>
        </ul>
    </main>

    <footer style="grid-template-columns: 1.5fr 1fr 2fr;">
        <a href="../allgemein/dashboard.php" class="footer-button_long dark"><span data-feather="arrow-left"></span>Zurück</a>
        <div style="display:flex;flex-direction:row;gap:16px;">
        <a href="erstellen.php" class="footer-button" title="Hinzufügen"><span style="color: white;" data-feather="plus"></span></a>
        <a onclick="pageRefresh()" class="footer-button light" title="Aktualisieren"><span style="color: #232527;" data-feather="refresh-ccw"></span></a>
        </div>
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

function toMaterialPage(id){
    window.location.href = 'ansicht-material.php?id=' + encodeURIComponent(id);
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