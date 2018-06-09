<?php
namespace module\admin\controller;

use model\RbacPermissionModel;
use model\RbacRolePermissionModel;

/**
 * Class MenuCtrl
 * @package module\admin\controller
 */
class MenuCtrl extends Authorize
{
    /**
     * 菜单管理
     * @return string
     */
    public function index()
    {
        if ($this->request->isAjax()) {
            $menus = RbacPermissionModel::find()->orderBy(['sort', 'id'])->asArray()->all();

            $data = [];
            foreach ($menus as $p) {
                if ($p['type'] == 0) {
                    $p['level'] = '├─';
                    $data[] = $p;

                    foreach ($menus as $c) {
                        if ($c['pid'] == $p['id']) {
                            $c['level'] = '│　　├─';
                            $data[] = $c;

                            foreach ($menus as $a) {
                                if ($a['pid'] == $c['id']) {
                                    $a['level'] = '│　　│　　├─';
                                    $data[] = $a;
                                }
                            }
                        }
                    }
                }
            }

            return $this->renderTable($data, count($data));
        }
        return $this->render();
    }

    /**
     * 新增菜单
     * @return string
     */
    public function create()
    {
        if ($this->request->isPost()) {
            $menu = new RbacPermissionModel();
            $menu->name = P('name');
            $menu->icon = P('icon');
            $menu->code = P('code');
            $menu->pid = P('pid');
            $menu->type = P('type');
            $menu->sort = P('sort');
            $menu->show = P('show');
            $menu->enable = P('enable');
            if ($menu->save()) {
                return $this->renderJson(0, '保存成功');
            } else {
                return $this->renderJson(1, '保存失败');
            }
        }
    }

    /**
     * 删除菜单
     * @return string
     */
    public function delete()
    {
        if ($this->request->isPost()) {
            $menu = RbacPermissionModel::findOne(['id'=>P('id')]);
            if ($menu) {
                $subMenus = RbacPermissionModel::find()->where(['pid'=>$menu->id])->count();
                if ($subMenus) {
                    return $this->renderJson(1, '删除失败，请先删除相关子级');
                } else {
                    try {
                        $this->db->begin();
                        $menu->delete();
                        RbacRolePermissionModel::deleteAll(['permission_id'=>$menu->id]);
                        $this->db->commit();
                        return $this->renderJson(0, '删除成功');
                    } catch (\Exception $e) {
                        $this->db->rollBack();
                    }
                }
            }
            return $this->renderJson(1, '删除失败');
        }
    }

    /**
     * 更新菜单
     * @return string
     */
    public function update()
    {
        if ($this->request->isPost()) {
            $menu = RbacPermissionModel::findOne(['id'=>P('id')]);
            if ($menu) {
                $menu->name = P('name');
                $menu->icon = P('icon');
                $menu->code = P('code');
                $menu->sort = P('sort');
                $menu->show = P('show');
                $menu->enable = P('enable');
                if ($menu->save() !== false) {
                    return $this->renderJson(0, '保存成功');
                }
            }
            return $this->renderJson(1, '保存失败');
        }
    }
}
