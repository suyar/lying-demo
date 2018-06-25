<?php
namespace module\admin\logic;

use model\RbacPermissionModel;

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

            $permissions = RbacPermissionModel::find()
                ->select(['p.*'])
                ->from(['p'=>'{{%rbac_permission}}'])
                ->leftJoin(['rp'=>'{{%rbac_role_permission}}'], 'p.id=rp.permission_id')
                ->leftJoin(['ur'=>'{{%rbac_user_role}}'], 'rp.role_id=ur.role_id AND ur.user_id=:user_id', [':user_id'=>$userId])
                ->leftJoin(['r'=>'{{%rbac_role}}'], 'r.id=ur.role_id AND r.`enable`=1')
                ->where(['p.enable'=>1])
                ->groupBy(['p.id'])
                ->orderBy(['p.type', 'p.sort', 'p.id'])
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
