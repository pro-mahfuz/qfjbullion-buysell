
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Relation - Shadhin Gold</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; padding: 20px; }
        h1 { color: #d4af37; }
        .container { max-width: 800px; margin: auto; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Account Relation</h1>
        <p>Your Shadhin Gold account provides access to features like transaction tracking, profile management, and secure login.</p>
        <h2>1. Account Creation</h2>
        <p>Users must register with valid details and agree to our policies.</p>
        <h2>2. Security Responsibilities</h2>
        <p>Users must keep their login credentials secure and notify us in case of suspicious activity.</p>
        <h2>3. Account Deletion</h2>
        <p>Users can request account deletion by filling out the form below. Upon submission, we will process your request within 7 business days.</p>
        <form action="/delete-account" method="POST">
            <label for="email">Enter your registered email:</label>
            <input type="email" id="email" name="email" required>
            <br><br>
            <label for="reason">Reason for account deletion:</label>
            <textarea id="reason" name="reason" rows="4" required></textarea>
            <br><br>
            <button type="submit">Request Account Deletion</button>
        </form>
        <h2>4. Support</h2>
        <p>For any account-related issues, contact our support team at support@shadhingold.com.</p>
    </div>
</body>
</html>
