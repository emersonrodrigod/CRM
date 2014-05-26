<?php

class Util {

    public static function dataMysql($data) {
        $dt = trim($data);

        if (strstr($dt, "/")) {

            $aux2 = explode("/", $dt);

            $datai2 = $aux2[2] . "-" . $aux2[1] . "-" . $aux2[0];

            return $datai2;
        }
    }

    public function sendMail($subject, $body, $from = array()) {

        $mail = new Zend_Mail('utf-8');

        $config ['username'] = 'informatica@vicentinos.com.br'; // informa o login do E-mail
        $config ['password'] = 'vicentinos.inf2013'; // senha
        //$config ['ssl'] = 'tls';
        //$config ['port'] = '587';

        $config ['auth'] = 'login';

        $smtp = new Zend_Mail_Transport_Smtp("mail.vicentinos.com.br", $config);
        $mail->setBodyHtml($body);

        foreach ($from as $f) {
            $mail->addTo($f);
        }

        $mail->setSubject($subject);
        $mail->setFrom('informatica@vicentinos.com.br', 'SSI Vicentino´s');
        $mail->send($smtp);
    }

}
