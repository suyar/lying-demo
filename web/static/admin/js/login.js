layui.define(['layer', 'form', 'tips'], function(exports) {
    var form = layui.form,
        layer = layui.layer,
        $ = layui.$,
        tips = layui.tips;

    //点击弹出登录框
    $('.sign-icon').click(function () {
        layer.open({
            type: 1,
            shade: false,
            title: false,
            closeBtn: false,
            area: '380px',
            content: $('.sign-form')
        });
    });

    //刷新验证码
    var captchaImg = $('.sign-captcha'), captchaSrc = captchaImg.attr('src');
    captchaImg.click(function () {
        this.src = captchaSrc + '?_t=' + Math.random();
    });

    //登陆
    form.on('submit(login)', function (data) {
        for (var i in data.field) {
            switch (i) {
                case 'username':
                    if (!/^\S{2,}$/.test(data.field[i])) {
                        tips.warning('请输入正确的账号');
                        return false;
                    }
                    break;
                case 'password':
                    if (!/^\S{6,}$/.test(data.field[i])) {
                        tips.warning('请输入正确的密码');
                        return false;
                    }
                    data.field[i] = CryptoJS.HmacSHA256(data.field[i], data.field.username).toString();
                    data.field[i] = CryptoJS.HmacSHA256(data.field[i], data.field._csrf).toString();
                    break;
                case 'captcha':
                    if (!/^[a-zA-z0-9]{4,}$/.test(data.field[i])) {
                        tips.warning('请输入正确的验证码');
                        return false;
                    }
                    break;
            }
        }

        //登陆中
        tips.loading('登陆中...', 0, -1);

        //发送登陆表单
        $.post(location.href, data.field, function (json) {
            if (json.code == 0) {
                tips.success(json.msg, function () {
                    location.href = '/';
                });
            } else {
                tips.error(json.msg, function () {
                    captchaImg.attr('src', captchaSrc + '?_t=' + Math.random());
                });
            }
        }, 'json');

        return false;
    });

    exports('login', {});
});
