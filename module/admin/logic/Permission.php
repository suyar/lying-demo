<?php
namespace module\admin\logic;

use model\RbacPermissionModel;
use model\RbacRoleModel;
use model\RbacRolePermissionModel;
use model\RbacUserRoleModel;

/**
 * Class Permission
 * @package module\admin\logic
 */
class Permission
{
    /**
     * @var array 权限列表
     */
    protected static $permissions;

    /**
     * 获取用户所有权限列表,有可能是从缓存取的
     * @param int $userId 用户ID
     * @param bool $refresh 是否刷新缓存,默认否
     * @return array
     */
    public static function getUserPermissions($userId, $refresh = false)
    {
        if (!$refresh && self::$permissions !== null) {
            return self::$permissions;
        }

        $cache = \Lying::$maker->cache;
        $permissions = $cache->get('permission_' . $userId);

        if ($refresh || $permissions === false) {
            $userRoles = RbacUserRoleModel::find()
                ->select(['role_id'])
                ->leftJoin(RbacRoleModel::table(), 'role_id=id')
                ->where(['user_id'=>$userId, 'enable'=>1]);

            $userPermissions = RbacRolePermissionModel::find()
                ->select(['permission_id'])
                ->distinct()
                ->where(['role_id'=>$userRoles]);

            $permissions = RbacPermissionModel::find()
                ->where(['id'=>$userPermissions, 'enable'=>1])
                ->orderBy(['type', 'sort', 'id'])
                ->asArray()
                ->all();

            $cache->set('permission_' . $userId, $permissions);
        }
        return self::$permissions = $permissions;
    }

    /**
     * 获取用户菜单
     * @param int $userId 用户ID
     * @param bool $refresh 是否刷新缓存,默认否
     * @return array
     */
    public static function getUserMenus($userId, $refresh = false)
    {
        $permissions = self::getUserPermissions($userId, $refresh);
        $menus = [];
        foreach ($permissions as $parent) {
            if ($parent['type'] == 0 && $parent['show']) {
                $menu = [
                    'title' => $parent['name'],
                    'icon' => $parent['icon'],
                    'href' => $parent['code'] ? url($parent['code']) : '',
                    'list' => [],
                ];
                foreach ($permissions as $child) {
                    if ($child['pid'] == $parent['id'] && $child['show']) {
                        $menu['list'][] = [
                            'title' => $child['name'],
                            'icon' => $child['icon'],
                            'href' => $child['code'] ? url($child['code']) : '',
                        ];
                    }
                }
                $menus[] = $menu;
            }
        }
        return $menus;
    }

    /**
     * 获取是否有权限
     * @param int $userId 用户ID
     * @param string $code 权限标识
     * @return bool
     */
    public static function check($userId, $code)
    {
        $permissions = self::getUserPermissions($userId);
        foreach ($permissions as $permission) {
            if ($permission['code'] === $code) {
                return true;
            }
        }
        return false;
    }
}
