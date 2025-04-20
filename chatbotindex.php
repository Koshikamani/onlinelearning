<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Chatbot</title>

   <!-- Font Awesome CDN link -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- Custom CSS link -->
   <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include 'components/user_header.php'; ?>

<!-- Chatbot Section Starts -->
<section class="chatbot">
   <h1 class="heading">Chat with Our Educational Assistant</h1>

   <div class="welcome-message">
      <?php if (isset($user)): ?>
        <p style="font-size: 50px; font-weight: 600; color: #2c3e50;">Hello, <?= htmlspecialchars($user['name']); ?>! How can I assist you today?</p>
      <?php endif; ?>
   </div>

   <div class="chat-container" id="chat-container">
      
   </div>

   <div class="chat-input">
      <textarea id="user-input" placeholder="Type your message here..."></textarea>
      <button onclick="sendMessage()">Send</button>
   </div>
</section>
<!-- Chatbot Section Ends -->

<?php include 'components/footer.php'; ?>

<!-- Custom JS link -->
<script src="js/chatbot.js"></script>
<script src="js/script.js"></script>

</body>
</html>
