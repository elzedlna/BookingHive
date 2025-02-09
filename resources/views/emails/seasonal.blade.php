<div style="font-family: Helvetica, sans-serif;">
    <h2>Hi {{ $user->name }},</h2>

    <p>We have some exciting travel deals just in time for the upcoming holiday season!</p>

    <p><strong>Check out our exclusive offers:</strong></p>

    <p>{{ $promoDetails }}</p>

    <p>Don't miss out, book your stay now!</p>

    <a href="{{ url('https://bookinghive.site/login') }}"
        style="background-color: #1e90ff; color: white; padding: 10px 20px; text-decoration: none; font-family: Helvetica; border-radius: 5px;">
        View Deals</a>

    <p>Happy travels<br>Booking Hive</p>
</div>
