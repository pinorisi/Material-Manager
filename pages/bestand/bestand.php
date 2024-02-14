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
        <h1>Bestand</h1>
        <form class="search-container">
            <input type="search" id="search-bar" placeholder="Suchen..." onkeyup="searchBestand()">
            <button type="button"><span data-feather="search"></span></button>
        </form>
        <p class="subname" style="margin-top: 2px;" id="resultCount">0 Ergebnisse gefunden</p>

        <ul class="bestand-list">
        <?php
            $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "materialmanager";
        
            $conn = new mysqli($servername, $username, $password, $dbname);
        
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }
        
            $sql = "SELECT bezeichnung, id, anzahl, status FROM material";
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
                    echo '<li class="space-between" onclick="toMaterialPage(\'' . $row["bezeichnung"] . '\')">
                            <p>' . $row["bezeichnung"] . '</p>
                            <div style="display: flex;flex-direction: row;gap: 8px;align-items: center;">
                                <span style="width: 14px; height: 14px;" ' . $statusIcon . '></span>|<p style="text-align: center;">' . $row["anzahl"] . '</p>
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
        <a href="#" class="footer-button_long dark"><span data-feather="arrow-left"></span>Zurück</a>
        <div style="display:flex;flex-direction:row;gap:16px;">
        <a href="erstellen.php" class="footer-button" title="Hinzufügen"><span style="color: white;" data-feather="plus"></span></a>
        <a onclick="pageRefresh()" class="footer-button light" title="Aktualisieren"><span style="color: #232527;" data-feather="refresh-ccw"></span></a>
        </div>
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
    // Modal schließen
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

function toMaterialPage(bezeichnung){
    window.location.href = 'ansicht-material.php?bezeichnung=' + encodeURIComponent(bezeichnung);
}
</script>
</html>