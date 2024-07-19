<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $arztbesuch = $_POST['arztbesuch'];
    $arztbesuch_datum = $_POST['arztbesuch_datum'];
    $krankheitsdauer = $_POST['krankheitsdauer'];
    $to = "buchhaltung@goetting.de";
    $subject = "Krankmeldung von $name";
    $message = "Name: $name\n";
    $message .= "War beim Arzt: $arztbesuch\n";
    if (!empty($arztbesuch_datum)) {
        $message .= "Datum des Arztbesuchs: $arztbesuch_datum\n";
    }
    $message .= "Dauer der Krankschreibung: $krankheitsdauer\n";

    $headers = "From: webmaster@yourdomain.com";

    // Dateianhang
    if (isset($_FILES['krankschreibung']) && $_FILES['krankschreibung']['error'] == UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['krankschreibung']['tmp_name'];
        $file_name = $_FILES['krankschreibung']['name'];
        $file_size = $_FILES['krankschreibung']['size'];
        $file_type = $_FILES['krankschreibung']['type'];

        $handle = fopen($file_tmp, "r");
        $content = fread($handle, $file_size);
        fclose($handle);

        $encoded_content = chunk_split(base64_encode($content));

        $boundary = md5("random");

        $headers .= "\r\nMIME-Version: 1.0\r\n";
        $headers .= "Content-Type: multipart/mixed; boundary=$boundary\r\n";

        $body = "--$boundary\r\n";
        $body .= "Content-Type: text/plain; charset=ISO-8859-1\r\n";
        $body .= "Content-Transfer-Encoding: base64\r\n\r\n";
        $body .= chunk_split(base64_encode($message));

        $body .= "--$boundary\r\n";
        $body .= "Content-Type: $file_type; name=\"$file_name\"\r\n";
        $body .= "Content-Disposition: attachment; filename=\"$file_name\"\r\n";
        $body .= "Content-Transfer-Encoding: base64\r\n";
        $body .= "X-Attachment-Id: ".rand(1000, 99999)."\r\n\r\n";
        $body .= $encoded_content;
    } else {
        $body = $message;
    }

    if (mail($to, $subject, $body, $headers)) {
        echo "E-Mail erfolgreich gesendet.";
    } else {
        echo "Fehler beim Senden der E-Mail.";
    }
}
?>
