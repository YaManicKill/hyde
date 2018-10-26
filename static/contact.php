<?php
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
        $headers = 'From: Harvest Season Podcast <contact@harvestseason.club>' . "\r\n" .
                "Content-Type: text/html; charset=ISO-8859-1\r\n" .
                'Bcc: harvestseason@10people.co.uk' . "\r\n" .
                'Reply-To: ' . $name . '<' . $email . '>' . "\r\n" .
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
