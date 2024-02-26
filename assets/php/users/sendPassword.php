<?php
session_start();

require_once '../config.php';

// Get the submitted email or username
$nameOrEmail = $_POST['nameOrEmail'];

// Check if the email or username exists in the database
$stmt = $conn->prepare("SELECT * FROM users WHERE email = ? OR username = ?");
$stmt->bind_param("ss", $nameOrEmail, $nameOrEmail);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
  // Email or username not found
  echo "Email oder Nutzername nicht gefunden.";
} else {
  // Generate a random reset key
  $resetKey = bin2hex(random_bytes(8));

  // Update the user's reset key in the database
  $stmt = $conn->prepare("UPDATE users SET resetKey = ? WHERE email = ? OR username = ?");
  $stmt->bind_param("sss", $resetKey, $nameOrEmail, $nameOrEmail);
  $stmt->execute();

  // Send the reset link to the user's email
  $to = $_POST['nameOrEmail'];
  $subject = "Passwort zurücksetzen";
  $message = "Click the following link to reset your password: <br><br>http://localhost/materialManager/pages/login/reset-password.php?key=$resetKey&email=$to";
  $headers = "From: your-email@example.com" . "\r\n" .
    "Reply-To: your-email@example.com" . "\r\n" .
    "X-Mailer: PHP/" . phpversion();
  mail($to, $subject, $message, $headers);

  echo "Ein Link zum zurücksetzen deines Passwortes wurde an deine Email gesendet.";
  echo $message;
}

$conn->close();
?>