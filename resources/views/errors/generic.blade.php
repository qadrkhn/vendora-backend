<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>401 Unauthorized</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            text-align: center;
            background-color: #f8d7da;
            color: #721c24;
        }
        .container {
            max-width: 600px;
            margin: 50px auto;
            padding: 30px;
            background: white;
            border: 1px solid #f5c6cb;
            border-radius: 5px;
        }
        h1 {
            font-size: 3rem;
            margin-bottom: 20px;
        }
        p {
            font-size: 1.2rem;
        }
        a {
            text-decoration: none;
            color: #721c24;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>{{ $title ?? 'Access Denied' }}</h1>
        <p>{{ $message ?? 'You do not have permission to access this page.' }}</p>
        <p>If you believe this is an error, please contact the system administrator.</p>
        <p><a href="{{ url('/') }}"><u>Go Back to Home</u></a></p>
        <h4>OR</h4>
        <p><a href="{{ url('/login') }}"><u>Login</u></a></p>
    </div>
</body>
</html>
