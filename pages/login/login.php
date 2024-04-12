<?php
session_start();

if (isset($_SESSION['wartungsmodus']) && $_SESSION["wartungsmodus"] == true) {
    header("location: ../login/wartungsmodus.html");
    exit;
}

require_once '../../assets/php/config.php';

if (isset($_POST['submit'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT idbenutzer, benutzername, passwort, letzterLogin FROM benutzer WHERE benutzername = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $id = $row['idbenutzer'];
            $db_username = $row['benutzername'];
            $db_password = $row['passwort'];
            $last_login = $row['letzterLogin'];

            if (password_verify($password, $db_password)) {
                $_SESSION['loggedin'] = true;
                $_SESSION['id'] = $id;
                $_SESSION['username'] = $username;

                $current_time = date("Y-m-d H:i:s");
                $sql_update = "UPDATE benutzer SET letzterLogin = ? WHERE idbenutzer = ?";
                $stmt_update = $conn->prepare($sql_update);
                $stmt_update->bind_param("ss", $current_time, $id);
                $stmt_update->execute();
                $stmt_update->close();

                header("location: ../allgemein/dashboard.php");
            } else {
                $error_message = 'Benutzername oder Passwort ist falsch.';
            }
        }
    } else {
        $error_message = 'Benutzername oder Passwort ist falsch.';
    }

    $stmt->close();
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
            <form method="Post">
                <div>
                    <h1 class="sitetitle">Anmelden</h1>
                    <?php if (isset($error_message)): ?>
                        <p class="errorMessage"><?php echo $error_message; ?></p>
                    <?php endif; ?>
                    <input type="text" id="username" name="username" required autocomplete="on" autofocus>
                    <p>Benutzername</p>
                    <input type="password" id="password" name="password" required autocomplete="on">
                    <p>Passwort</p>
                </div>
                <button type="submit" id="submit" name="submit" value="Anmelden">Anmelden</button>
            </form>
        </div>
    </main>

    <footer>
        <a href="../login/register.php"><span data-feather="arrow-right"></span>Registrieren</a>
        <a href="../login/send-password.php"><span data-feather="arrow-right"></span>Passwort vergessen</a>
    </footer>
</body>

<script>
feather.replace();
</script>
</html>