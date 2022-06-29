<?php

class AboutModel extends AbstractModel
{
    public $feedback_fio;
    public $feedback_message;
    public $feedback_md5;
    public $feedback_captcha;

    public $feedback_captcha_test = '543966';
    public $feedback_status = 0;

    protected function dataProcess()
    {
        $this->feedback_fio     = isset($_POST['fio'])       ? $_POST['fio']      : '';
        $this->feedback_message = isset($_POST['message'])   ? $_POST['message']  : '';
        $this->feedback_md5     = isset($_POST['check'])     ? $_POST['check']    : '';
        $this->feedback_captcha = isset($_POST['captcha'])   ? $_POST['captcha']  : '';

        if (
            (!empty($this->feedback_md5))
            &&
            ($this->feedback_captcha == $this->feedback_captcha_test)
            &&
            (!empty($this->feedback_message))
        ) {

            $this->sendMessage();
            $this->feedback_status = 1;//отправлено

        } elseif (

            (!empty($this->feedback_md5))

        ) {
            $this->feedback_status = -1;//ошибка
        }
    }

    protected function sendMessage()
    {
        $message = '';

		$message .= 'Новое сообщение с сайта: georostgrupp.ru';

		$message .= '<b>Автор сообщения: </b>' . $this->feedback_fio . '<br>';
		$message .= '<b>Сообщение: </b>' . $this->feedback_message . '<br>';
		$message .= '</body></html>';

		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";

		$subject = "Новое сообщение с сайта: georostgrupp.ru";

		$mailto = "redreem@mail.ru";
		$setmail = mail($mailto, $subject, $message, $headers);
    }
}