<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: ../login/login.php");
    exit;
}
require_once '../../assets/php/config.php';

$id = $_SESSION['id'];
$username = $_POST['username_input'];
$email = $_POST['email_input'];
$old_password = $_POST['old_password_input'];
$new_password = $_POST['new_password_input'];
$re_new_password = $_POST['re_new_password_input'];
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $users = $result->fetch_assoc();
    // Überprüfen, ob das alte Passwort mit dem in der Datenbank gespeicherten übereinstimmt
    if (password_verify($old_password, $users['password'])) {
        // Passwort aktualisieren, wenn das neue Passwort mit der Wiederholung übereinstimmt
        if ($new_password === $re_new_password) {
            $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $sql = "UPDATE users SET username = ?, email = ?, password = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssss", $username, $email, $new_hashed_password, $id);
            $stmt->execute();

            // Weiterleitung zur Bestätigungsseite
            header("location: profile_saved.php");
            exit;
        } else {
            echo "Die neuen Passwörter stimmen nicht überein.";
        }
    } else {
        echo "Das aktuelle Passwort ist falsch.";
    }
} else {
    echo "Kein Benutzer mit der Id gefunden.";
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
        <a href="#dashboard"><img id="logo" src="../../assets/icons/logo-small.png"></a>
        <div id="user-header" title="Account">
            <p id="username"><?php echo $_SESSION['username'] ?></p>
            <a onclick="toggleMenu()"><img id="user-image" src="../../assets/images/placeholders/Portrait_Placeholder.png"></a>
        </div>
    </header>
    <div id="user-menu">
        <ul>
            <li><a class="menu-link">Profil</a></li>
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
            <p style="text-align:center;"><?php echo $users['first_name'] . ' ' . $users['last_name']; ?></p>
        </div>
        <form class="infoHolder" method="post">
            <div class="disp-text">
                <input type="text" name="username_input" class="text-container" value="<?php echo $users['username']; ?>" autocomplete="off" required/>
                <p class="subname">Benutzername</p>
            </div>
            <div class="disp-text">
                <input type="text" name="email_input" class="text-container" value="<?php echo $users['email']; ?>" autocomplete="off" required/>
                <p class="subname">E-Mail-Adresse</p>
            </div>
            <div class="disp-text">
                <input type="text" name="old_password_input" class="text-container" autocomplete="off" required/>
                <p class="subname">Aktuelles Passwort</p>
            </div>
            <div class="disp-text">
                <input type="text" name="new_password_input" class="text-container" autocomplete="off" required/>
                <p class="subname">Neues Passwort</p>
            </div>
            <div class="disp-text">
                <input type="text" name="re_new_password_input" class="text-container" autocomplete="off" required/>
                <p class="subname">Neues Password wiederholen</p>
            </div>
    </main>

    <footer style="grid-template-columns: 1.5fr 1fr 2fr;">
        <a onclick="siteBack()" class="footer-button_long dark"><span data-feather="arrow-left"></span>Zurück</a>
        <button type="submit" class="footer-button_long" style="font-size:16px;">Speichern</button>
</form>
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
</script>
</html>