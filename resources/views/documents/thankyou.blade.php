<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document Signed Successfully</title>
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
        }
        .success-icon {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            animation: scaleIn 0.5s ease-out;
        }
        .success-icon i {
            font-size: 48px;
            color: white;
        }
        @keyframes scaleIn {
            0% {
                transform: scale(0);
                opacity: 0;
            }
            50% {
                transform: scale(1.1);
            }
            100% {
                transform: scale(1);
                opacity: 1;
            }
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
        .document-info {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
        }
        .document-info h3 {
            font-size: 14px;
            font-weight: 600;
            color: #6c757d;
            text-transform: uppercase;
            margin-bottom: 10px;
        }
        .document-info p {
            font-size: 18px;
            color: #2c3e50;
            font-weight: 500;
            margin-bottom: 0;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e9ecef;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-row label {
            font-size: 14px;
            color: #6c757d;
        }
        .info-row span {
            font-size: 14px;
            color: #2c3e50;
            font-weight: 500;
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
        .btn-outline {
            background: transparent;
            color: #667eea;
            border: 2px solid #667eea;
        }
        .btn-outline:hover {
            background: #667eea;
            color: white;
        }
        .buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }
        .confetti {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            overflow: hidden;
            z-index: 1000;
        }
        .confetti-piece {
            position: absolute;
            width: 10px;
            height: 10px;
            background: #ffd700;
            animation: confetti-fall 3s ease-in-out forwards;
        }
        @keyframes confetti-fall {
            0% {
                transform: translateY(-100vh) rotate(0deg);
                opacity: 1;
            }
            100% {
                transform: translateY(100vh) rotate(720deg);
                opacity: 0;
            }
        }
    </style>
</head>
<body>
    <div class="confetti" id="confetti"></div>
    
    <div class="container">
        <div class="success-icon">
            <i class="fas fa-check"></i>
        </div>
        
        <h1>Document Signed Successfully!</h1>
        <p>Thank you for signing the document. All parties will receive a copy of the signed document via email.</p>
        
        @if(isset($document))
        <div class="document-info">
            <h3>Document Details</h3>
            <div class="info-row">
                <label>Document</label>
                <span>{{ $document->display_title }}</span>
            </div>
            <div class="info-row">
                <label>Signed On</label>
                <span>{{ now()->format('M d, Y h:i A') }}</span>
            </div>
            @if(isset($signer))
            <div class="info-row">
                <label>Signed By</label>
                <span>{{ $signer->name }}</span>
            </div>
            @endif
        </div>
        @endif
        
        <div class="buttons">
            @if(isset($document) && $document->signed_doc_link)
            <a href="{{ route('public.documents.download.signed', $document->id) }}?token={{ $signer->token ?? '' }}" class="btn btn-primary">
                <i class="fas fa-download"></i> Download Signed Copy
            </a>
            @endif
        </div>
    </div>

    <script>
        // Create confetti effect
        function createConfetti() {
            const confettiContainer = document.getElementById('confetti');
            const colors = ['#667eea', '#764ba2', '#28a745', '#ffd700', '#ff6b6b', '#4ecdc4'];
            
            for (let i = 0; i < 50; i++) {
                const piece = document.createElement('div');
                piece.className = 'confetti-piece';
                piece.style.left = Math.random() * 100 + 'vw';
                piece.style.background = colors[Math.floor(Math.random() * colors.length)];
                piece.style.animationDelay = Math.random() * 2 + 's';
                piece.style.animationDuration = (Math.random() * 2 + 2) + 's';
                piece.style.transform = 'rotate(' + Math.random() * 360 + 'deg)';
                confettiContainer.appendChild(piece);
            }
            
            // Clean up confetti after animation
            setTimeout(() => {
                confettiContainer.innerHTML = '';
            }, 5000);
        }
        
        createConfetti();
    </script>
</body>
</html>
