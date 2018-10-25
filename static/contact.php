<?php

class Mail
{
    protected $to;
    protected $from;
    protected $sender;
    protected $sender_email;
    protected $subject;
    protected $html;

    public function setTo($to)
    {
        $this->to = $to;
    }

    public function setFrom($from)
    {
        $this->from = $from;
    }

    public function setSender($sender)
    {
        $this->sender = $sender;
    }

    public function setSenderEmail($sender_email)
    {
        $this->sender_email = $sender_email;
    }

    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    public function setHtml($html)
    {
        $this->html = $html;
    }

    public function send()
    {
        $boundary = '----=_NextPart_' . md5(time());

        $header   = 'MIME-Version: 1.0' . PHP_EOL;
        $header  .= 'To: <' . $this->to . '>' . PHP_EOL;
        $header  .= 'Date: ' . date('D, d M Y H:i:s O') . PHP_EOL;
        $header  .= 'From: =?UTF-8?B?' . base64_encode('Harvest Season Podcast') . '?= <' . $this->from . '>' . PHP_EOL;
        $header  .= 'Reply-To: =?UTF-8?B?' . base64_encode($this->sender) . '?= <' . $this->sender_email . '>' . PHP_EOL;
        $header  .= 'Return-Path: ' . $this->from . PHP_EOL;
        $header  .= 'X-Mailer: PHP/' . phpversion() . PHP_EOL;
        $header  .= 'X-Priority: 3' . PHP_EOL;
        $header  .= 'MIME-Version: 1.0' . PHP_EOL;
        $header  .= 'Message-ID: ' . '<' . md5(uniqid(rand(), true )) . '@' . $_SERVER[ 'HTTP_HOST' ] . '>' . PHP_EOL;
        $header  .= 'Content-Type: multipart/mixed; boundary="' . $boundary . '"' . PHP_EOL . PHP_EOL;

        $message  = '--' . $boundary . PHP_EOL;
        $message .= 'Content-Type: multipart/alternative; boundary="' . $boundary . '_alt"' . PHP_EOL . PHP_EOL;
        $message .= '--' . $boundary . '_alt' . PHP_EOL;
        $message .= 'Content-Type: text/plain; charset="utf-8"' . PHP_EOL;
        $message .= 'Content-Transfer-Encoding: 8bit' . PHP_EOL . PHP_EOL;

        $message .= '--' . $boundary . '_alt' . PHP_EOL;
        $message .= 'Content-Type: text/html; charset="utf-8"' . PHP_EOL;
        $message .= 'Content-Transfer-Encoding: 8bit' . PHP_EOL . PHP_EOL;
        $message .= $this->html . PHP_EOL;
        $message .= '--' . $boundary . '_alt--' . PHP_EOL;

        ini_set('sendmail_from', $this->from);

        $success = mail("", '=?UTF-8?B?' . base64_encode($this->subject) . '?=', $message, $header);
        return $success;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
        $mail = new Mail();
        $mail->setTo('contact@harvestseason.club');
        $mail->setFrom('contact@harvestseason.club');
        $mail->setSender($name);
        $mail->setSenderEmail($email);
        $mail->setSubject('Harvest Season Email: ' . $subject);
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
        $mail->setHtml($body);
        $success = $mail->send();
        if ("$success" == "1") {
            header( 'Location: /thanks' );
        } else {
            header( 'Location: /error' );
        }
        $emailSent = true;
    } else {
        $hasError = true;
    }
}
?>
