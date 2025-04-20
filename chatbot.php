
<?php
// Backend logic to handle OpenAI API calls
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $apiKey =""; // Replace with your OpenAI API key

    // Read and decode the incoming JSON data
    $requestData = json_decode(file_get_contents('php://input'), true);

    // Check if a prompt is provided
    if (!isset($requestData['prompt'])) {
        echo json_encode(['error' => 'No prompt provided']);
        exit;
    }

    $prompt = $requestData['prompt'];

    // Prepare the OpenAI API request payload
    $data = [
        "model" => "gpt-3.5-turbo",
        "messages" => [
            ["role" => "system", "content" => "You are a helpful educational assistant."],
            ["role" => "user", "content" => $prompt]
        ]
    ];

    // Initialize cURL for the OpenAI API request
    $ch = curl_init("https://api.openai.com/v1/chat/completions");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "Authorization: Bearer $apiKey"
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        echo json_encode(['error' => 'Curl error: ' . curl_error($ch)]);
        curl_close($ch);
        exit;
    }

    curl_close($ch);

    // Return the OpenAI API response
    $responseData = json_decode($response, true);

    // Check if the response contains the expected data
    if (isset($responseData['choices'][0]['message']['content'])) {
        echo json_encode(['reply' => $responseData['choices'][0]['message']['content']]);
    } else {
        // If 'choices' or 'message' is not found, return an error message
        echo json_encode(['error' => 'No response from OpenAI or API error']);
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chatbot</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .chat-container {
            width: 100%;
            max-width: 600px;
            margin: 50px auto;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .messages {
            max-height: 400px;
            overflow-y: auto;
            margin-bottom: 20px;
        }
        .message {
            margin-bottom: 10px;
        }
        .message.user {
            text-align: right;
            color: blue;
        }
        .message.bot {
            text-align: left;
            color: green;
        }
        textarea {
            width: calc(100% - 100px);
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        button {
            padding: 10px 20px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<div class="chat-container">
    <div class="messages" id="messages"></div>
    <div class="input-area">
        <textarea id="user-input" placeholder="Type your message here..."></textarea>
        <button onclick="sendMessage()">Send</button>
    </div>
</div>

<script>
    const messagesDiv = document.getElementById('messages');

    function appendMessage(content, role) {
        const messageDiv = document.createElement('div');
        messageDiv.classList.add('message', role);
        messageDiv.textContent = content;
        messagesDiv.appendChild(messageDiv);
        messagesDiv.scrollTop = messagesDiv.scrollHeight;
    }

    async function sendMessage() {
    const userInput = document.getElementById('user-input');
    const prompt = userInput.value.trim();
    if (!prompt) return;

    // Display the user's message
    appendMessage(prompt, 'user');
    userInput.value = '';

    // Display a typing indicator
    appendMessage('Typing...', 'bot');

    try {
        // Send the message to the backend
        const response = await fetch('', { // Same file handles requests
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ prompt }),
        });

        const data = await response.json();

        if (data.error) {
            // Handle any error in the response
            console.error('API Error:', data.error);
            appendMessage('Error occurred: ' + data.error, 'bot');
        } else {
            // Safely handle the bot's response
            const botReply = data.reply || 'Sorry, I didn\'t understand that.';
            
            // Remove the typing indicator
            messagesDiv.lastChild.remove();

            // Display the bot's response
            appendMessage(botReply, 'bot');
        }
    } catch (error) {
        console.error('Error:', error);
        appendMessage('Error occurred. Please try again.', 'bot');
    }
}
</script>

</body>
</html>
