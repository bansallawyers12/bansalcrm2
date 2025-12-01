<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            background-color: #f5f5f5;
        }
        .container {
            text-align: center;
            background: white;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .success {
            color: #28a745;
            font-size: 24px;
            margin-bottom: 20px;
        }
        .error {
            color: #dc3545;
            font-size: 24px;
            margin-bottom: 20px;
        }
        .message {
            font-size: 18px;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
    <div class="container">
        @if(session('success'))
            <div class="success">✓ Success</div>
            <div class="message">{{ session('success') }}</div>
        @elseif(session('error'))
            <div class="error">✗ Error</div>
            <div class="message">{{ session('error') }}</div>
        @else
            <div class="success">Thank You!</div>
            <div class="message">Your request has been processed successfully.</div>
        @endif
    </div>
</body>
</html>

