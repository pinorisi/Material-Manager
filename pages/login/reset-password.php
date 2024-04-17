<?php
session_start();

if (isset($_SESSION['wartungsmodus']) && $_SESSION["wartungsmodus"] == true) {
    header("location: ../login/wartungsmodus.html");
    exit;
}

require_once '../../assets/php/config.php';

$resetKey = $_GET['key'];
$email = $_GET['email'];

$stmt = $conn->prepare("SELECT * FROM users WHERE resetKey = ? AND email = ?");
$stmt->bind_param("ss", $resetKey, $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    $error_message = "Ungültiger Reset-Key";
} else {
  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = $result->fetch_assoc();
    $userId = $user['id'];
    $password = $_POST['password'];
    $re_password = $_POST['re-password'];

    if ($password !== $re_password) {
      $error_message = "Die eingegebenen Passwörter stimmen nicht überein";
    } else {
      $hashed_password = password_hash($password, PASSWORD_DEFAULT);

      $stmt = $conn->prepare("UPDATE users SET password = ?, resetKey = NULL WHERE id = ?");
      $stmt->bind_param("si", $hashed_password, $userId);
      $stmt->execute();

      $error_message = "Dein Password wurde zurückgesetzt";
      header("location: ../login/login.php");
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
                    <?php if (isset($error_message)): ?>
                        <p class="errorMessage"><?php echo $error_message; ?></p>
                    <?php endif; ?>
                    <p style="font-size: 16px;">Erstelle ein neues Passwort für dein Konto.</p>
                    <input type="password" id="password" name="password" required autocomplete="off" autofocus>
                    <p>Neues Passwort</p>
                    <input type="password" id="re-password" name="re-password" required autocomplete="off">
                    <p>Neues Passwort wiederholen</p>
                </div>
                <button type="submit" id="submit" name="submit" value="Anmelden">Zurücksetzen</button>
            </form>
        </div>
    </main>
</div>
</body>

<script>
feather.replace();
</script>
</html>