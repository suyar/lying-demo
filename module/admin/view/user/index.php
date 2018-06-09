<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>用户管理</title>
    <link rel="stylesheet" href="<?= $STATIC; ?>/layui/css/layui.css">
    <link rel="stylesheet" href="<?= $STATIC; ?>/admin/css/common.css">
</head>
<body>
<div class="layui-card">
    <div class="layui-card-header">用户管理</div>
    <div class="layui-card-body">
        <div class="layui-btn-group">
            <?php if ($AUTH('user/create')): ?>
                <button class="layui-btn layui-btn-sm" id="create"><i class="layui-icon layui-icon-add-1"></i> 添加用户</button>
            <?php endif; ?>
            <button class="layui-btn layui-btn-sm" id="refresh"><i class="layui-icon layui-icon-refresh-3"></i> 刷新列表</button>
        </div>
        <table id="user" lay-filter="user"></table>
    </div>
</div>

<!--操作开始-->
<script type="text/html" id="op">
    <div class="layui-btn-group">
        <?php if ($AUTH('user/update')): ?>
            <button class="layui-btn layui-btn-xs" lay-event="edit"><i class="layui-icon layui-icon-edit"></i></button>
        <?php endif; ?>

        <?php if ($AUTH('user/delete')): ?>
            <button class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del"><i class="layui-icon layui-icon-delete"></i></button>
        <?php endif; ?>

        <?php if ($AUTH('user/refresh')): ?>
            <button class="layui-btn layui-btn-xs" lay-event="refresh"><i class="layui-icon layui-icon-refresh"></i></button>
        <?php endif; ?>
    </div>
</script>
<!--操作结束-->

<!--编辑开始-->
<script id="edit" type="text/html">
    <form class="layui-form layui-form-pane" style="padding: 15px;">
        {{#  if (d.id) { }}
        <input type="hidden" name="id" value="{{ d.id }}">
        {{#  } }}

        <div class="layui-form-item">
            <label class="layui-form-label">名 称</label>
            <div class="layui-input-block">
                <input type="text" name="username" value="{{ d.username || '' }}" placeholder="请输入用户名" autocomplete="off" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">旧 密</label>
            <div class="layui-input-block">
                <input type="password" name="old_password" value="" placeholder="输入旧密码（更改密码时填写）" autocomplete="off" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">新 密</label>
            <div class="layui-input-block">
                <input type="password" name="password" value="" placeholder="输入新密码（新增管理员/更改密码时填写）" autocomplete="off" class="layui-input">
            </div>
        </div>

        <fieldset class="layui-elem-field">
            <legend>配置角色</legend>
            <div class="layui-field-box">
                {{# layui.each(d.roles, function(index, item) { }}
                <input type="checkbox" title="{{ item.name }}" name="role[]" value="{{ item.id }}" {{ item.check ? 'checked' : '' }} lay-skin="primary">
                {{# }); }}
            </div>
        </fieldset>

        <div class="layui-btn-container">
            {{#  if (d.id) { }}
            <button class="layui-btn layui-btn-fluid" lay-submit lay-filter="edit">更新</button>
            {{#  } else { }}
            <button class="layui-btn layui-btn-fluid" lay-submit lay-filter="create">添加</button>
            {{#  } }}
        </div>
    </form>
</script>
<!--编辑结束-->

</body>
<script src="<?= $STATIC; ?>/layui/layui.js"></script>
<script>
    layui.config({base: '<?= $STATIC; ?>/admin/js/'}).use(['tips', 'table', 'laytpl', 'form'], function() {
        var tips = layui.tips,
            table = layui.table,
            $ = layui.$,
            laytpl = layui.laytpl,
            form = layui.form,
            layer = layui.layer;

        //设置CSRF头
        $.ajaxSetup({
            headers: {'x-csrf-token': '<?= $CSRF; ?>'}
        });

        //增加角色
        $('#create').click(function () {
            var index = tips.loading('正在加载……', null, -1);
            $.getJSON('<?= url('create'); ?>', function (res) {
                layer.close(index);
                laytpl($('#edit').html()).render({roles: res.data}, function(html){
                    layer.open({
                        type: 1,
                        title: '添加用户',
                        area: ['95%', '95%'],
                        content: html,
                        success: function () {
                            form.render();
                        }
                    });
                });
            });
        });

        //实例化表格
        var tableIns = table.render({
            elem: '#user',
            url: location.href,
            text: {
                none: '暂无相关数据'
            },
            size: 'sm',
            cols: [[
                {field: 'id', title: 'ID', width: 50},
                {field: 'username', title: '用户名', width: 150},
                {field: 'roles', title: '所属角色', width: 300},
                {title: '操作', width: 115, fixed: 'right', toolbar: '#op'}
            ]]
        });

        //刷新表格
        $('#refresh').click(function () {
            tableIns.reload();
        });

        //表格事件监听
        table.on('tool(user)', function(obj) {
            switch (obj.event) {
                case 'edit':
                    var index = tips.loading('正在加载……', null, -1);
                    $.get('<?= url('update'); ?>', {id: obj.data.id}, function (res) {
                        layer.close(index);
                        obj.data.roles = res.data;
                        laytpl($('#edit').html()).render(obj.data, function(html){
                            layer.open({
                                type: 1,
                                title: '编辑【' + obj.data.username + '】',
                                area: ['95%', '95%'],
                                content: html,
                                success: function () {
                                    form.render();
                                }
                            });
                        });
                    }, 'json');
                    break;
                case 'del':
                    layer.confirm('确定要删除【' + obj.data.username + '】？', {icon: 3}, function() {
                        $.post('<?= url('delete'); ?>', obj.data, function (res) {
                            if (res.code == 0) {
                                tips.success(res.msg, function () {
                                    tableIns.reload();
                                });
                            } else {
                                tips.error(res.msg);
                            }
                        }, 'json');
                    });
                    break;
                case 'refresh':
                    $.get('<?= url('user/refresh'); ?>', {id: obj.data.id}, function (res) {
                        tips.success(res.msg);
                    }, 'json');
                    break;
            }
        });

        //更新用户
        form.on('submit(edit)', function(data) {
            if (!/^\S+$/.test(data.field.name)) {
                tips.warning('用户名格式不正确');
                return false;
            } else if (data.field.old_password !== '' || data.field.password !== '') {
                if (!/^\S{6,}$/.test(data.field.old_password)) {
                    tips.warning('旧密码格式不正确');
                    return false;
                } else if (!/^\S{6,}$/.test(data.field.password)) {
                    tips.warning('新密码格式不正确');
                    return false;
                } else if (data.field.old_password === data.field.password) {
                    tips.warning('新旧密码不能一样');
                    return false;
                }
            }

            $.post('<?= url('update'); ?>', data.field, function (res) {
                if (res.code == 0) {
                    tips.success(res.msg, function () {
                        tableIns.reload();
                        layer.closeAll();
                    });
                } else {
                    tips.error(res.msg);
                }
            }, 'json');

            return false;
        });

        //增加用户
        form.on('submit(create)', function(data) {
            if (!/^\S+$/.test(data.field.name)) {
                tips.warning('用户名格式不正确');
                return false;
            } else if (!/^\S{6,}$/.test(data.field.password)) {
                tips.warning('密码格式不正确');
                return false;
            }

            $.post('<?= url('create'); ?>', data.field, function (res) {
                if (res.code == 0) {
                    tips.success(res.msg, function () {
                        tableIns.reload();
                        layer.closeAll();
                    });
                } else {
                    tips.error(res.msg);
                }
            }, 'json');

            return false;
        });
    });
</script>
</html>
