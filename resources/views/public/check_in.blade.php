<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Check In — Bansal Immigration and Education</title>
    <style>
        :root { --navy:#1e3a5f; --blue:#2d6a9f; --bg:#f4f7fb; --muted:#5c6770; --line:#e4e8ee; }
        * { box-sizing: border-box; }
        body { margin: 0; font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif; background: var(--bg); color: #1a1a1a; line-height: 1.45; }
        .wrap { max-width: 720px; margin: 0 auto; padding: 16px 12px 40px; }
        .card { background: #fff; border-radius: 14px; overflow: hidden; box-shadow: 0 8px 28px rgba(30,58,95,.08); border: 1px solid var(--line); }
        .head { background: linear-gradient(135deg, var(--navy), var(--blue)); color: #fff; padding: 28px 18px; text-align: center; }
        .head h1 { margin: 0; font-size: 1.35rem; line-height: 1.3; }
        .body { padding: 22px 16px 28px; }
        .prompt { margin: 0 0 18px; text-align: center; color: var(--muted); font-size: .98rem; }
        .choices { display: flex; flex-direction: column; gap: 12px; }
        .choice {
            display: flex; align-items: center; justify-content: center;
            min-height: 64px; padding: 18px 16px; border-radius: 12px;
            text-decoration: none; font-weight: 700; font-size: 1.1rem;
            border: 2px solid var(--line); color: var(--navy); background: #f8fbff;
        }
        .choice:hover, .choice:focus { border-color: var(--navy); background: #eef4fb; outline: none; }
        .choice.education { color: #0f4c81; }
        .foot { text-align: center; color: var(--muted); font-size: .8rem; margin-top: 16px; }
        @media (min-width: 700px) {
            .wrap { padding: 36px 20px 56px; }
            .head, .body { padding-left: 28px; padding-right: 28px; }
            .head h1 { font-size: 1.65rem; }
            .choices { flex-direction: row; }
            .choice { flex: 1; }
        }
    </style>
</head>
<body>
<div class="wrap">
    <div class="card">
        <div class="head">
            <h1>Welcome to Bansal Immigration and Education</h1>
        </div>
        <div class="body">
            <p class="prompt">Please choose how you are checking in today.</p>
            <div class="choices">
                <a class="choice" id="opt-immigration" href="{{ $immigrationCheckInUrl }}">Immigration</a>
                <a class="choice education" id="opt-education" href="{{ $educationCheckInUrl }}">Education</a>
            </div>
        </div>
    </div>
    <p class="foot">Bansal Immigration and Education</p>
</div>
</body>
</html>
