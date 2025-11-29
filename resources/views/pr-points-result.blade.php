<!DOCTYPE html>
<html>
<head>
    <title>PR Points Result</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1>PR Points Result</h1>
        <p>Your total PR points: {{ $points }}</p>
        <p>The result has been emailed to you.</p>
        <a href="{{ route('pr-points.index') }}" class="btn btn-primary">Calculate Again</a>
    </div>
</body>
</html>
