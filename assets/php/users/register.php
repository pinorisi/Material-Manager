<?php
session_start();

require_once '../config.php';

$username = $_POST['username'];
$email = $_POST['email'];
$password = $_POST['password'];
$re_password = $_POST['re-password'];
$registerKey = $_POST['registerKey'];

$stmt = $conn->prepare("SELECT * FROM users WHERE registerKey = ?");
$stmt->bind_param("s", $registerKey);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
  echo "Ungültiger Registrierungsschlüssel.";
} else {
  $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
  $stmt->bind_param("ss", $username, $email);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows > 0) {
    echo "Nutzername oder E-Mail existieren bereits.";
  } else {
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, password = ? WHERE registerKey = ?");
    $stmt->bind_param("ssss", $username, $email, $hashed_password, $registerKey);

    if ($stmt->execute()) {
      header("location: ../../../pages/bestand/bestand.php");
    } else {
      echo "Registrierung fehlgeschlagen.";
    }
  }

  $stmt->close();
}

$conn->close();
?>