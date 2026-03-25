<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }} - New Facility Created</title>
</head>

<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 0; margin: 0;">
<table align="center" cellpadding="0" cellspacing="0" width="100%">
    <tr>
        <td style="padding: 20px;">
            <table align="center" cellpadding="0" cellspacing="0" width="600"
                   style="background-color: #ffffff; border-radius: 8px; padding: 20px; margin-top: 30px;
                   box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);">

                <!-- Logo -->
                <tr>
                    <td align="center" style="padding: 20px 0;">
                        <img src="https://hfr.e4eweb.space/img/new_logo.png" alt="digifac" width="150">
                    </td>
                </tr>

                <!-- Header -->
                <tr>
                    <td align="center"
                        style="padding: 10px 30px; background: #10B981; border-radius: 4px; color: #fff;
                        font-size: 20px; text-align:center">
                        <h1 style="font-size: 20px; font-weight: 800; margin: 7px 0px; color: #ffffff; text-align:center">
                            Nigeria Health Facility Registry
                        </h1>
                    </td>
                </tr>

                <!-- Body -->
                <tr>
                    <td align="center" style="padding: 20px 30px;">
                        <h2 style="font-size: 20px; color: #333;">Hello!</h2>

                        <p style="font-size: 16px; color: #333;">
                            A new facility has been successfully created in DHIS2.
                        </p>

                        <p style="font-size: 16px; color: #333; text-align:left; margin-top: 10px;">
                            Below are the details:
                        </p>

                        <!-- Facility Details Box -->
                        <table width="100%" cellpadding="8"
                               style="font-size: 15px; background: #f8f9fa; border-radius: 8px; border: 1px solid #e0e0e0;">
                            <tr>
                                <td><strong>Facility Name:</strong></td>
                                <td>{{ $name }}</td>
                            </tr>
                            <tr>
                                <td><strong>State:</strong></td>
                                <td>{{ $state }}</td>
                            </tr>
                            <tr>
                                <td><strong>LGA:</strong></td>
                                <td>{{ $lga }}</td>
                            </tr>
                            <tr>
                                <td><strong>Ward:</strong></td>
                                <td>{{ $ward }}</td>
                            </tr>
                        </table>

                        <p style="font-size: 16px; color: #333; margin-top: 20px;">
                            You may review the exchange logs for more information.
                        </p>

                        <!-- Action Button -->
                        <a href="{{ $actionUrl }}"
                           style="display: inline-block; padding: 10px 20px; color: #ffffff; background-color: #10B981;
                           border-radius: 6px; text-decoration: none; font-weight: bold;">
                            View Logs
                        </a>

                        <p style="margin-top: 20px;">
                            If you’re unable to click the button, copy the URL below into your browser:
                        </p>

                        <div
                            style="font-size: 12px; font-weight: bold; background-color: #f8f9fa; padding: 15px;
                            text-align: left; border-radius: 8px; border: 1px dashed #10B981; color: #10B981;">
                            <p>{{ $actionUrl }}</p>
                        </div>

                        <p style="margin-top: 20px;">
                            Kindly take appropriate action at your end.
                        </p>
                    </td>
                </tr>

                <!-- Footer -->
                <tr>
                    <td align="center"
                        style="padding: 20px 30px; font-size: 14px; color: #6c757d; text-align: center;">
                        Regards,<br>
                        <p>Nigeria Health Facility Registry</p>
                    </td>
                </tr>

            </table>
        </td>
    </tr>
</table>
</body>

</html>
