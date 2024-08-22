<?php

error_reporting(0);
require __DIR__ . '/Main/Init.php';

/** Req-1 */
$resp = $curl->post(
    '',
    '',
    []
);

$message = $init->parseString($resp, '<li>Error Processing Credit Card - ', '</li>');

/** Set Response */
if (strpos($resp, "Your donation has been completed!")) {
    $curl->setResponse('Approved', "Transaction Approved:3.00$");
} else {
    $curl->setResponse('Declined', $message);
}

/** Delete Cookie */
$curl->deleteCookie();