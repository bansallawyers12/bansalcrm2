<!DOCTYPE html>
<html>
<head>
    <title>Australian PR Points Calculator</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1>Australian PR Points Calculator</h1>
        <form action="{{ route('pr-points.calculate') }}" method="post">
            @csrf
            <div class="mb-3">
                <label for="age" class="form-label">Age:</label>
                <select name="age" id="age" class="form-select" required>
                    <option value="">Select age range</option>
                    <option value="18">18-24</option>
                    <option value="25">25-32</option>
                    <option value="33">33-39</option>
                    <option value="40">40-44</option>
                    <option value="45">45+</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="english_level" class="form-label">English Language Skills:</label>
                <select name="english_level" id="english_level" class="form-select" required>
                    <option value="">Select English level</option>
                    <option value="competent">Competent English</option>
                    <option value="proficient">Proficient English</option>
                    <option value="superior">Superior English</option>
                </select>
            </div>
            <!-- Add other form fields for skilled employment, education qualifications, etc. -->
            <div class="mb-3">
                <label for="email" class="form-label">Email Address:</label>
                <input type="email" name="email" id="email" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Calculate Points</button>
        </form>
    </div>
</body>
</html>
