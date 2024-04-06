<?php
session_start();

if (isset($_SESSION['loggedin']) || $_SESSION['loggedin'] == true) {
  header("location: ../allgemein/dashboard.php");
  exit;
} elseif (isset($_SESSION['wartungsmodus']) && $_SESSION["wartungsmodus"] == true) {
  header("location: ../login/wartungsmodus.html");
  exit;
}

require_once '../../assets/php/config.php';

if (isset($_POST['submit'])) {
  $username = $_POST['username'];
  $email = $_POST['email'];
  $password = $_POST['password'];
  $re_password = $_POST['re-password'];
  $registerKey = $_POST['registerKey'];

  $stmt = $conn->prepare("SELECT * FROM benutzer WHERE registrierSchluessel = ?");
  $stmt->bind_param("s", $registerKey);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows == 0) {
      $error_message = "Ungültiger Registrierungsschlüssel.";
  } else {
    $stmt = $conn->prepare("SELECT * FROM benutzer WHERE benutzername = ? OR emailAdresse = ?");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
      $error_message = "Nutzername oder E-Mail existieren bereits.";
    } else {
      $hashed_password = password_hash($password, PASSWORD_DEFAULT);

      $stmt = $conn->prepare("UPDATE benutzer SET benutzername = ?, emailAdresse = ?, passwort = ? WHERE registrierSchluessel = ?");
      $stmt->bind_param("ssss", $username, $email, $hashed_password, $registerKey);

      if ($stmt->execute()) {
        header("location: ../bestand/bestand.php");
      } else {
        $error_message = "Registrierung fehlgeschlagen.";
      }
    }

    $stmt->close();
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
        <img id="logo" src="../../assets/icons/logo-long.png">
    </header>

    <main>
        <div class="main-container">
            <form method="post">
                <div>
                    <h1 class="sitetitle">Registrieren</h1>
                    <?php if (isset($error_message)): ?>
                        <p class="errorMessage"><?php echo $error_message; ?></p>
                    <?php endif; ?>
                    <input type="text" id="username" name="username" required autocomplete="on" autofocus>
                    <p>Benutzername</p>
                    <input type="email" id="email" name="email" required autocomplete="on">
                    <p>Email</p>
                    <input type="password" id="password" name="password" required autocomplete="off">
                    <p>Passwort</p>
                    <input type="password" id="re-password" name="re-password" required autocomplete="off">
                    <p>Passwort wiederholen</p>
                    <input type="text" id="registerKey" name="registerKey" required autocomplete="off" style="width: 50%;">
                    <p>Anmeldeschlüssel</p>
                </div>
                <button type="submit" id="submit" name="submit" value="Anmelden">Registrieren</button>
            </form>
        </div>
    </main>

    <footer>
        <a href="../login/login.php"><span data-feather="arrow-left"></span>Anmelden</a>
    </footer>
</body>

<script>
feather.replace();
</script>
</html>