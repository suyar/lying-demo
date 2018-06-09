<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>菜单管理</title>
    <link rel="stylesheet" href="<?= $STATIC; ?>/layui/css/layui.css">
    <link rel="stylesheet" href="<?= $STATIC; ?>/admin/css/common.css">
</head>
<body>
<div class="layui-card">
    <div class="layui-card-header">菜单管理</div>
    <div class="layui-card-body">
        <div class="layui-btn-group">
            <?php if ($AUTH('menu/create')): ?>
                <button class="layui-btn layui-btn-sm" id="create"><i class="layui-icon layui-icon-add-1"></i> 添加一级菜单</button>
            <?php endif; ?>
            <button class="layui-btn layui-btn-sm" id="refresh"><i class="layui-icon layui-icon-refresh-3"></i> 刷新列表</button>
        </div>
        <table id="menu" lay-filter="menu"></table>
    </div>
</div>

<!--操作开始-->
<script type="text/html" id="op">
    <div class="layui-btn-group">
        <?php if ($AUTH('menu/update')): ?>
            <button class="layui-btn layui-btn-xs" lay-event="edit"><i class="layui-icon layui-icon-edit"></i></button>
        <?php endif; ?>

        <?php if ($AUTH('menu/delete')): ?>
            <button class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del"><i class="layui-icon layui-icon-delete"></i></button>
        <?php endif; ?>

        <?php if ($AUTH('menu/create')): ?>
            {{# if (d.type == 0) { }}
            <button class="layui-btn layui-btn-xs" lay-event="sub"><i class="layui-icon layui-icon-add-1"></i></button>
            {{# } else if (d.type == 1) { }}
            <button class="layui-btn layui-btn-xs" lay-event="auth"><i class="layui-icon layui-icon-add-1"></i></button>
            {{# } }}
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
        <input type="hidden" name="type" value="{{ d.type }}">
        <input type="hidden" name="pid" value="{{ d.pid }}">
        <div class="layui-form-item">
            <label class="layui-form-label">标 题</label>
            <div class="layui-input-block">
                <input type="text" name="name" value="{{ d.name || '' }}" placeholder="请输入菜单标题" autocomplete="off" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">图 标</label>
            <div class="layui-input-block">
                <input type="text" name="icon" value="{{ d.icon || '' }}" placeholder="请输入图标样式" autocomplete="off" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">鉴 权</label>
            <div class="layui-input-block">
                <input type="text" name="code" value="{{ d.code || '' }}" placeholder="请输入鉴权标识" autocomplete="off" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item" pane>
            <label class="layui-form-label">显 示</label>
            <div class="layui-input-block">
                <input type="radio" name="show" value="1" title="显示" {{ d.show ? 'checked' : '' }}>
                <input type="radio" name="show" value="0" title="隐藏" {{ d.show ? '' : 'checked' }}>
            </div>
        </div>
        <div class="layui-form-item" pane>
            <label class="layui-form-label">启 用</label>
            <div class="layui-input-block">
                <input type="radio" name="enable" value="1" title="启用" {{ d.enable ? 'checked' : '' }}>
                <input type="radio" name="enable" value="0" title="禁用" {{ d.enable ? '' : 'checked' }}>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">排 序</label>
            <div class="layui-input-block">
                <input type="text" name="sort" value="{{ d.sort || 0 }}" placeholder="请输入0-255的数字" autocomplete="off" class="layui-input">
            </div>
        </div>
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

        //增加一级菜单
        $('#create').click(function () {
            laytpl($('#edit').html()).render({type: 0, pid: 0}, function(html){
                layer.open({
                    type: 1,
                    title: '添加一级菜单',
                    area: '420px',
                    content: html,
                    success: function () {
                        form.render();
                    }
                });
            });
        });

        //实例化表格
        var tableIns = table.render({
            elem: '#menu',
            url: location.href,
            text: {
                none: '暂无相关数据'
            },
            size: 'sm',
            cols: [[
                {field: 'id', title: 'ID', width: 50},
                {field: 'name', title: '菜单标题', width: 200, templet: function (d) { return d.level + d.name; }},
                {title: '菜单类型', width: 90, templet: function (d) { return d.type == 0 ? '一级菜单' : d.type == 1 ? '二级菜单' : '细节权限'; }},
                {field: 'icon', title: '菜单图标', width: 200},
                {field: 'code', title: '鉴权标识', width: 200},
                {field: 'show', title: '显示状态', width: 85, templet: function (d) { return d.show ? '显示' : '隐藏'; }},
                {field: 'enable', title: '启用状态', width: 85, templet: function (d) { return d.enable ? '启用' : '禁用'; }},
                {field: 'sort', title: '排序', width: 60},
                {title: '操作', width: 115, fixed: 'right', toolbar: '#op'}
            ]]
        });

        //刷新表格
        $('#refresh').click(function () {
            tableIns.reload();
        });

        //表格事件监听
        table.on('tool(menu)', function(obj) {
            switch (obj.event) {
                case 'edit':
                    laytpl($('#edit').html()).render(obj.data, function(html){
                        layer.open({
                            type: 1,
                            title: '编辑【' + obj.data.name + '】',
                            area: '420px',
                            content: html,
                            success: function () {
                                form.render();
                            }
                        });
                    });
                    break;
                case 'del':
                    layer.confirm('确定要删除【' + obj.data.name + '】？', {icon: 3}, function() {
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
                case 'sub':
                    laytpl($('#edit').html()).render({type: 1, pid: obj.data.id}, function(html){
                        layer.open({
                            type: 1,
                            title: '【' + obj.data.name + '】添加二级菜单',
                            area: '420px',
                            content: html,
                            success: function () {
                                form.render();
                            }
                        });
                    });
                    break;
                case 'auth':
                    laytpl($('#edit').html()).render({type: 2, pid: obj.data.id}, function(html){
                        layer.open({
                            type: 1,
                            title: '【' + obj.data.name + '】添加细节权限',
                            area: '420px',
                            content: html,
                            success: function () {
                                form.render();
                            }
                        });
                    });
                    break;
            }
        });

        //更新菜单
        form.on('submit(edit)', function(data) {
            if (!/^\S+$/.test(data.field.name)) {
                tips.warning('菜单标题不正确');
                return false;
            } else if (!/^([0-9]|[1-9]\d|1\d{2}|2[0-4]\d|25[0-5])$/.test(data.field.sort)) {
                tips.warning('排序数字应为0-255');
                return false;
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

        //增加菜单
        form.on('submit(create)', function(data) {
            if (!/^\S+$/.test(data.field.name)) {
                tips.warning('菜单标题不正确');
                return false;
            } else if (!/^([0-9]|[1-9]\d|1\d{2}|2[0-4]\d|25[0-5])$/.test(data.field.sort)) {
                tips.warning('排序数字应为0-255');
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
