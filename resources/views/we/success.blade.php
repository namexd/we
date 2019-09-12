<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>成功</title>
    <style>
        #error {
            margin: 15% auto;
            text-align: center;
            color: darkgreen;
            font-size: 16px;
            font-weight: bold;
        }
        #error h1{ font-size:80px;}
    </style>
</head>
<body>
<div id="error">
    <h1>:)</h1>
    <br>
    {{ $message }}
    <a href="{{ $refer }}">点击返回</a>
</div>
</body>
</html>
