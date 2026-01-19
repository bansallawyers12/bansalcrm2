<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Error' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Inter', sans-serif;
            background: #f5f7fa;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            background: white;
            border-radius: 20px;
            padding: 60px 40px;
            text-align: center;
            max-width: 500px;
            width: 100%;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        }
        .error-icon {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
        }
        .error-icon.warning {
            background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);
        }
        .error-icon.cancelled {
            background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%);
        }
        .error-icon i {
            font-size: 48px;
            color: white;
        }
        h1 {
            font-size: 28px;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 15px;
        }
        p {
            font-size: 16px;
            color: #6c757d;
            line-height: 1.6;
            margin-bottom: 30px;
        }
        .error-details {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
            text-align: left;
        }
        .error-details h3 {
            font-size: 14px;
            font-weight: 600;
            color: #6c757d;
            text-transform: uppercase;
            margin-bottom: 10px;
        }
        .error-details p {
            font-size: 14px;
            color: #2c3e50;
            margin-bottom: 0;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 15px 30px;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s ease;
            cursor: pointer;
            border: none;
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .btn-primary:hover {
            opacity: 0.9;
            transform: translateY(-2px);
        }
        .support-text {
            margin-top: 30px;
            font-size: 14px;
            color: #6c757d;
        }
        .support-text a {
            color: #667eea;
            text-decoration: none;
        }
        .support-text a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="error-icon {{ $iconClass ?? '' }}">
            <i class="fas {{ $icon ?? 'fa-exclamation-triangle' }}"></i>
        </div>
        
        <h1>{{ $title ?? 'Something Went Wrong' }}</h1>
        <p>{{ $message ?? 'We encountered an error while processing your request.' }}</p>
        
        @if(isset($details))
        <div class="error-details">
            <h3>Details</h3>
            <p>{{ $details }}</p>
        </div>
        @endif
        
        @if(isset($actionUrl) && isset($actionText))
        <a href="{{ $actionUrl }}" class="btn btn-primary">
            <i class="fas {{ $actionIcon ?? 'fa-arrow-left' }}"></i> {{ $actionText }}
        </a>
        @endif
        
        <p class="support-text">
            Need help? <a href="mailto:support@bansalcrm.com">Contact Support</a>
        </p>
    </div>
</body>
</html>
