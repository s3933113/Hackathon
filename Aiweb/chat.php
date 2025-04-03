<?php

$api_key = "xxxxx";

$user_input = $_POST['message'] ?? '';

if (!$user_input) {
    echo "No input received.";
    exit;
}

$data = [
    "model" => "gpt-4",
    "messages" => [
        ["role" => "user", "content" => $user_input]
    ],
    "temperature" => 0.7
];

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, "https://api.openai.com/v1/chat/completions");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer $api_key",
    "Content-Type: application/json"
]);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

$response = curl_exec($ch);
curl_close($ch);

//่json
$result = json_decode($response, true);

echo $result["choices"][0]["message"]["content"] ?? "Error contacting GPT-4.";