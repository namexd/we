<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>错误</title>
    <style>
        #error {
            margin: 25% auto;
            text-align: center;
            color: darkred;
            font-size: 16px;
            font-weight: bold;
        }
    </style>
</head>
<body>
<div id="error">
    403, {{ $message }}
</div>
</body>
</html>
