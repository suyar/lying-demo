<?php
namespace module\admin\logic;

use model\RbacUserModel;

/**
 * Class UserLogin
 * @package module\admin\logic
 */
class UserLogin
{
    /**
     * @var string 鉴权标识
     */
    private static $AUTH_KEY = 'AUTH_ADMIN';

    /**
     * 判断是否已经登陆
     * @return bool
     */
    public static function isLogin()
    {
        if (\Lying::$maker->session->exists(self::$AUTH_KEY)) {
            return true;
        } elseif ($remember = \Lying::$maker->cookie->get(sha1(self::$AUTH_KEY))) {
            $result =  self::doLogin($remember['username'], $remember['password']);
            if ($result == false) {
                \Lying::$maker->cookie->remove(sha1(self::$AUTH_KEY));
            }
            return $result;
        } else {
            return false;
        }
    }

    /**
     * 登录操作
     * @param string $username 用户名
     * @param string $password 密码
     * @param string $csrf 签名密钥,默认无,使用原始密码
     * @param mixed $remember 是否记住密码,默认否
     * @return bool
     */
    public static function doLogin($username, $password, $csrf = '', $remember = false)
    {
        if ($user = RbacUserModel::find()->where(['username'=>$username])->asArray()->one()) {
            $cpassword = $csrf ? hash_hmac('sha256', $user['password'], $csrf) : $user['password'];
            if (strcmp($password, $cpassword) === 0) {
                \Lying::$maker->session->set(self::$AUTH_KEY, $user);
                if ($remember) {
                    \Lying::$maker->cookie->set(sha1(self::$AUTH_KEY), ['username'=>$user['username'], 'password'=>$user['password']], time() + 604800);
                }
                return true;
            }
        }
        return false;
    }

    /**
     * 获取登录用户信息
     * @param bool $refresh 是否从数据库刷新用户信息
     * @return array|false 如果用户已经登陆,返回用户信息,否则返回false
     */
    public static function getLoginInfo($refresh = false)
    {
        if (self::isLogin()) {
            $info = \Lying::$maker->session->get(self::$AUTH_KEY);
            if ($refresh) {
                $info = RbacUserModel::find()->where(['id'=>$info['id']])->asArray()->one();
                \Lying::$maker->session->set(self::$AUTH_KEY, $info);
            }
            return $info;
        }
        return false;
    }

    /**
     * 退出登录
     */
    public static function doLogout()
    {
        if (self::isLogin()) {
            \Lying::$maker->cookie->remove(sha1(self::$AUTH_KEY));
            \Lying::$maker->session->destroy();
        }
    }
}
