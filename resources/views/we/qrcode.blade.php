<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>扫码登录</title>
    <style>
        #qrCode{ margin:0 auto;text-align:center;}
    </style>
</head>
<body>
<div id="qrCode">
</div>
<script src="//res.wx.qq.com/connect/zh_CN/htmledition/js/wxLogin.js"></script>
<script>
    window.onload = function () {
        var obj = new WxLogin({
            id: "qrCode",
            appid: "{{  config('wechat.open_platform.weixinweb.app_id') }}",
            scope: "snsapi_login",
            redirect_uri: "{{ $redirect_uri }}",
            state: "{{ time() }}",
            style: "",
            href:"data:text/css;base64,LmltcG93ZXJCb3ggLnFyY29kZSB7d2lkdGg6IDIwMHB4O30NCi5pbXBvd2VyQm94IC50aXRsZSB7ZGlzcGxheTogbm9uZTt9DQouaW1wb3dlckJveCAuaW5mbyB7d2lkdGg6IDIwMHB4O30NCi5zdGF0dXNfaWNvbiB7ZGlzcGxheTpub25lfQ0KLmltcG93ZXJCb3ggLnN0YXR1cyB7dGV4dC1hbGlnbjogY2VudGVyO30="//data-url
        });
    }


</script>
</body>
</html>
