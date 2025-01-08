<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendar</title>
    <style>
        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
            font-family: Arial, sans-serif;
        }
        .calendar-container {
            width: 100%;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        iframe {
            width: 100%;
            height: 100%;
            border: none;
        }
    </style>
</head>
<body>
    <div class="calendar-container">
        <iframe src="https://calendar.google.com/calendar/embed?src=your_calendar_id&ctz=your_timezone" frameborder="0" scrolling="no"></iframe>
    </div>
</body>
</html>