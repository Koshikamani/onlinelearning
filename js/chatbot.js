function sendMessage() {
    const userInput = document.getElementById('user-input').value;
    if (userInput.trim() === "") return;

    // Display user's message
    displayMessage(userInput, 'user-message');

    // Clear input field
    document.getElementById('user-input').value = '';

    // Add typing indicator
    const chatWindow = document.getElementById('chat-window');
    const typingIndicator = document.createElement('div');
    typingIndicator.classList.add('chat-message', 'bot-message');
    typingIndicator.textContent = "...typing";
    typingIndicator.id = "typing-indicator";
    chatWindow.appendChild(typingIndicator);
    chatWindow.scrollTop = chatWindow.scrollHeight;

    // Call API to get bot response
    fetchResponse(userInput);
}

function displayMessage(message, className) {
    const chatWindow = document.getElementById('chat-window');
    const messageDiv = document.createElement('div');
    messageDiv.classList.add('chat-message', className);
    messageDiv.textContent = message;
    chatWindow.appendChild(messageDiv);

    // Auto-scroll to the bottom
    chatWindow.scrollTop = chatWindow.scrollHeight;
}

function fetchResponse(userInput) {
    fetch('chatbot.php', { // PHP endpoint
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ prompt: userInput })
    })
    .then(response => response.json())
    .then(data => {
        // Remove typing indicator
        document.getElementById('typing-indicator').remove();

        // Display bot's response
        const botMessage = data.reply || "Sorry, I didn't understand that.";
        displayMessage(botMessage, 'bot-message');

        // Optionally, save to message history
        saveMessage(userInput, botMessage);
    })
    .catch(error => {
        // Remove typing indicator
        document.getElementById('typing-indicator').remove();

        displayMessage("Error: Unable to fetch response.", 'bot-message');
        console.error('Error:', error);
    });
}

function toggleTheme() {
    const body = document.body;
    body.classList.toggle('dark');

    const chatContainer = document.querySelector('.chat-container');
    chatContainer.classList.toggle('dark');

    const messages = document.querySelectorAll('.chat-message');
    messages.forEach(message => message.classList.toggle('dark'));

    const themeToggleBtn = document.getElementById('theme-toggle-btn');
    themeToggleBtn.textContent = body.classList.contains('dark') 
        ? "Switch to Light Mode" 
        : "Switch to Dark Mode";
}

function saveMessage(userMessage, botMessage) {
    fetch('save_message.php', { // PHP backend to save message
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            user_message: userMessage,
            bot_message: botMessage
        })
    });
}

// Load previous messages on page load
window.onload = function() {
    fetch('get_messages.php')
    .then(response => response.json())
    .then(data => {
        const chatWindow = document.getElementById('chat-window');
        data.forEach(message => {
            const className = message.role === 'user' ? 'user-message' : 'bot-message';
            displayMessage(message.content, className);
        });
    });
};
