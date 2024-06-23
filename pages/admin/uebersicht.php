<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: ../login/login.php");
    exit;
} elseif (isset($_SESSION['berechtigung']) && $_SESSION["berechtigung"] !== 4) {
    header("location: ../allgemein/dashboard.php");
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
        <h1>Administration</h1>
        
        <ul class="bestand-list">
            <?php 
                require_once('../../assets/php/config.php');

                $query = "SELECT idbenutzer, benutzername, vorname FROM benutzer";
                $result = mysqli_query($conn, $query);

                if ($result) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo '<li style="height: 40px; justify-content:space-between;">
                                <div>
                                    <p style="width:auto">' . htmlspecialchars($row['vorname']) . '</p>
                                    <p class="subname" style="width:auto">' . htmlspecialchars($row['benutzername']) . '</p>
                                </div>
                                <div style="display:flex;">
                                    <p>' . htmlspecialchars($row['idbenutzer']) . '</p>';
                
                                    if ($row['idbenutzer'] == $_SESSION['id']) {
                                        echo '<div style="width:20px"></div>';
                                    } else {
                                        echo '<a class="delMatBtn" onclick="openModal(' . $row['idbenutzer'] . ')"><span style="margin-top:1px; margin-left:0px;" data-feather="x"></span></a>';
                                    }
                
                        echo '    </div>
                            </li>';
                    }
                } else {
                    echo "Fehler beim Abrufen der Daten.";
                }

                mysqli_close($conn);
            ?>
        </ul>
    </main>

    <footer style="grid-template-columns: 1.5fr 1fr 2fr;">
        <a onclick="siteBack()" class="footer-button_long dark"><span data-feather="arrow-left"></span>Zurück</a>
        <a href="add-user.php" class="footer-button_long" style="font-size:16px;">Hinzufügen</a>
    </footer>

    <div class="modal" id="delModal">
        <div class="modal-content">
            <div class="space-between">
                <a onclick="closeModal()"><span data-feather="arrow-left"></span></a>
                <p style="width: 100%; text-align: center; font-weight: 600;">Löschen</p>
            </div>
            <p>Soll der Benutzer unwiderruflich gelöscht werden?</p>
            <div class="space-between" style="margin-top: 16px;">
                <a id="delBtn" onclick="deleteUser()" class="footer-button_long" style="background-color:#9B3535;">Löschen</a>
                <a onClick="closeModal()" class="footer-button_long light">Abbrechen</a>
            </div>
        </div>
    </div>

</div>
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

function openModal(iduser){
    console.log(iduser);
    var x = document.getElementById('delModal');
    x.classList.add('open');
    document.getElementById('delBtn').setAttribute('data-id', iduser);
}

function closeModal(){
    var x = document.getElementById('delModal');
    x.classList.remove('open');
}

function deleteUser(){
    var iduser = document.getElementById('delBtn').getAttribute('data-id');
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "../../assets/php/admin/delete_user.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            alert(xhr.responseText); // Optional: Benutzer über den Erfolg informieren
            location.reload(); // Seite neu laden
        }
    };
    xhr.send("iduser=" + iduser);
}
</script>
</body>
</html>
