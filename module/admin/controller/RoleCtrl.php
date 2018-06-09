<?php
namespace module\admin\controller;

use model\RbacPermissionModel;
use model\RbacRoleModel;
use model\RbacRolePermissionModel;
use model\RbacUserRoleModel;

/**
 * Class RoleCtrl
 * @package module\admin\controller
 */
class RoleCtrl extends Authorize
{
    /**
     * 角色管理
     * @return string
     */
    public function index()
    {
        if ($this->request->isAjax()) {
            $roles = RbacRoleModel::find()->asArray()->all();
            return $this->renderTable($roles, count($roles));
        }
        return $this->render();
    }

    /**
     * 创建角色
     * @return string
     */
    public function create()
    {
        if ($this->request->isPost()) {
            $role = new RbacRoleModel();
            $role->name = P('name');
            $role->enable = P('enable');
            try {
                $this->db->begin();
                if ($role->save()) {
                    if ($permissions = P('permission')) {
                        $data = [];
                        foreach ($permissions as $permissionId) {
                            $data[] = [$role->id, $permissionId];
                        }
                        RbacRolePermissionModel::find()->batchInsert(RbacRolePermissionModel::table(), ['role_id', 'permission_id'], $data);
                    }
                }
                $this->db->commit();
                return $this->renderJson(0, '保存成功');
            } catch (\Exception $e) {
                $this->db->rollBack();
                return $this->renderJson(1, '保存失败');
            }
        } elseif ($this->request->isAjax()) {
            return $this->renderJson(0, '', $this->getPermissions());
        }
    }

    /**
     * 更新角色
     * @param int $id 角色ID
     * @return string
     */
    public function update($id = null)
    {
        if ($this->request->isPost()) {
            $role = RbacRoleModel::findOne(['id'=>P('id')]);
            if ($role) {
                $role->name = P('name');
                $role->enable = P('enable');
                try {
                    $this->db->begin();
                    if ($role->save() !== false) {
                        RbacRolePermissionModel::deleteAll(['role_id'=>$role->id]);
                        if ($permissions = P('permission')) {
                            $data = [];
                            foreach ($permissions as $permissionId) {
                                $data[] = [$role->id, $permissionId];
                            }
                            RbacRolePermissionModel::find()->batchInsert(RbacRolePermissionModel::table(), ['role_id', 'permission_id'], $data);
                        }
                    }
                    $this->db->commit();
                    return $this->renderJson(0, '保存成功');
                } catch (\Exception $e) {
                    $this->db->rollBack();
                    return $this->renderJson(1, '保存失败');
                }
            }
            return $this->renderJson(1, '保存失败');
        } elseif ($this->request->isAjax()) {
            return $this->renderJson(0, '', $this->getPermissions($id));
        }
    }

    /**
     * 删除角色
     * @return string
     */
    public function delete()
    {
        if ($this->request->isPost()) {
            $role = RbacRoleModel::findOne(['id'=>P('id')]);
            if ($role) {
                try {
                    $this->db->begin();
                    $role->delete();
                    RbacRolePermissionModel::deleteAll(['role_id'=>$role->id]);
                    RbacUserRoleModel::deleteAll(['role_id'=>$role->id]);
                    $this->db->commit();
                    return $this->renderJson(0, '删除成功');
                } catch (\Exception $e) {
                    $this->db->rollBack();
                }
            }
            return $this->renderJson(1, '删除失败');
        }
    }

    /**
     * 获取权限列表
     * @param int $roleId 根据某个角色设置勾选
     * @return array
     */
    private function getPermissions($roleId = null)
    {
        $rolePermissions = [];
        if ($roleId) {
            $rolePermissions = RbacRolePermissionModel::find()
                ->select(['permission_id'])
                ->where(['role_id'=>$roleId])
                ->column();
        }

        $permissions = RbacPermissionModel::find()->orderBy(['sort', 'id'])->asArray()->all();
        $data = [];
        foreach ($permissions as $p) {
            if ($p['type'] == 0) {
                $p['childs'] = [];
                $p['check'] = in_array($p['id'], $rolePermissions) ? 1 : 0;
                foreach ($permissions as $c) {
                    if ($c['pid'] == $p['id']) {
                        $c['childs'] = [];
                        $c['check'] = in_array($c['id'], $rolePermissions) ? 1 : 0;
                        foreach ($permissions as $a) {
                            if ($a['pid'] == $c['id']) {
                                $a['check'] = in_array($a['id'], $rolePermissions) ? 1 : 0;
                                $c['childs'][] = $a;
                            }
                        }
                        $p['childs'][] = $c;
                    }
                }
                $data[] = $p;
            }
        }
        return $data;
    }
}
