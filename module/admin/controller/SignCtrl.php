<?php
namespace module\admin\controller;

use lying\event\ActionEvent;
use lying\service\Response;
use module\admin\logic\UserLogin;

/**
 * Class SignCtrl
 * @package module\admin\controller
 */
class SignCtrl extends Basic
{
    /**
     * 已经登录跳转到首页
     * @param ActionEvent $event
     * @throws \Exception
     */
    public function beforeAction(ActionEvent $event)
    {
        parent::beforeAction($event);
        if (UserLogin::isLogin() && $event->action != 'logout') {
            exit($this->response->redirect('/')->send());
        }
    }

    /**
     * 登录
     * @return string
     */
    public function login()
    {
        if ($this->request->isAjax() && $this->request->isPost()) {
            if (!$this->captcha->check(P('captcha'), 'login')) {
                return $this->renderJson(1, '验证码错误');
            } elseif (!UserLogin::doLogin(P('username'), P('password'), P('_csrf'), P('remember'))) {
                return $this->renderJson(1, '用户名或密码错误');
            } else {
                return $this->renderJson(0, '登陆成功');
            }
        }
        return $this->render();
    }

    /**
     * 退出登录
     * @return Response
     */
    public function logout()
    {
        UserLogin::doLogout();
        return $this->response->redirect('login');
    }

    /**
     * 验证码
     */
    public function captcha()
    {
        $this->captcha->render('login');
    }
}
