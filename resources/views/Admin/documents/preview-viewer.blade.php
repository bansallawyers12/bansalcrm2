<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $filename }}</title>
    <style>
        html, body { margin: 0; padding: 0; height: 100%; overflow: hidden; background: #525659; }
        iframe { position: fixed; inset: 0; width: 100%; height: 100%; border: 0; }
        img { max-width: 100%; height: auto; display: block; margin: 0 auto; }
    </style>
</head>
<body>
@if($isImage)
    <img src="{{ $contentSrc }}" alt="{{ $filename }}">
@else
    <iframe src="{{ $contentSrc }}" title="{{ $filename }}"></iframe>
@endif
</body>
</html>
