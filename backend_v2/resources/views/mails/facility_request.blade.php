<!doctype html>
<html lang="en">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name') }} - Facility Verification</title>
    </head>

    <body style="font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 0; margin: 0;">
        <table align="center" cellpadding="0" cellspacing="0" width="100%">
            <tr>
                <td style="padding: 20px;">
                    <table align="center" cellpadding="0" cellspacing="0" width="600"
                        style="background-color: #ffffff; border-radius: 8px; padding: 20px; margin-top: 30px; box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);">
                        <tr>
                            <td align="center" style="padding: 20px 0;">
                                <img src="https://hfr.e4eweb.space/img/new_logo.png" alt="digifac" width="150">
                            </td>
                        </tr>
                        <tr>
                            <td align="center"
                                style="padding: 10px 30px; background: #10B981; border-radius: 4px; color: #fff; font-size: 20px; text-align:center">
                                <h1
                                    style="font-size: 20px; font-weight: 800; margin: 7px 0px; color: #ffffff; text-align:center">
                                    Nigeria Health Facility Registry
                                </h1>
                            </td>
                        </tr>
                        <tr>
                            <td align="center" style="padding: 10px 30px;">

                                <h2 style="font-size: 20px; color: #333;">Hello!
                                </h2>
                                <p style="font-size: 16px; color: #333;">
                                    New facility have been created. Please login to the system to review and verify the
                                    request.
                                </p>

                                <a href="{{ $url }}"
                                    style="display: inline-block; padding: 10px 20px; color: #ffffff; background-color: #10B981; border-radius: 6px; text-decoration: none; font-weight: bold;">
                                    Verify Now
                                </a>

                                <p>
                                    If you’re having trouble clicking the "Verify Now" button, copy and paste the
                                    URL
                                    below into your web browser:
                                </p>

                                <div
                                    style="font-size: 12px; font-weight: bold; background-color: #f8f9fa; padding: 15px; text-align: left; border-radius: 8px; border: 1px dashed #10B981; color: #10B981;">
                                    <p>
                                        {{ $url }}
                                    </p>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td align="center" bgcolor="#f4f4f4" style="padding: 20px; font-size: 12px; color: #aaaaaa">
                                &copy; {{ date('Y') }} {{ config('app.name') }}. All rights
                                reserved.
                            </td>
                        </tr>

                    </table>
                </td>
            </tr>
        </table>
    </body>

</html>
