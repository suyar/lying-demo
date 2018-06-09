<?php
namespace module\admin\controller;

use model\RbacRoleModel;
use model\RbacUserModel;
use model\RbacUserRoleModel;
use module\admin\logic\Permission;

/**
 * Class UserCtrl
 * @package module\admin\controller
 */
class UserCtrl extends Authorize
{
    /**
     * 用户管理
     * @return string
     */
    public function index()
    {
        if ($this->request->isAjax()) {
            $roles = RbacUserRoleModel::find()
                ->select(['user_id', 'roles'=>'GROUP_CONCAT([[name]])'])
                ->leftJoin(RbacRoleModel::table(), 'role_id=id')
                ->groupBy(['user_id']);

            $users = RbacUserModel::find()
                ->select(['id', 'username', 'roles'])
                ->leftJoin(['roles'=>$roles], 'user_id=id')
                ->asArray()
                ->all();

            return $this->renderTable($users, count($users));
        }
        return $this->render();
    }

    /**
     * 添加角色
     * @return string
     */
    public function create()
    {
        if ($this->request->isPost()) {
            $user = new RbacUserModel();
            $user->username = P('username');
            $user->password = hash_hmac('sha256', P('password'), $user->username);
            try {
                $this->db->begin();
                if ($user->save()) {
                    if ($roles = P('role')) {
                        $data = [];
                        foreach ($roles as $roleId) {
                            $data[] = [$user->id, $roleId];
                        }
                        RbacUserRoleModel::find()->batchInsert(RbacUserRoleModel::table(), ['user_id', 'role_id'], $data);
                    }
                }
                $this->db->commit();
                return $this->renderJson(0, '保存成功');
            } catch (\Exception $e) {
                $this->db->rollBack();
                return $this->renderJson(1, '保存失败');
            }
        } elseif ($this->request->isAjax()) {
            return $this->renderJson(0, '', $this->getRoles());
        }
    }

    /**
     * 更新用户
     * @param int $id 用户ID
     * @return string
     */
    public function update($id = null)
    {
        if ($this->request->isPost()) {
            $user = RbacUserModel::findOne(['id'=>P('id')]);
            if ($user) {
                $username = P('username');
                $password = P('password');
                $oldPassword = P('old_password');
                if ($password && $oldPassword) {
                    if ($oldPassword === $password) {
                        return $this->renderJson(1, '新旧密码不能一样');
                    }
                    $oldPassword = hash_hmac('sha256', $oldPassword, $user->username);
                    if ($oldPassword !== $user->password) {
                        return $this->renderJson(1, '旧密码不正确');
                    }
                    $password = hash_hmac('sha256', $password, $username);
                    $user->password = $password;
                }
                $user->username = $username;
                try {
                    $this->db->begin();
                    if ($user->save() !== false) {
                        RbacUserRoleModel::deleteAll(['user_id'=>$user->id]);
                        if ($roles = P('role')) {
                            $data = [];
                            foreach ($roles as $roleId) {
                                $data[] = [$user->id, $roleId];
                            }
                            RbacUserRoleModel::find()->batchInsert(RbacUserRoleModel::table(), ['user_id', 'role_id'], $data);
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
            return $this->renderJson(0, '', $this->getRoles($id));
        }
    }

    /**
     * 删除用户
     * @return string
     */
    public function delete()
    {
        if ($this->request->isPost()) {
            $user = RbacUserModel::findOne(['id'=>P('id')]);
            if ($user) {
                try {
                    $this->db->begin();
                    $user->delete();
                    RbacUserRoleModel::deleteAll(['user_id'=>$user->id]);
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
     * 刷新用户权限缓存
     * @param int $id 用户ID
     * @return string
     */
    public function refresh($id)
    {
        Permission::getUserPermissions($id, true);
        return $this->renderJson(0, '权限刷新成功');
    }

    /**
     * 获取角色列表
     * @param int $userId 根据某个用户勾选
     * @return array
     */
    private function getRoles($userId = null)
    {
        $userRoles = [];
        if ($userId) {
            $userRoles = RbacUserRoleModel::find()
                ->select(['role_id'])
                ->where(['user_id'=>$userId])
                ->column();
        }

        $roles = RbacRoleModel::find()->asArray()->all();
        $data = [];
        foreach ($roles as $r) {
            $r['check'] = in_array($r['id'], $userRoles) ? 1 : 0;
            $data[] = $r;
        }
        return $data;
    }
}
