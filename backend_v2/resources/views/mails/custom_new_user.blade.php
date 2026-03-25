<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>{{ config('app.name') }} - Account Created</title>
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
                                    Nigeria Health Facility Registry
                                </h1>
                            </td>
                        </tr>
                        <tr>
                            <td align="center" style="padding: 10px 30px">
                                <h2 style="margin-top: 0">Welcome, {{ $name }}!</h2>
                                <p>Your account has been created successfully.</p>

                                <!-- <p>
                  If you’re having trouble clicking the "Login" button,
                  copy and paste the URL below into your web browser:
                </p> -->

                                <p>
                                    You will be required to change your password before you
                                    proceed!
                                </p>


                                <table width="100%" cellpadding="0" cellspacing="0"
                                    style="
                    font-size: 12px;
                    font-family: Arial, sans-serif;
                    background-color: #f8f9fa;
                    border-radius: 8px;
                    border: 1px dashed #10b981;
                    color: #10b981;
                  ">
                                    <thead>
                                        <tr>
                                            <th colspan="2"
                                                style="
                          text-align: left;
                          padding: 15px;
                          font-weight: bold;
                          font-size: 14px;
                          border-bottom: 1px solid #10b981;
                        ">
                                                User Account Details
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td style="padding: 10px; font-weight: bold">Email:</td>
                                            <td style="padding: 10px">{{ $email }}</td>
                                        </tr>
                                        <tr>
                                            <td style="padding: 10px; font-weight: bold">
                                                Temporary Password:
                                            </td>
                                            <td style="padding: 10px">{{ $password }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>

                        <tr>
                            <td align="center" style="padding: 10px 30px">
                                <a href="{{ $loginUrl }}"
                                    style="
                    display: inline-block;
                    padding: 10px 20px;
                    color: #ffffff;
                    background-color: #10b981;
                    border-radius: 6px;
                    text-decoration: none;
                    font-weight: bold;
                  ">
                                    Login
                                </a>

                                <p style="margin-top: 30px; font-size: 14px; color: #888888">
                                    If you did not request this account, please contact support.
                                </p>
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
