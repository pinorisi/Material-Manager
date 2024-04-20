<?php
session_start();

require_once '../config.php';

if (isset($_POST['nameOrEmail'])) {
    $nameOrEmail = $_POST['nameOrEmail'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? OR username = ?");
    $stmt->bind_param("ss", $nameOrEmail, $nameOrEmail);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        echo "Email oder Nutzername nicht gefunden.";
    } else {
        $resetKey = bin2hex(random_bytes(16));
        $stmt = $conn->prepare("UPDATE users SET resetKey = ? WHERE email = ? OR username = ?");
        $stmt->bind_param("sss", $resetKey, $nameOrEmail, $nameOrEmail);
        $stmt->execute();

        $to = $_POST['nameOrEmail'];
        $subject = "Passwort zurücksetzen";
        $message = "Klicken Sie auf den folgenden Link, um Ihr Passwort zurückzusetzen: <br><br>http://localhost/materialManager/pages/login/reset-password.php?key=$resetKey&email=$to";
        $headers = "From: your-email@example.com" . "\r\n" .
            "Reply-To: your-email@example.com" . "\r\n" .
            "X-Mailer: PHP/" . phpversion();
        mail($to, $subject, $message, $headers);

        echo "Ein Link zum Zurücksetzen Ihres Passworts wurde an Ihre E-Mail-Adresse gesendet.";
    }
} else {
    echo "Ungültige Anforderung.";
}

$conn->close();
?>
