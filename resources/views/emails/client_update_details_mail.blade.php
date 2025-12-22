<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Profile Has Been Updated!</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 0;">

<div style="width: 90%; max-width: 600px; margin: 20px auto; background-color: #ffffff; padding: 20px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">
    <!-- Email Header -->
    <div style="text-align: center; padding-bottom: 20px;">
        <img src="{{ asset('admin_assets/images/logo.svg') }}" alt="Your Brand Logo" style="max-width: 150px; height: auto;">
        <h1 style="color: #53a9b4; font-size: 24px; margin: 10px 0 0;">Your Profile Has Been Updated!</h1>
    </div>

    <!-- Email Body -->
    <div style="color: #555555; font-size: 16px; line-height: 1.6;">
        <p>Hi {{ $user->first_name }} {{ $user->last_name }},</p>

        @if($user->profile_update )
       
        <p>We wanted to let you know that the following details of your profile have been updated:</p>

        <table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
            <thead>
                <tr>
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Field</th>
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Old Value</th>
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">New Value</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($user->updated_fields as $field => $values)
                    <tr>
                        <td style="border: 1px solid #ddd; padding: 8px;">{{ ucfirst(str_replace('_', ' ', $field)) }}</td>
                        <td style="border: 1px solid #ddd; padding: 8px;">{{ $values['old'] ?? 'N/A' }}</td>
                        <td style="border: 1px solid #ddd; padding: 8px;">{{ $values['new'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @else
        <p>We wanted to let you know that the your account has been <strong> @php echo $user->account_status == 1 ? 'Activated' : 'Deactivated'; @endphp </strong> by the admin.</p>       
        @endif
        {{-- <p>If you did not make these changes or any concerns, please contact our support team immediately.</p> --}}
        {{-- <p style="text-align: center;">
            <a href="{{ route('frontend.contact_us') }}" style="display: inline-block; background-color: #53a9b4; color: #ffffff; padding: 10px 20px; text-decoration: none; border-radius: 5px; font-size: 16px; margin-top: 20px;" target="_blank" rel="noopener">Contact Support</a>
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
