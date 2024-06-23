<?php
session_start();

if (isset($_SESSION['wartungsmodus']) && $_SESSION["wartungsmodus"] == true) {
    header("location: ../login/wartungsmodus.html");
    exit;
}

require_once '../../assets/php/config.php';

if (isset($_POST['submit'])) {
    $nameOrEmail = $_POST['nameOrEmail'];

    $stmt = $conn->prepare("SELECT * FROM benutzer WHERE emailAdresse = ?");
    $stmt->bind_param("s", $nameOrEmail);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        $error_message = "Keinen Account mit dieser Email-Adresse gefunden.";
    } else {
        $benutzer = $result->fetch_assoc();
        $name = $benutzer['vorname'];
        $resetKey = $benutzer['resetSchluessel'];

        if (empty($resetKey)) {
            $resetKey = bin2hex(random_bytes(8));

            $stmt = $conn->prepare("UPDATE benutzer SET resetSchluessel = ? WHERE emailAdresse = ?");
            $stmt->bind_param("ss", $resetKey, $nameOrEmail);
            $stmt->execute();
        }

        $betreff = "Link zum Zurücksetzen deines Passworts.";
        $vorlage = file_get_contents("../../assets/mail-templates/link.html");


        $resetlink = 'https://pinorisi.de/pages/login/reset-password.php?key=' . urlencode($resetKey) . '&email=' . urlencode($nameOrEmail);

        $placeholders = array('%name%', '%resetlink%');
        $replacements = array(htmlspecialchars($name, ENT_QUOTES, 'UTF-8'), htmlspecialchars($resetlink, ENT_QUOTES, 'UTF-8'));
        $inhalt = str_replace($placeholders, $replacements, $vorlage);

        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: Material-Manager <materialmanager@pinorisi.de>" . "\r\n";
        
        $empfaenger = $nameOrEmail;

        if (mail($empfaenger, $betreff, $inhalt, $headers)) {
            $error_message = "Ein Link zum Zurücksetzen deines Passwortes wurde an deine E-Mail gesendet.";
        } else {
            $error_message = "Es ist ein Fehler aufgetreten.";
        }
    }
}



$conn->close();
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

    <link rel="stylesheet" type="text/css" href="../../assets/css/login.css">
</head>
<body>
<div class="wrapper">
    <header>
        <img id="logo" src="../../assets/icons/logo-long.png">
    </header>

    <main>
        <div class="main-container">
            <form method="post">
                <div>
                    <h1 class="sitetitle">Passwort zurücksetzen</h1>
                    <p style="font-size: 16px;">An deine E-Mail Adresse wird ein Link zum zurücksetzen deines Passworts gesendet.</p>
                    <?php if (isset($error_message)): ?>
                        <p class="errorMessage"><?php echo $error_message; ?></p>
                    <?php endif; ?>
                    <input type="text" id="nameOrEmail" name="nameOrEmail" required autocomplete="off" autofocus>
                    <p>E-Mail des Accounts</p>
                </div>
                <button type="submit" id="submit" name="submit" value="Anmelden">Senden</button>
            </form>
        </div>
    </main>

    <footer>
        <a href="../login/login.php"><span data-feather="arrow-left"></span>Anmelden</a>
    </footer>
</div>
</body>

<script>
feather.replace();
</script>
</html>