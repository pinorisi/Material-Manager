<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: ../login/login.php");
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
        
            $sql = "SELECT bezeichnung, id, icon, status FROM lager";
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
                    echo '<li class="space-between blli" onclick="toLagerPage(\'' . $row["id"] . '\')">
                            <p>' . $row["bezeichnung"] . '</p>
                            <div style="display: flex;flex-direction: row;gap: 8px;align-items: center;">
                                <span style="width: 14px; height: 14px;" ' . $statusIcon . '></span><div class="vertical-line"></div><img src="' . $chestIcon . '" style="height: 16px; aspect-ratio: 1/1;">
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

    <footer style="grid-template-columns: 1.5fr 1fr 2fr;">
        <a href="../allgemein/dashboard.php" class="footer-button_long dark"><span data-feather="arrow-left"></span>Zurück</a>
        <button id="scanBtn" class="footer-button_long"><span data-feather="camera"></span>Scannen</button>
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
</script>
</html>