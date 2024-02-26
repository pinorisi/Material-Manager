<?php
session_start();

require_once '../../assets/php/config.php';

if (isset($_POST['submit'])) {
    $nameOrEmail = $_POST['nameOrEmail'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? OR username = ?");
    $stmt->bind_param("ss", $nameOrEmail, $nameOrEmail);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        $error_message = "Email oder Nutzername nicht gefunden.";
    } else {
      $resetKey = bin2hex(random_bytes(8));

      $stmt = $conn->prepare("UPDATE users SET resetKey = ? WHERE email = ? OR username = ?");
      $stmt->bind_param("sss", $resetKey, $nameOrEmail, $nameOrEmail);
      $stmt->execute();

      $to = $_POST['nameOrEmail'];
      $subject = "Passwort zurücksetzen";
      $message = "Click the following link to reset your password: <br><br>http://localhost/materialManager/pages/login/reset-password.php?key=$resetKey&email=$to";
      $headers = "From: your-email@example.com" . "\r\n" .
        "Reply-To: your-email@example.com" . "\r\n" .
        "X-Mailer: PHP/" . phpversion();
      mail($to, $subject, $message, $headers);

      $error_message = "Ein Link zum zurücksetzen deines Passwortes wurde an deine Email gesendet.";
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
    <header>
        <!-- Logo -->
        <img id="logo" src="../../assets/icons/logo-long.png">
    </header>

    <main>
        <!-- Formulare -->
        <div class="main-container">
            <form method="post">
                <div>
                    <h1 class="sitetitle">Passwort zurücksetzen</h1>
                    <p style="font-size: 16px;">An deine E-Mail Adresse wird ein Link zum zurücksetzen deines Passworts gesendet.</p>
                    <?php if (isset($error_message)): ?>
                        <p class="errorMessage"><?php echo $error_message; ?></p>
                    <?php endif; ?>
                    <input type="text" id="nameOrEmail" name="nameOrEmail" required autocomplete="off" autofocus>
                    <p>Benutzername oder E-Mail</p>
                </div>
                <button type="submit" id="submit" name="submit" value="Anmelden">Senden</button>
            </form>
        </div>
    </main>

    <footer>
        <!-- Links -->
        <a href="../login/login.php"><span data-feather="arrow-left"></span>Anmelden</a>
    </footer>
</body>

<script>
feather.replace();
</script>
</html>