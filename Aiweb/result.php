<?php
$api_key = "xxxxxx";

// รับข้อความจากฟอร์ม
$user_input = $_POST["message"] ?? "";
$image_data = $_FILES["image"]["tmp_name"] ?? null;

$image_base64 = null;
if ($image_data && file_exists($image_data)) {
    $image_content = file_get_contents($image_data);
    $image_base64 = base64_encode($image_content);
}

// เตรียม payload
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
  $payload["messages"][] = ["role" => "user", "content" => $user_input];
}

// เรียก OpenAI API
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

$reply = $result["choices"][0]["message"]["content"] ?? "Error from GPT-4o.";
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>GPT Response</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 p-6">
  <div class="max-w-2xl mx-auto bg-white p-6 rounded shadow">
    <h1 class="text-2xl font-bold mb-4">GPT-4o Response</h1>
    <p class="mb-4"><strong>You:</strong> <?= htmlspecialchars($user_input) ?></p>
    <p class="bg-gray-100 p-4 rounded"><strong>GPT:</strong> <?= nl2br(htmlspecialchars($reply)) ?></p>
    <a href="index.php" class="inline-block mt-6 text-purple-600 hover:underline">← Back to Chat</a>
  </div>
</body>
</html>
