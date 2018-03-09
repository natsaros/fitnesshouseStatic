<?php
try {
    $headers = "MIME-Version: 1.1\r\n";
    $headers .= "Content-type: text/html; charset=iso-8859-7\r\n";
	$headers .= "From: info@sellinofos.gr\r\n";
	$headers .= "Reply-To: info@sellinofos.gr\r\n";
	$headers .= "Return-Path: info@sellinofos.gr\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
    $headers .= "Date: " . date(DEFAULT_DATE_FORMAT);
    $email = Email::createFull('info@sellinofos.gr', 'nkatsiaounis@vertoyo.com', 'TEST', file_get_contents("./assets/emailTemplates/sellinofos_newsletter.htm"), $headers);
    EmailHandler::sendEmail($email);
} catch (SystemException $ex) {
    logError($ex);
    addErrorMessage(ErrorMessages::GENERIC_ERROR_GR);
}

Redirect(getAdminRequestUri() . "products");