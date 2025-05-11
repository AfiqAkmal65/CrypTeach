<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo "Access denied";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_message = $_POST['message'] ?? '';

    $api_key = 'AIzaSyCitJXSJ9G7mAIA1-oMVh-_rSjmbShjU08'; // Replace with your Gemini key

    $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key=" . $api_key;

    $data = [
        "contents" => [
            [
                "parts" => [
                    ["text" => $user_message]
                ]
            ]
        ]
    ];

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json'
        ],
        CURLOPT_POSTFIELDS => json_encode($data)
    ]);

    $response = curl_exec($ch);
    $err = curl_error($ch);
    curl_close($ch);

    if ($err) {
        echo "cURL Error: $err";
    } else {
        $json = json_decode($response, true);
        if (isset($json['candidates'][0]['content']['parts'][0]['text'])) {
            echo $json['candidates'][0]['content']['parts'][0]['text'];
        } else {
            echo "CrypBot couldn't answer that. [Debug: " . json_encode($json) . "]";
        }
    }
    exit();
}
?>
