<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>角色管理</title>
    <link rel="stylesheet" href="<?= $STATIC; ?>/layui/css/layui.css">
    <link rel="stylesheet" href="<?= $STATIC; ?>/admin/css/common.css">
</head>
<body>
<div class="layui-card">
    <div class="layui-card-header">角色管理</div>
    <div class="layui-card-body">
        <div class="layui-btn-group">
            <?php if ($AUTH('role/create')): ?>
                <button class="layui-btn layui-btn-sm" id="create"><i class="layui-icon layui-icon-add-1"></i> 添加角色</button>
            <?php endif; ?>
            <button class="layui-btn layui-btn-sm" id="refresh"><i class="layui-icon layui-icon-refresh-3"></i> 刷新列表</button>
        </div>
        <table id="role" lay-filter="role"></table>
    </div>
</div>

<!--操作开始-->
<script type="text/html" id="op">
    <div class="layui-btn-group">
        <?php if ($AUTH('role/update')): ?>
            <button class="layui-btn layui-btn-xs" lay-event="edit"><i class="layui-icon layui-icon-edit"></i></button>
        <?php endif; ?>

        <?php if ($AUTH('role/delete')): ?>
            <button class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del"><i class="layui-icon layui-icon-delete"></i></button>
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

        <div class="layui-row layui-col-space10">
            <div class="layui-col-sm6">
                <div class="layui-form-item">
                    <label class="layui-form-label">名 称</label>
                    <div class="layui-input-block">
                        <input type="text" name="name" value="{{ d.name || '' }}" placeholder="请输入角色名称" autocomplete="off" class="layui-input">
                    </div>
                </div>
            </div>
            <div class="layui-col-sm6">
                <div class="layui-form-item" pane>
                    <label class="layui-form-label">启 用</label>
                    <div class="layui-input-block">
                        <input type="radio" name="enable" value="1" title="启用" {{ d.enable ? 'checked' : '' }}>
                        <input type="radio" name="enable" value="0" title="禁用" {{ d.enable ? '' : 'checked' }}>
                    </div>
                </div>
            </div>
        </div>

        {{# layui.each(d.auth, function(index, item) { }}
        <fieldset class="layui-elem-field">
            <legend><input type="checkbox" title="{{ item.name }}" name="permission[]" value="{{ item.id }}" {{ item.check ? 'checked' : '' }} lay-skin="primary" lay-filter="p0"></legend>
            <div class="layui-field-box">
                {{# layui.each(item.childs, function(index1, item1) { }}
                <fieldset class="layui-elem-field">
                    <legend><input type="checkbox" title="{{ item1.name }}" name="permission[]" value="{{ item1.id }}" {{ item1.check ? 'checked' : '' }} lay-skin="primary" lay-filter="p1"></legend>
                    <div class="layui-field-box">
                        {{# layui.each(item1.childs, function(index2, item2) { }}
                        <input type="checkbox" title="{{ item2.name }}" name="permission[]" value="{{ item2.id }}" {{ item2.check ? 'checked' : '' }} lay-skin="primary" lay-filter="p2">
                        {{# }); }}
                    </div>
                </fieldset>
                {{# }); }}
            </div>
        </fieldset>
        {{# }); }}

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

        //监听一级菜单
        form.on('checkbox(p0)', function(data) {
            $(data.elem).parent().next().find('input').prop('checked', data.elem.checked);
            form.render();
        });

        //监听二级菜单
        form.on('checkbox(p1)', function(data) {
            var _this = $(data.elem),
                _parent = _this.parents('.layui-field-box'),
                _checked = _parent.find('input:checked');

            //操作子级
            _this.parent().next().find('input').prop('checked', data.elem.checked);

            //操作父级
            if (_checked[0]) {
                _parent.prev().find('input').prop('checked', true);
            }

            form.render();
        });

        //监听细节权限
        form.on('checkbox(p2)', function(data) {
            var _this = $(data.elem),
                _parent = _this.parent(),
                _checked = _parent.find('input:checked');

            //操作父级
            if (_checked[0]) {
                _parent.prev().find('input').prop('checked', true);
            }

            var _pparent = _parent.parents('.layui-field-box'),
                _pchecked = _pparent.find('input:checked');

            //操作爷爷级
            if (_pchecked[0]) {
                _pparent.prev().find('input').prop('checked', true);
            }

            form.render();
        });

        //增加角色
        $('#create').click(function () {
            var index = tips.loading('正在加载……', null, -1);
            $.getJSON('<?= url('create'); ?>', function (res) {
                layer.close(index);
                laytpl($('#edit').html()).render({auth: res.data}, function(html){
                    layer.open({
                        type: 1,
                        title: '添加角色',
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
            elem: '#role',
            url: location.href,
            text: {
                none: '暂无相关数据'
            },
            size: 'sm',
            cols: [[
                {field: 'id', title: 'ID', width: 50},
                {field: 'name', title: '角色名称', width: 200},
                {field: 'enable', title: '启用状态', width: 85, templet: function (d) { return d.enable ? '启用' : '禁用'; }},
                {title: '操作', width: 85, fixed: 'right', toolbar: '#op'}
            ]]
        });

        //刷新表格
        $('#refresh').click(function () {
            tableIns.reload();
        });

        //表格事件监听
        table.on('tool(role)', function(obj) {
            switch (obj.event) {
                case 'edit':
                    var index = tips.loading('正在加载……', null, -1);
                    $.get('<?= url('update'); ?>', {id: obj.data.id}, function (res) {
                        layer.close(index);
                        obj.data.auth = res.data;
                        laytpl($('#edit').html()).render(obj.data, function(html){
                            layer.open({
                                type: 1,
                                title: '编辑【' + obj.data.name + '】',
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
            }
        });

        //更新角色
        form.on('submit(edit)', function(data) {
            if (!/^\S+$/.test(data.field.name)) {
                tips.warning('角色名称不正确');
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

        //增加角色
        form.on('submit(create)', function(data) {
            if (!/^\S+$/.test(data.field.name)) {
                tips.warning('角色名称不正确');
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
