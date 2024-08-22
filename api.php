<?php
header('Content-Type: application/json');

// Get the JSON input from the POST request
$data = json_decode(file_get_contents('php://input'), true);

$cc_list = $data['cc_list'] ?? [];

// Initialize an array to hold the results
$results = [];

foreach ($cc_list as $cc_entry) {
    // Split the card details
    $card_data = explode('|', $cc_entry);
    
    if (count($card_data) != 4) {
        $results[] = ['message' => 'Invalid format: ' . $cc_entry];
        continue;
    }

    $cc_number = trim($card_data[0]);
    $cc_expiry_month = trim($card_data[1]);
    $cc_expiry_year = trim($card_data[2]);
    $cc_cvv = trim($card_data[3]);

    // Validate the card data
    if (validate_card($cc_number, $cc_expiry_month, $cc_expiry_year, $cc_cvv)) {
        $results[] = ['message' => 'Card is valid: ' . $cc_number];
    } else {
        $results[] = ['message' => 'Card is invalid: ' . $cc_number];
    }
}

echo json_encode($results);

function validate_card($cc_number, $cc_expiry_month, $cc_expiry_year, $cc_cvv) {
    // Example simple validation
    if (strlen($cc_number) != 16 || strlen($cc_cvv) != 3 || !preg_match('/^\d{2}$/', $cc_expiry_month) || !preg_match('/^\d{2}$/', $cc_expiry_year)) {
        return false;
    }

    // In a real scenario, you'd call an API or check against a database
    return true;
}
?>
