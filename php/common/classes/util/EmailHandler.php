<?php
require_once(CLASSES_ROOT_PATH . 'bo' . DS . 'mail' . DS . 'Email.php');

class EmailHandler {

    /**
     * @param $name
     * @param $email
     * @param $interested
     * @param $goal
     * @param $phone
     * @throws SystemException
     */
    static function sendFitnessHouse($name, $email, $interested, $goal, $phone) {
        $systemEmailAdrs = SettingsHandler::getSettingValueByKey(Setting::EMAILS);
        $basicAdr = explode(';', $systemEmailAdrs)[0];

        $email_subject = "Fitness House Contact from: " . $name;
        $email_body = "Name: " . $name . "\n";
        $email_body .= "Email: " . $email . "\n\n";
        $email_body .= "Phone: " . $phone . "\n\n";
        if (!empty($goal)) {
            $email_body .= "\tGoals: \n \t " . $goal . "\n\n";
        }
        $email_body .= "\tInterested in : \n \t" . $interested . "\n";
        $headers = "MIME-Version: 1.1\r\n";
        $headers .= "Content-type: text/plain; charset=utf-8\r\n";
        $headers .= "From:" . $basicAdr . "\r\n";
        $headers .= "Reply-To:" . $email . "\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
        $headers .= "Date: " . date(DEFAULT_DATE_FORMAT);

        $email = Email::createFull($basicAdr, $basicAdr, $email_subject, $email_body, $headers);
        self::sendEmail($email);
    }

    /**
     * @param $from
     * @param $to
     * @param $subject
     * @param $body
     * @param $headers
     */
    static function send($from, $to, $subject, $body, $headers) {
        $email = Email::createFull($from, $to, $subject, $body, $headers);
        self::sendEmail($email);
    }

    /**
     * @param Email $email
     */
    static function sendEmail($email) {
        mail($email->getTo(), '=?utf-8?B?' . base64_encode($email->getSubject()) . '?=', wordwrap($email->getBody(), 70), $email->getHeaders());
    }

}