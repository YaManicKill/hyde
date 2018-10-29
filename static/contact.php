<?php
    $myfile = fopen(__DIR__.'/../../../config.toml', "r") or die("Unable to open file!");
    // Things to get out of config
    $fromEmail = "";
    $fromName = "";
    $toEmails = "";

    while(!feof($myfile)) {
        $line = fgets($myfile);
        if(strpos($line, 'toEmails') !== FALSE) {
            $toEmails = str_replace("\n", "", str_replace("\"", "", explode(' = ',$line)[1]));
        }
        if(strpos($line, 'fromEmail') !== FALSE) {
            $fromEmail = str_replace("\n", "", str_replace("\"", "", explode(' = ',$line)[1]));
        }
        if(strpos($line, 'fromName') !== FALSE) {
            $fromName = str_replace("\n", "", str_replace("\"", "", explode(' = ',$line)[1]));
        }
    }
    fclose($myfile);

    $name    = stripslashes(trim($_POST['form-name']));
    $email   = stripslashes(trim($_POST['form-email']));
    $subject = stripslashes(trim($_POST['form-subject']));
    $message = stripslashes(trim($_POST['form-message']));
    $pattern = '/[\r\n]|Content-Type:|Bcc:|Cc:/i';

    if (preg_match($pattern, $name) || preg_match($pattern, $email) || preg_match($pattern, $subject)) {
        die("Header injection detected");
    }
    $emailIsValid = filter_var($email, FILTER_VALIDATE_EMAIL);
    if ($name && $email && $emailIsValid && $subject && $message) {
        $to      = '';
        $headers = "From: $fromName <$fromEmail>\r\n" .
                "Content-Type: text/html; charset=ISO-8859-1\r\n" .
                "Bcc: $toEmails\r\n" .
                "Reply-To: $name <$email>\r\n" .
                'X-Mailer: PHP/' . phpversion();

        $body = "
        <!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transit$
        <html>
            <head>
                <meta charset=\"utf-8\">
            </head>
            <body>
                <h1>{$subject}</h1>
                <p><strong>Name:</strong> {$name}</p>
                <p><strong>Email:</strong> {$email}</p>
                <p><strong>Message:</strong> {$message}</p>
            </body>
        </html>";

        $success = mail($to, $subject, $body, $headers);
        if ("$success" == "1") {
            header( 'Location: /thanks' );
        } else {
            header( 'Location: /error' );
        }
    }

?>
