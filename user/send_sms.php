<?php
function sendSMS($message, $phone_number) {
    // Replace with your SMS API integration
    $api_url = "https://sms-api.example.com/send"; // Example SMS API URL
    $api_key = "your_api_key"; // Your SMS API Key

    $post_fields = [
        'to' => $phone_number,
        'message' => $message,
        'api_key' => $api_key,
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_fields));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    curl_close($ch);

    return $response !== false;
}
?>
