layui.define(['layer', 'laytpl', 'element'], function(exports) {
    var layer = layui.layer,
        laytpl = layui.laytpl,
        element = layui.element,
        $ = layui.$;

    /**
     * 预设模板
     * @type {{ Tpl }}
     */
    var Tpl = {
        menu: [
            '{{# layui.each(d, function(index, item) { ',
                'var hasItem = typeof item.list === \'object\' && item.list.length > 0, ',
                    'href = !hasItem && item.href ? \' lau-href="\' + item.href + \'"\' : \'\', ',
                    'title = item.title, ',
                    'icon = item.icon || \'layui-icon layui-icon-right\', ',
                    'open = Boolean(item.open) && hasItem ? \' lau-open\' : \'\'; ',
            '}}',
            '<li class="lau-nav-item{{ open }}">',
                '<a class="lau-nav-header"{{ href }}>',
                    '<i class="{{ icon }}"></i>',
                    '<cite>{{ title }}</cite>',
                '</a>',
                '{{# if (hasItem) { }}',
                '<dl class="lau-nav-child">',
                    '{{# layui.each(item.list, function(index2, item2) { ',
                        'var href = item2.href ? \' lau-href="\' + item2.href + \'"\' : \'\', ',
                            'title = item2.title, ',
                            'icon = item2.icon || \'layui-icon layui-icon-circle\'; ',
                    '}}',
                    '<dd>',
                        '<a{{ href }}>',
                            '<i class="{{ icon }}"></i>',
                            '<cite>{{ title }}</cite>',
                        '</a>',
                    '</dd>',
                    '{{# }); }}',
                '</dl>',
                '{{# } }}',
            '</li>',
            '{{# }); }}'
        ].join(''),
        body: '<iframe src=""></iframe>'
    };

    /**
     * 布局对象
     * @constructor
     */
    var Layout = function () {
        var THIS = this,
            BODY = $('.layui-body'),
            SIDE = $('.layui-side'),
            IFRAME,
            DRAWER_INDEX;

        //渲染内容模板
        laytpl(Tpl.body).render({}, function (html) {
            IFRAME = BODY.html(html).find('iframe');
        });

        /**
         * 刷新当前iframe
         * @returns {Layout}
         */
        this.reload = function () {
            IFRAME.prop('src', IFRAME.prop('src'));
            return this;
        };

        /**
         * 渲染侧栏菜单
         * @param menu 要渲染的菜单结构,提供一个规范格式的数组
         * @returns {Layout}
         */
        this.sideMenuRender = function (menu) {
            if (typeof menu === "object") {
                laytpl(Tpl.menu).render(menu, function (html) {
                    var sideNav = SIDE.find('.layui-nav.layui-nav-tree');
                    if (sideNav[0]) {
                        sideNav.fadeOut(200, function () {
                            sideNav.html(html).fadeIn(200).find('.lau-nav-header:first').click();
                        });
                    }
                });
            }
            return this;
        };

        /**
         * 弹出右侧抽屉
         * @param options layer选项
         * @returns {Layout}
         */
        this.drawer = function (options) {
            DRAWER_INDEX = layer.open($.extend({
                type: 1,
                id: "drawer",
                anim: -1,
                title: false,
                closeBtn: false,
                offset: "r",
                shade: 0.1,
                shadeClose: true,
                skin: "layui-anim layui-anim-rl lau-drawer",
                area: "300px"
            }, options));
            return this;
        };

        /**
         * 关闭抽屉
         * @returns {Layout}
         */
        this.drawerClose = function () {
            layer.close(DRAWER_INDEX);
            return this;
        };

        /**
         * 跳转/打开选项卡
         * @param href 打开的链接
         * @returns {Layout}
         */
        this.go = function (href) {
            IFRAME.prop('src', $.trim(href));
            return this;
        };

        //监听锚点打开页面
        $(document).on('click', '*[lau-href]', function () {
            var _this = $(this), href = _this.attr('lau-href');
            if (_this.parents('.lau-nav-item')[0]) {
                if (!_this.next('.lau-nav-child')[0]) {
                    THIS.go(href);
                }
            } else {
                THIS.go(href);
            }
        });

        //监听侧栏缩进
        $(document).on('click', '.lau-side-fold', function () {
            SIDE.toggleClass('lau-mini');
        });

        //监听菜单展开
        $(document).on('click', '.lau-nav-header', function () {
            var _this = $(this);
            if (_this.next()[0]) {
                _this.parent().toggleClass('lau-open').siblings().removeClass('lau-open');
            } else {
                _this.parent().siblings().removeClass('lau-open');
            }
        });

        //MINI菜单下显示tips
        $(document).on('mouseenter', '.layui-side.lau-mini .lau-nav-item a', function () {
            layer.tips($(this).find('cite').text(), this, {time: 1000, tips: [2, '#53616F']});
        });
    };

    exports('lau', new Layout());
});
