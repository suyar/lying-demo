<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>登录小黑屋</title>
    <link rel="stylesheet" href="<?= $STATIC; ?>/layui/css/layui.css">
    <link rel="stylesheet" href="<?= $STATIC; ?>/admin/css/sign.css">
    <script src="<?= $STATIC; ?>/js/top.js"></script>
</head>
<body class="layui-unselect">

<div class="layui-form layui-form-pane sign-form">
    <h1 class="sign-title">٩(๑❛ᴗ❛๑)۶登录小黑屋</h1>
    <p class="sign-subtitle">再烦，我就把你关进来</p>
    <div class="layui-form-item">
        <label class="layui-form-label"><i class="layui-icon layui-icon-username"></i> 账　号</label>
        <div class="layui-input-block">
            <input type="text" name="username" placeholder="请输入用户名" autocomplete="off" class="layui-input">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label"><i class="layui-icon layui-icon-password"></i> 密　码</label>
        <div class="layui-input-block">
            <input type="password" name="password" placeholder="请输入密码" autocomplete="off" class="layui-input">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label"><i class="layui-icon layui-icon-vercode"></i> 验证码</label>
        <div class="layui-input-block">
            <input type="text" name="captcha" placeholder="请输入图形验证码" autocomplete="off" class="layui-input sign-code-input">
            <img src="<?= url('captcha'); ?>" alt="图形验证码" class="sign-captcha">
        </div>
    </div>
    <div class="layui-form-item">
        <input type="hidden" name="_csrf" value="<?= $CSRF; ?>">
        <input type="checkbox" name="remember" lay-skin="primary" title="记住密码">
    </div>
    <div class="layui-form-item">
        <button type="button" class="layui-btn layui-btn-fluid" lay-submit lay-filter="login">登 入</button>
    </div>
</div>

<div class="sign-icon"><i class="layui-icon layui-icon-face-surprised"></i></div>

</body>
<script src="<?= $STATIC; ?>/js/lib/crypto-js.js"></script>
<script src="<?= $STATIC; ?>/layui/layui.js"></script>
<script>
    layui.config({base: '/static/admin/js/'}).use('login');
</script>
</html>
