<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Provider Account Status</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 0;">

<div style="width: 90%; max-width: 600px; margin: 20px auto; background-color: #ffffff; padding: 20px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">
    <!-- Email Header -->
    <div style="text-align: center; padding-bottom: 20px;">
        <img src="{{ asset('admin_assets/images/logo.svg') }}" alt="Your Brand Logo" style="max-width: 150px; height: auto;">
        <h1 style="color: #53a9b4; font-size: 24px; margin: 10px 0 0;">Your Provider Account Status</h1>
    </div>

    <!-- Email Body -->
    <div style="color: #555555; font-size: 16px; line-height: 1.6;">
        <p>Hi {{ $data['name'] }} ,</p>

       
        @if($data['status'] == 'Approved') 
            <p>We are pleased to inform you that your provider account has been <strong>approved</strong>!</p>
            <p>Welcome aboard! You can now start providing services to our platform.</p>
            <p>If you have any questions or need assistance getting started, feel free to reach out to our support team.</p>
            <p>Thank you for being a part of our community!</p>

        @elseif($data['status'] == 'Rejected')
            <p>We regret to inform you that your provider account has been <strong>rejected</strong>.</p>
            <p>Reason for rejection: <strong>{{ $data['reason'] }}</strong></p>
            <p>We understand this might be disappointing, and we encourage you to reach out to our support team if you have any questions or would like to know more about the reason for this decision.</p>
            <p>If you wish to apply again in the future, we will be happy to reconsider your application after you have made the necessary improvements.</p>
        @else
            <p>Your account status is currently under review. We will notify you once a decision has been made regarding your application.</p>
        @endif

        <p>If you have any questions, please contact our support team:</p>
        {{-- <p style="text-align: center;">
            <a href="#" style="display: inline-block; background-color: #53a9b4; color: #ffffff; padding: 10px 20px; text-decoration: none; border-radius: 5px; font-size: 16px; margin-top: 20px;" target="_blank" rel="noopener">Contact Support</a>
        </p> --}}

        <p>Thank you,<br>{{ config('app.name') }} Support Team</p>
    </div>

    <div style="text-align: center; font-size: 14px; color: #777777; margin-top: 40px;">
        <p>&copy; {{ date('Y') }}<br>
            {{ config('app.name') }} - All Rights Reserved.
        </p>
    </div>
</div>

</body>
</html>
