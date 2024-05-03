<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $message = $_POST['message'];

    $empfaenger = $email;

    $betreff = "Deine Zugangsdaten zum Material-Manager";

    $vorlage = file_get_contents("assets/mail-templates/registration.html");

    $placeholders = array('%name%', '%registerCode%');
    $replacements = array($name, $message);
    $inhalt = str_replace($placeholders, $replacements, $vorlage);

    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: Material-Manager <materialmanager@pinorisi.de>" . "\r\n";

    if (mail($empfaenger, $betreff, $inhalt, $headers)) {
        echo "Nachricht wurde erfolgreich versendet.";
    } else {
        echo "Es gab ein Problem beim Versenden Ihrer Nachricht.";
    }
}
?>
