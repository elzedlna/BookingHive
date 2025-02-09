<!DOCTYPE html>
<html>
<head>
    <title>Login Reminder</title>
</head>
<body>
    <h1>Hello {{ $user->name }}!</h1>
    
    <p>We noticed you haven't visited us in a while. We miss you!</p>
    
    <p>Log in to your account to see what's new and explore our latest offerings.</p>
    <a href="{{ url('https://bookinghive.site/login') }}"
        style="background-color: #1e90ff; color: white; padding: 10px 20px; text-decoration: none; font-family: Helvetica; border-radius: 5px;">
        Login</a>
    
    <p>Best regards,<br>
    BookingHive</p>
</body>
</html> 