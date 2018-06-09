<?php
namespace module\admin\controller;

use module\admin\logic\Permission;
use module\admin\logic\UserLogin;

/**
 * Class Authorize
 * @package module\admin\controller
 */
class Authorize extends Basic
{
    /**
     * @var array
     */
    protected $userInfo;

    /**
     * @inheritdoc
     */
    protected function init()
    {
        parent::init();
        if (!UserLogin::isLogin()) {
            exit($this->response->redirect('sign/login')->send());
        }
        $this->userInfo = UserLogin::getLoginInfo();
        $this->assign('USER', $this->userInfo);

        $auth = function ($code) {
            return Permission::check($this->userInfo['id'], $code);
        };
        $this->assign('AUTH', $auth);

        $router = \Lying::$maker->router;
        if (!$auth(implode('/', [$router->controller(), $router->action()]))) {
            if ($this->request->isAjax()) {
                exit($this->response->setContent($this->renderJson(403, '您没有权限'))->send());
            } else {
                exit($this->response->setContent($this->render('/403'))->send());
            }
        }
    }
}
