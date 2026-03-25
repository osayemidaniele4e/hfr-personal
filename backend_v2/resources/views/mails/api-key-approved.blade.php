<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #1a7a3a; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { background: #f9f9f9; padding: 24px; border: 1px solid #e0e0e0; }
        .api-key-box { background: #1a1a2e; color: #00ff88; padding: 16px; border-radius: 6px; font-family: 'Courier New', monospace; font-size: 14px; word-break: break-all; margin: 16px 0; }
        .warning { background: #fff3cd; border: 1px solid #ffc107; padding: 12px; border-radius: 4px; margin: 16px 0; }
        .footer { text-align: center; padding: 16px; color: #888; font-size: 12px; }
        .info-table td { padding: 6px 12px; }
        .info-table td:first-child { font-weight: bold; color: #555; }
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
        <h1 style="margin:0;">🎉 API Key Approved</h1>
        <p style="margin:5px 0 0;">Nigeria Health Facility Registry</p>
    </div>

    <div class="content">
        <p>Dear <strong>{{ $client->name }}</strong>,</p>

        <p>Great news! Your request for an HFR API key has been <strong>approved</strong>. You can now access Nigeria's Health Facility Registry data programmatically.</p>

        <h3>Your API Key</h3>
        <div class="api-key-box">{{ $plainKey }}</div>

        <div class="warning">
            <strong>⚠ Important:</strong> This is the only time your full API key will be shown. Store it securely. If you lose it, you will need to request a new key.
        </div>

        <h3>Your Account Details</h3>
        <table class="info-table">
            <tr><td>Name:</td><td>{{ $client->name }}</td></tr>
            <tr><td>Email:</td><td>{{ $client->email }}</td></tr>
            @if($client->organisation)<tr><td>Organisation:</td><td>{{ $client->organisation }}</td></tr>@endif
            <tr><td>Rate Limit:</td><td>{{ $client->rate_limit }} requests/minute</td></tr>
            @if($client->expires_at)<tr><td>Expires:</td><td>{{ $client->expires_at->format('d M Y') }}</td></tr>@endif
        </table>

        <h3>How to Use</h3>
        <p>Include your API key in the <code>X-API-Key</code> header with every request:</p>
        <div class="api-key-box" style="color:#ccc;">
            curl -H "X-API-Key: {{ $plainKey }}" \<br>
            &nbsp;&nbsp;{{ url('/api/v1/facilities') }}
        </div>

        <p>📖 Full documentation: <a href="{{ url('/developers') }}">{{ url('/developers') }}</a></p>
    </div>

    <div class="footer">
        <p>This is an automated message from the HFR API system. Do not reply to this email.</p>
    </div>
</body>
</html>
