<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #dc3545; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { background: #f9f9f9; padding: 24px; border: 1px solid #e0e0e0; }
        .reason-box { background: #fff3cd; border-left: 4px solid #ffc107; padding: 12px 16px; margin: 16px 0; }
        .footer { text-align: center; padding: 16px; color: #888; font-size: 12px; }
    </style>
</head>
<body>
    <table align="center" cellpadding="0" cellspacing="0" width="100%">
            <tr>
                <td style="padding: 20px">
                    <table align="center" cellpadding="0" cellspacing="0" width="600"
                        style="
                                background-color: #ffffff;
                                border-radius: 8px;
                                padding: 20px;
                                margin-top: 30px;
                                box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
                    ">
                        <tr>
                            <td align="center" style="padding: 20px 0">
                                <img src="https://hfr.e4eweb.space/img/new_logo.png" alt="digifac" width="150" />
                            </td>
                        </tr>


                    </table>
                </td>
            </tr>
    </table>
    <div class="header">
        <h1 style="margin:0;">API Key Request Update</h1>
        <p style="margin:5px 0 0;">Nigeria Health Facility Registry</p>
    </div>

    <div class="content">
        <p>Dear <strong>{{ $client->name }}</strong>,</p>

        <p>Thank you for your interest in the HFR API. Unfortunately, your API key request has not been approved at this time.</p>

        @if($client->rejection_reason)
            <div class="reason-box">
                <strong>Reason:</strong><br>
                {{ $client->rejection_reason }}
            </div>
        @endif

        <p>If you believe this was in error or would like to discuss your use case further, please contact the HFR administrator.</p>
    </div>

    <div class="footer">
        <p>This is an automated message from the HFR API system. Do not reply to this email.</p>
    </div>
</body>
</html>
