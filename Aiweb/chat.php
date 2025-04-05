<?php

$api_key = "your_openai_key_here";

$data = json_decode(file_get_contents("php://input"), true);

$user_input = $data["message"] ?? '';
$image_base64 = $data["image"] ?? null;

$messages = [["role" => "user", "content" => $user_input]];
$payload = [
  "model" => "gpt-4o",
  "messages" => [],
  "temperature" => 0.7
];

if ($image_base64) {
  $payload["messages"][] = [
    "role" => "user",
    "content" => [
      ["type" => "text", "text" => $user_input],
      ["type" => "image_url", "image_url" => ["url" => "data:image/jpeg;base64," . $image_base64]]
    ]
  ];
} else {
  $payload["messages"] = [["role" => "user", "content" => $user_input]];
}

$ch = curl_init("https://api.openai.com/v1/chat/completions");
curl_setopt_array($ch, [
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_POST => true,
  CURLOPT_HTTPHEADER => [
    "Content-Type: application/json",
    "Authorization: Bearer $api_key"
  ],
  CURLOPT_POSTFIELDS => json_encode($payload)
]);

$response = curl_exec($ch);
curl_close($ch);

$result = json_decode($response, true);
echo $result["choices"][0]["message"]["content"] ?? "Error from GPT-4o.";
