<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $to = "buchhaltung@mfg-goetting.de";
    $subject = "Neue Krankmeldung";
    
    $name = htmlspecialchars($_POST['name']);
    $arztbesuch = htmlspecialchars($_POST['arztbesuch']);
    $arztbesuch_datum = htmlspecialchars($_POST['arztbesuch_datum']);
    $krankheitsdauer = htmlspecialchars($_POST['krankheitsdauer']);
    $krankschreibung_typ = htmlspecialchars($_POST['krankschreibung_typ']);

    $message = "Name: $name\n";
    $message .= "War beim Arzt: $arztbesuch\n";
    $message .= "Datum des Arztbesuchs: $arztbesuch_datum\n";
    $message .= "Krankheitsdauer: $krankheitsdauer\n";
    $message .= "Krankmeldung Typ: $krankschreibung_typ\n";

    // Datei-Upload-Handling
    if (isset($_FILES['krankschreibung']) && $_FILES['krankschreibung']['error'] == UPLOAD_ERR_OK) {
        $file = $_FILES['krankschreibung'];
        $file_name = $file['name'];
        $file_tmp = $file['tmp_name'];
        $file_size = $file['size'];
        $file_type = $file['type'];
        
        $file_content = chunk_split(base64_encode(file_get_contents($file_tmp)));
        $boundary = md5(uniqid(time()));

        $headers = "From: noreply@mfg-goetting.de\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: multipart/mixed; boundary=\"{$boundary}\"\r\n";

        $body = "--{$boundary}\r\n";
        $body .= "Content-Type: text/plain; charset=\"UTF-8\"\r\n";
        $body .= "Content-Transfer-Encoding: 7bit\r\n";
        $body .= "\r\n{$message}\r\n";
        $body .= "--{$boundary}\r\n";
        $body .= "Content-Type: {$file_type}; name=\"{$file_name}\"\r\n";
        $body .= "Content-Disposition: attachment; filename=\"{$file_name}\"\r\n";
        $body .= "Content-Transfer-Encoding: base64\r\n";
        $body .= "\r\n{$file_content}\r\n";
        $body .= "--{$boundary}--";

        mail($to, $subject, $body, $headers);
    } else {
        $headers = "From: noreply@mfg-goetting.de\r\n";
        $headers .= "Content-Type: text/plain; charset=\"UTF-8\"";

        mail($to, $subject, $message, $headers);
    }

    header("Location: danke.html");
    exit();
}
?>
