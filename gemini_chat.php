<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userMessage = $_POST['message'];

    // Your Gemini API Key
    $apiKey = 'AIzaSyCitJXSJ9G7mAIA1-oMVh-_rSjmbShjU08';

    // Gemini 1.5 Flash endpoint
    $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=$apiKey";

    // Send initial context + user message
    $postData = json_encode([
        'contents' => [
            [
                'role' => 'user',
                'parts' => [[
                    'text' => "You are CrypTeach AI, a helpful assistant in a cryptography learning platform called CrypTeach. Always answer as the official AI tutor."
                ]]
            ],
            [
                'role' => 'user',
                'parts' => [[ 'text' => $userMessage ]]
            ]
        ]
    ]);

    // cURL setup
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        echo json_encode(['error' => 'Curl error: ' . curl_error($ch)]);
    } else {
        $json = json_decode($response, true);
        $reply = $json['candidates'][0]['content']['parts'][0]['text'] ?? '[No response from Gemini]';
        echo $reply;
    }

    curl_close($ch);
}
?>
