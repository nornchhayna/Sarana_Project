<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Verification Code</title>
</head>

<body style="margin:0; padding:0; background-color:#f4f6f9; font-family:Arial, Helvetica, sans-serif;">

    <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f6f9; padding:40px 0;">
        <tr>
            <td align="center">

                <table width="600" cellpadding="0" cellspacing="0"
                    style="background:#ffffff; border-radius:16px; overflow:hidden; box-shadow:0 4px 20px rgba(0,0,0,0.08);">

                    <!-- Header -->
                    <tr>
                        <td align="center" style="background:#111827; padding:30px 20px;">

                            <h1 style="color:#ffffff; margin:0; font-size:28px;">
                                {{ config('app.name') }}
                            </h1>

                            <p style="color:#d1d5db; margin-top:8px; font-size:14px;">
                                Secure Verification System
                            </p>
                        </td>
                    </tr>

                    <!-- Content -->
                    <tr>
                        <td style="padding:40px 35px;">

                            <h2 style="margin:0; color:#111827; font-size:24px;">
                                Verification Code
                            </h2>

                            <p style="margin-top:20px; color:#4b5563; font-size:16px; line-height:1.7;">
                                Hello,
                                <br><br>
                                Use the verification code below to continue your request.
                                This code is valid for
                                <strong>{{ $expires }} minutes</strong>.
                            </p>

                            <!-- OTP Box -->
                            <div style="margin:35px 0; text-align:center;">

                                <div style="
                                    display:inline-block;
                                    background:#f3f4f6;
                                    border:2px dashed #d1d5db;
                                    border-radius:14px;
                                    padding:20px 40px;
                                ">

                                    <span style="
                                        font-size:38px;
                                        letter-spacing:10px;
                                        font-weight:bold;
                                        color:#111827;
                                    ">
                                        {{ $otp }}
                                    </span>

                                </div>

                            </div>

                            <p style="color:#6b7280; font-size:14px; line-height:1.7;">
                                If you did not request this code, please ignore this email.
                                Never share your OTP with anyone for security reasons.
                            </p>

                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td align="center" style="background:#f9fafb; padding:25px; border-top:1px solid #e5e7eb;">

                            <p style="margin:0; color:#9ca3af; font-size:13px;">
                                © {{ date('Y') }} {{ config('app.name') }}.
                                All rights reserved.
                            </p>

                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>

</body>

</html>