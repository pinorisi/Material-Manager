<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: ../login/login.php");
    exit;
} elseif (isset($_SESSION['wartungsmodus']) && $_SESSION["wartungsmodus"] == true) {
    header("location: ../login/logout.html");
    exit;
}
require_once '../../assets/php/config.php';

$id = $_SESSION['id'];

$sql = "SELECT * FROM benutzer WHERE idbenutzer = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $id);
$stmt->execute();
$result = $stmt->get_result();

if( $result->num_rows > 0) {
    $users = $result->fetch_assoc();
} else {
    echo "Kein Benutzer mit der Id gefunden.";
}

if($_SERVER['REQUEST_METHOD'] == "POST"){
    $username = $_POST["username_input"];
    $email = $_POST["email_input"];
    $old_password = $_POST["old_password_input"];
    $new_password = $_POST["new_password_input"];
    $new_confirm = $_POST["confirm_new_password_input"];

    // Überprüfen, ob sich der Benutzername geändert hat
    if (!empty($username) && $username !== $users['benutzername']) {
        $username = filter_var($username, FILTER_SANITIZE_STRING);
        $sql = "UPDATE benutzer SET benutzername = ? WHERE idbenutzer = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $username, $id);
        $stmt->execute();
    }

    // Überprüfen, ob sich die E-Mail-Adresse geändert hat
    if (!empty($email) && $email !== $users['emailAdresse']) {
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        $sql = "UPDATE benutzer SET emailAdresse = ? WHERE idbenutzer = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $email, $id);
        $stmt->execute();
    }

    if(password_verify($old_password, $users['passwort'])){
        if (!empty($new_password) && $new_password === $new_confirm) {
            $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);

            $sql = "UPDATE benutzer SET passwort = ? WHERE idbenutzer = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $new_password_hash, $id);
            $stmt->execute();

            $_SESSION['benutzername'] = $username;

            echo "<scrip>document.getElementById('modal').classlist.add('open');</scrip>";
        } else {
            echo 'Die neuen Passwörter stimmen nicht überein.';
        }
    } else{
        echo 'Das alte Passwort ist falsch.';
    }
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
        <a href="dashboard.php"><img id="logo" src="../../assets/icons/logo-small.png"></a>
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
        <h1>Profil</h1>
        <div class="infoHolder" style="align-items: center;">
            <img class="profile-img" src="../../assets/images/placeholders/Portrait_Placeholder.png">
            <p style="text-align:center;"><?php echo $users['vorname']; ?></p>
        </div>
        <form class="infoHolder" method="post">
            <div class="disp-text">
                <input type="text" name="username_input" class="text-container" value="<?php echo $users['benutzername']; ?>" autocomplete="off" required/>
                <p class="subname">Benutzername</p>
            </div>
            <div class="disp-text">
                <input type="email" name="email_input" class="text-container" value="<?php echo $users['emailAdresse']; ?>" autocomplete="off" required/>
                <p class="subname">E-Mail-Adresse</p>
            </div>
            <div class="disp-text">
                <input type="password" name="old_password_input" class="text-container" autocomplete="off" required/>
                <p class="subname">Aktuelles Passwort</p>
            </div>
            <div class="disp-text">
                <input type="password" name="new_password_input" class="text-container" autocomplete="off"/>
                <p class="subname">Neues Passwort</p>
            </div>
            <div class="disp-text">
                <input type="password" name="confirm_new_password_input" class="text-container" autocomplete="off"/>
                <p class="subname">Neues Password wiederholen</p>
            </div>
    </main>

    <footer style="grid-template-columns: 1.5fr 1fr 2fr;">
        <a onclick="siteBack()" class="footer-button_long dark"><span data-feather="arrow-left"></span>Zurück</a>
        <button type="submit" class="footer-button_long" style="font-size:16px;">Speichern</button>
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
                <p id="qrContent">...</p>
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