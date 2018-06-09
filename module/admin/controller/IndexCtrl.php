<?php
namespace module\admin\controller;

use module\admin\logic\Permission;

/**
 * Class IndexCtrl
 * @package module\admin\controller
 */
class IndexCtrl extends Authorize
{
    /**
     * 首页
     * @return string
     */
    public function index()
    {
        if ($this->request->isAjax()) {
            return $this->renderJson(0, '', Permission::getUserMenus($this->userInfo['id']));
        }
        return $this->render();
    }
}
