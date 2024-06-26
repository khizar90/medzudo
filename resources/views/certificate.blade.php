<!DOCTYPE html>
<html>
<head>
    <title>Certificate</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
        }
        .certificate {
            text-align: center;
            border: 10px solid #000;
            padding: 50px;
        }
        .certificate h1 {
            font-size: 50px;
            margin: 0;
        }
        .certificate p {
            font-size: 20px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="certificate">
        <h1>Certificate of Completion</h1>
        <p>This is to certify that</p>
        <h2>{{ $name }}</h2>
        <p>has successfully completed the course</p>
        <h2>{{ $course }}</h2>
        <p>on</p>
        <h2>{{ $date }}</h2>
    </div>
</body>
</html>