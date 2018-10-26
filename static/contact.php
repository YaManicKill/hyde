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
        $to      = 'harvestseason@10people.co.uk,raschelle.dellaney@gmail.com';
        $headers = 'From: contact@harvestseason.club' . "\r\n" .
                'Reply-To: ' . $email . "\r\n" .
                'X-Mailer: PHP/' . phpversion();

        $success = mail($to, $subject, $message, $headers);
        if ("$success" == "1") {
            header( 'Location: /thanks' );
        } else {
            header( 'Location: /error' );
        }
    }

?>
