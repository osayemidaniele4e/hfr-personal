<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>{{ config('app.name') }} - Feedback</title>
    </head>

    <body
        style="
      font-family: Arial, sans-serif;
      background-color: #f4f4f4;
      padding: 0;
      margin: 0;
    ">
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
                        <tr>
                            <td align="center"
                                style="
                  padding: 10px 30px;
                  background: #10b981;
                  border-radius: 4px;
                  color: #fff;
                  font-size: 20px;
                  text-align: center;
                ">
                                <h1
                                    style="
                    font-size: 20px;
                    font-weight: 800;
                    margin: 7px 0px;
                    color: #ffffff;
                    text-align: center;
                  ">
                                    <!-- Nigeria Health Facility Registry -->
                                    New Contact Message
                                </h1>
                            </td>
                        </tr>
                        <tr>

                            <td align="center" style="padding: 10px 30px">
                                <h2 style="font-size: 20px; color: #333">Hello!</h2>
                                <!-- <p style="font-size: 16px; color: #333;">
                                {{ $contact->message }}
                            </p> -->

                                <p><strong>Name:</strong> {{ $contact->full_name }}</p>
                                <p><strong>Email:</strong> {{ $contact->email }}</p>
                                <p><strong>Subject:</strong> {{ $contact->subject }}</p>
                                <p><strong>Message:</strong> {{ $contact->message }}</p>

                                <a href="{{ env('APP_URL') }}"
                                    style="
                    display: inline-block;
                    padding: 10px 20px;
                    color: #ffffff;
                    background-color: #10b981;
                    border-radius: 6px;
                    text-decoration: none;
                    font-weight: bold;
                  ">
                                    Home
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td align="center"
                                style="
                  padding: 20px 30px;
                  font-size: 14px;
                  color: #6c757d;
                  text-align: center;
                  margin-top: 20px;
                ">
                                Regards,
                                <br />
                                <p>Nigeria Health Facility</p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </body>

</html>
