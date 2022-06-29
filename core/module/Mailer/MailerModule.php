<?php

include dirname(__FILE__) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'PHPMailer.php';
include dirname(__FILE__) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'SMTP.php';

class MailerModule
{

    const SUCCESS_CODE = 1;
    const ERROR_CODE = -1;

    public static $mail;
    public static $info;

    public static $recipients = [];

    public static function sendMail($sender, $subject, $mail_body)
    {
        self::$mail = new PHPMailer();

        self::$mail->isSMTP();

        self::$mail->CharSet        = Core::$config['module']['Mailer']['charset'];
        self::$mail->SMTPDebug      = Core::$config['module']['Mailer']['SMTPDebug'];
        self::$mail->Debugoutput    = 'html';
        self::$mail->Host           = Core::$config['module']['Mailer']['host'];
        self::$mail->Port           = 465;
        self::$mail->SMTPSecure     = 'ssl';
        self::$mail->SMTPAuth       = true;

        self::$mail->Username       = Core::$config['module']['Mailer']['senders'][$sender]['email'];
        self::$mail->Password       = Core::$config['module']['Mailer']['senders'][$sender]['password'];
        self::$mail->setFrom(

            Core::$config['module']['Mailer']['senders'][$sender]['email'],
            Core::$config['module']['Mailer']['senders'][$sender]['name']
        );

        foreach (self::$recipients as $recipient) {

            self::$mail->addAddress(
                $recipient[0],
                $recipient[1]
            );
        }

        self::$mail->Subject = $subject;
        self::$mail->Body    = $mail_body;

        if (!self::$mail->send()) {

            $ret = self::ERROR_CODE;
            self::$info = self::$mail->ErrorInfo;

        } else {

            $ret = self::SUCCESS_CODE;
            self::$info = 'success';
        }

        return $ret;
    }

    public static function addRecipient($email, $name)
    {
        self::$recipients[] = [$email, $name];
    }

    public static function attach($file)
    {
        self::$mail->addAttachment($file);
    }

}