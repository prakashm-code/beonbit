<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Email Verified</title>

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f8;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }

        .card {
            background: #ffffff;
            padding: 30px;
            max-width: 420px;
            width: 100%;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            text-align: center;
        }

        .icon {
            font-size: 60px;
            color: #28a745;
        }

        h2 {
            margin-top: 15px;
            color: #333;
        }

        p {
            color: #666;
            margin: 10px 0 25px;
        }

        .btn {
            display: inline-block;
            padding: 12px 25px;
            background: #28a745;
            color: #ffffff;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }

        .btn:hover {
            background: #218838;
        }
    </style>
</head>
<body>

    <div class="card">
        <div class="icon">✅</div>

        <h2>Email Verified Successfully</h2>

        <p>
            Thank you! Your email address has been verified.
            You can now log in and start using the application.
        </p>

        {{-- <a href="{{ url('/login') }}" class="btn">
            Go to Login
        </a> --}}
    </div>

</body>
</html>
