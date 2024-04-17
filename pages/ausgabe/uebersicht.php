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
<div class="wrapper">
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
        <h1>Ausgaben</h1>
        <p style="margin-top:40px;">Aktuelle Ausgaben</p>
        <hr style="margin-top:8px;">
        <ul class="bestand-list" style="height:78px;">
            <?php
                require_once('../../assets/php/config.php');
                $currentDate = date("Y.m.d");
        
                $sql = "SELECT idAktion, bezeichnung, beginn, ende FROM aktionen WHERE beginn <= '$currentDate' AND ende >= '$currentDate'";
                $result = $conn->query($sql);
            
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo '<li class="space-between blli" onclick="toAktionspage(\'' . $row["idAktion"] . '\')">
                                <p>' . $row["bezeichnung"] . '</p>
                                <div style="display: flex;flex-direction: row;gap: 8px;align-items: center;">
                                    <span style="width: 14px; height: 14px;"></span><div class="vertical-line"></div><p style="text-align: center; width:auto;">bis ' . date("d.m.Y", strtotime($row['ende'])) . '</p>
                                </div>
                            </li>';
                    }
                } else {
                    echo "<li>Keine aktuellen Ausgaben.</li>";
                }
            
            ?>
        </ul>

        <p style="margin-top:20px;"><b>Anstehend</b></p>
        <hr style="margin-top:8px;">
        <ul class="bestand-list" style="height:10%;">
            <?php
                require_once('../../assets/php/config.php');
                $currentDate = date("Y.m.d");
        
                $sql = "SELECT idAktion, bezeichnung, beginn, ende FROM aktionen WHERE beginn > '$currentDate' AND beginn <> '$currentDate'";
                $result = $conn->query($sql);
            
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo '<li class="space-between blli" onclick="toAktionspage(\'' . $row["idAktion"] . '\')">
                                <p>' . $row["bezeichnung"] . '</p>
                                <div style="display: flex;flex-direction: row;gap: 8px;align-items: center;">
                                    <span style="width: 14px; height: 14px;"></span><div class="vertical-line"></div><p style="text-align: center; width:auto;">ab ' . date("d.m.Y", strtotime($row['beginn'])) . '</p>
                                </div>
                            </li>';
                    }
                } else {
                    echo "<li>Keine Anstehenden Aktionen.</li>";
                }
            ?>
        </ul>

        <p style="margin-top:20px;"><b>Historie</b></p>
        <hr style="margin-top:8px;">
        <ul class="bestand-list" style="height:38%;">
            <?php
                require_once('../../assets/php/config.php');
                $currentDate = date("Y.m.d");
        
                $sql = "SELECT idAktion, bezeichnung, beginn, ende FROM aktionen WHERE ende < '$currentDate'";
                $result = $conn->query($sql);
            
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo '<li class="space-between blli" onclick="toAktionspage(\'' . $row["idAktion"] . '\')">
                                <p>' . $row["bezeichnung"] . '</p>
                                <div style="display: flex;flex-direction: row;gap: 8px;align-items: center;">
                                    <span style="width: 14px; height: 14px;"></span><div class="vertical-line"></div><p style="text-align: center; width:auto;">bis ' . date("d.m.Y", strtotime($row['ende'])) . '</p>
                                </div>
                            </li>';
                    }
                } else {
                    echo "<li>Keine Historie verfügbar.</li>";
                }
            
                $conn->close();
            ?>
        </ul>
    </main>

    <footer>
        <a href="../allgemein/dashboard.php" class="footer-button_long dark"><span data-feather="arrow-left"></span>Zurück</a>
        <a href="erstellen.php" class="footer-button_long"><span data-feather="plus"></span>Ausgabe</a>
    </footer>
</div>
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
    window.location.href = '../ausgabe/ansicht.php?id=' + encodeURIComponent(id);
}
</script>
</html>