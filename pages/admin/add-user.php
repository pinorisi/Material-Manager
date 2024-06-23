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

require_once '../../assets/php/config.php';

if($_SERVER['REQUEST_METHOD'] == "POST") {
    $vorname = $_POST["vorname_input"];
    $email = $_POST["email_input"];
    $berechtigung = $_POST["berechtigung_input"];
    
    $registerKey = generateRegisterKey();
    $sql = "INSERT INTO benutzer (vorname, emailAdresse, rolleId, registrierSchluessel) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssis", $vorname, $email, $berechtigung, $registerKey);
    $stmt->execute();

    $betreff = "Deine Zugangsdaten zum Material-Manager";
    $vorlage = file_get_contents("../../assets/mail-templates/registration.html");

    $placeholders = array('%name%', '%registerCode%');
    $replacements = array($vorname, $registerKey);
    $inhalt = str_replace($placeholders, $replacements, $vorlage);
    $empfaenger = $email;

    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: Material-Manager <materialmanager@pinorisi.de>" . "\r\n";

    if (mail($empfaenger, $betreff, $inhalt, $headers)) {
        $error_message = "Nachricht wurde erfolgreich versendet.";
    } else {
        $error_message = "Es gab ein Problem beim Versenden Ihrer Nachricht.";
    }

    $error_message = "Benutzer erfolgreich erstellt!";
}


function generateRegisterKey() {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $registerKey = '';
    $length = 8;
    for ($i = 0; $i < $length; $i++) {
        $registerKey .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $registerKey;
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
        <h1>Nutzer erstellen</h1>
        <form class="infoHolder" method="post">
            <div class="disp-text">
                <input type="text" id="vorname_input" name="vorname_input" class="text-container" autocomplete="off" required/>
                <p class="subname">Vorname</p>
            </div>
            <div class="disp-text">
                <input type="text" id="email_input" name="email_input" class="text-container" autocomplete="off" required/>
                <p class="subname">E-Mail-Adresse</p>
            </div>
            <div class="grid-2">
                <div class="disp-text">
                    <input type="number" id="berechtigung_input" name="berechtigung_input" min="1" max="4" class="text-container" style="text-decoration: none; color: #232527;" autocomplete="off" required/>
                    <p class="subname">Berechtigung</p>
                </div>
                <div class="disp-text">
                </div>
            </div>
                <?php if (isset($error_message)): ?>
                    <p class="errorMessage"><?php echo $error_message; ?></p>
                <?php endif; ?>
            <p class="subname">Dem neuen Nutzer wird eine Mail mit einem Registrierungsschl&uuml;ssel zugesendet.</p>
            <?php  ?>
    </main>

    <footer style="grid-template-columns: 1.5fr 1fr 2fr;">
        <a onclick="siteBack()" class="footer-button_long dark"><span data-feather="arrow-left"></span>Zurück</a>
        <button type="submit" class="footer-button_long" style="font-size:16px;">Erstellen</button>
</form>
    </footer>

    <div class="modal" id="modal">
		<div class="modal-content">
			<div class="space-between">
				<a onclick="closeModal('modal')"><span data-feather="arrow-left"></span></a>
				<p style="width: 100%; text-align: center;">Profil aktualisiert</p>
			</div>
            <p>Deine Kontodaten wurden erfolgreich aktualisiert</p>
            <span data-feather="check"></span>
			<div class="space-between" style="margin-top: 16px;">
				<a onClick="closeModal('modal')" class="footer-button_long dark"><span data-feather="arrow-left"></span>Zurück</a>
			</div>
		</div>
	</div>
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

function openModal(){
    var x = document.getElementById('modal');
    x.classList.add('open');
}

function closeModal(){
    var x = document.getElementById('modal');
    x.classList.remove('open');
}
</script>
</html>