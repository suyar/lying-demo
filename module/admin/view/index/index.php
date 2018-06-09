<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>小黑屋</title>
    <link rel="stylesheet" href="<?= $STATIC; ?>/layui/css/layui.css">
    <link rel="stylesheet" href="<?= $STATIC; ?>/lau/lau.css">
    <script src="<?= $STATIC; ?>/js/top.js"></script>
</head>
<body class="layui-layout-body layui-unselect">
<div class="layui-layout layui-layout-admin">
    <!--顶部导航开始-->
    <div class="layui-header">
        <a class="lau-logo-mini"><i class="layui-icon layui-icon-rate-half"></i></a>
        <a class="layui-logo">▄︻┻┳══━一</a>
        <ul class="layui-nav layui-layout-left">
            <li class="layui-nav-item" lay-unselect>
                <a>站点推荐</a>
                <dl class="layui-nav-child">
                    <dd><a href="http://ylui.yuri2.cn/" target="_blank">YLUI(WIN10UI)</a></dd>
                    <dd><a href="http://www.bycodes.net/" target="_blank">码外社区(你想看的都有)</a></dd>
                    <dd><a href="http://lovefc.cn/" target="_blank">女装大佬(lovefc)</a></dd>
                    <dd><a href="http://blog.he110.info/" target="_blank">校园吸血鬼(He110)</a></dd>
                    <dd><a href="http://yuri2.cn/blog/" target="_blank">尤里2号(赞助商)</a></dd>
                </dl>
            </li>
        </ul>
        <ul class="layui-nav layui-layout-right">
            <li class="layui-nav-item" lay-unselect>
                <a><?= $USER['username']; ?></a>
                <dl class="layui-nav-child">
                    <dd><a href="<?= url('sign/logout'); ?>">安全退出</a></dd>
                </dl>
            </li>
            <li class="layui-nav-item" lay-unselect><a lau-event="about"><i class="layui-icon layui-icon-more-vertical"></i></a></li>
        </ul>
    </div>
    <!--顶部导航结束-->

    <!--侧边菜单开始-->
    <div class="layui-side">
        <div class="lau-side-fold"><i class="layui-icon layui-icon-shrink-right"></i></div>
        <div class="layui-side-scroll">
            <ul class="layui-nav layui-nav-tree"></ul>
        </div>
    </div>
    <!--侧边菜单结束-->

    <!--内容主体区域开始-->
    <div class="layui-body"></div>
    <!--内容主体区域结束-->
</div>
</body>
<script src="<?= $STATIC; ?>/layui/layui.js"></script>
<script>
    layui.config({base: '<?= $STATIC; ?>/'}).extend({lau: 'lau/lau'}).use(['lau'], function () {
        var lau = layui.lau,
            layer = layui.layer,
            $ = layui.$;

        //渲染菜单
        $.get(location.href, function (res) {
            lau.sideMenuRender(res.data);
        }, 'json');

        //监听事件,这个不一定要用lau-event,可以自己写
        $(document).on('click', '[lau-event]', function () {
            var _this = $(this);
            switch (_this.attr('lau-event')) {
                case 'about':
                    lau.drawer();
                    break;
            }
        });

    });
</script>
</html>