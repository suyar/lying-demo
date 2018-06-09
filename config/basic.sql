-- --------------------------------------------------------
-- 主机:                           121.196.204.163
-- 服务器版本:                        5.7.20 - MySQL Community Server (GPL)
-- 服务器操作系统:                      linux-glibc2.12
-- HeidiSQL 版本:                  9.5.0.5278
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


-- 导出 lying_blog 的数据库结构
DROP DATABASE IF EXISTS `lying_blog`;
CREATE DATABASE IF NOT EXISTS `lying_blog` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `lying_blog`;

-- 导出  表 lying_blog.lying_rbac_permission 结构
DROP TABLE IF EXISTS `lying_rbac_permission`;
CREATE TABLE IF NOT EXISTS `lying_rbac_permission` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL COMMENT '名称',
  `icon` varchar(100) NOT NULL DEFAULT '' COMMENT '图标',
  `code` varchar(255) NOT NULL DEFAULT '' COMMENT '标识',
  `pid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '父级',
  `type` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '类型，0父菜单，1子菜单，2细节菜单',
  `sort` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `show` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '是否显示，0隐藏，1显示',
  `enable` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '是否可用，0禁用，1可用',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8 COMMENT='权限表';

-- 正在导出表  lying_blog.lying_rbac_permission 的数据：~-1 rows (大约)
DELETE FROM `lying_rbac_permission`;
/*!40000 ALTER TABLE `lying_rbac_permission` DISABLE KEYS */;
INSERT INTO `lying_rbac_permission` (`id`, `name`, `icon`, `code`, `pid`, `type`, `sort`, `show`, `enable`) VALUES
	(1, '后台框架', '', '', 0, 0, 0, 0, 1),
	(2, '后台首页', '', 'index/index', 1, 1, 0, 0, 1),
	(3, '系统设置', '', '', 0, 0, 1, 1, 1),
	(4, '角色管理', '', 'role/index', 3, 1, 0, 1, 1),
	(5, '添加角色', '', 'role/create', 4, 2, 0, 1, 1),
	(6, '更新角色', '', 'role/update', 4, 2, 0, 1, 1),
	(7, '删除角色', '', 'role/delete', 4, 2, 0, 1, 1),
	(8, '用户管理', '', 'user/index', 3, 1, 0, 1, 1),
	(9, '添加用户', '', 'user/create', 8, 2, 0, 1, 1),
	(10, '更新用户', '', 'user/update', 8, 2, 0, 1, 1),
	(11, '删除用户', '', 'user/delete', 8, 2, 0, 1, 1),
	(12, '刷新权限', '', 'user/refresh', 8, 2, 0, 1, 1),
	(13, '菜单管理', '', 'menu/index', 3, 1, 0, 1, 1),
	(14, '添加菜单', '', 'menu/create', 13, 2, 0, 1, 1),
	(15, '更新菜单', '', 'menu/update', 13, 2, 0, 1, 1),
	(16, '删除菜单', '', 'menu/delete', 13, 2, 0, 1, 1);
/*!40000 ALTER TABLE `lying_rbac_permission` ENABLE KEYS */;

-- 导出  表 lying_blog.lying_rbac_role 结构
DROP TABLE IF EXISTS `lying_rbac_role`;
CREATE TABLE IF NOT EXISTS `lying_rbac_role` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `enable` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='角色表';

-- 正在导出表  lying_blog.lying_rbac_role 的数据：~-1 rows (大约)
DELETE FROM `lying_rbac_role`;
/*!40000 ALTER TABLE `lying_rbac_role` DISABLE KEYS */;
INSERT INTO `lying_rbac_role` (`id`, `name`, `enable`) VALUES
	(1, '管理员', 1);
/*!40000 ALTER TABLE `lying_rbac_role` ENABLE KEYS */;

-- 导出  表 lying_blog.lying_rbac_role_permission 结构
DROP TABLE IF EXISTS `lying_rbac_role_permission`;
CREATE TABLE IF NOT EXISTS `lying_rbac_role_permission` (
  `role_id` int(10) unsigned NOT NULL,
  `permission_id` int(10) unsigned NOT NULL,
  KEY `role_id` (`role_id`),
  KEY `permission_id` (`permission_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='角色权限表';

-- 正在导出表  lying_blog.lying_rbac_role_permission 的数据：~-1 rows (大约)
DELETE FROM `lying_rbac_role_permission`;
/*!40000 ALTER TABLE `lying_rbac_role_permission` DISABLE KEYS */;
INSERT INTO `lying_rbac_role_permission` (`role_id`, `permission_id`) VALUES
	(1, 3),
	(1, 2),
	(1, 1),
	(1, 4),
	(1, 11),
	(1, 9),
	(1, 10),
	(1, 5),
	(1, 6),
	(1, 7),
	(1, 8),
	(1, 12),
	(1, 13),
	(1, 14),
	(1, 15),
	(1, 16);
/*!40000 ALTER TABLE `lying_rbac_role_permission` ENABLE KEYS */;

-- 导出  表 lying_blog.lying_rbac_user 结构
DROP TABLE IF EXISTS `lying_rbac_user`;
CREATE TABLE IF NOT EXISTS `lying_rbac_user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` char(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='用户表';

-- 正在导出表  lying_blog.lying_rbac_user 的数据：~-1 rows (大约)
DELETE FROM `lying_rbac_user`;
/*!40000 ALTER TABLE `lying_rbac_user` DISABLE KEYS */;
INSERT INTO `lying_rbac_user` (`id`, `username`, `password`) VALUES
	(1, 'revoke', 'e9689fcb4c303022f6ce2f2e261f6c1a132b8478cc4e5f97fe38360df6445a80');
/*!40000 ALTER TABLE `lying_rbac_user` ENABLE KEYS */;

-- 导出  表 lying_blog.lying_rbac_user_role 结构
DROP TABLE IF EXISTS `lying_rbac_user_role`;
CREATE TABLE IF NOT EXISTS `lying_rbac_user_role` (
  `user_id` int(10) unsigned NOT NULL,
  `role_id` int(10) unsigned NOT NULL,
  KEY `user_id` (`user_id`),
  KEY `role_id` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户角色关联表';

-- 正在导出表  lying_blog.lying_rbac_user_role 的数据：~-1 rows (大约)
DELETE FROM `lying_rbac_user_role`;
/*!40000 ALTER TABLE `lying_rbac_user_role` DISABLE KEYS */;
INSERT INTO `lying_rbac_user_role` (`user_id`, `role_id`) VALUES
	(1, 1);
/*!40000 ALTER TABLE `lying_rbac_user_role` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
