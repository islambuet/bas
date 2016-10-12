/*
Navicat MySQL Data Transfer

Source Server         : local
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : shaiful_arm_ems

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2016-10-12 17:20:40
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `bas_history`
-- ----------------------------
DROP TABLE IF EXISTS `bas_history`;
CREATE TABLE `bas_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `controller` varchar(255) DEFAULT NULL,
  `table_id` int(11) NOT NULL,
  `table_name` varchar(50) NOT NULL,
  `data` varchar(255) NOT NULL,
  `user_id` varchar(20) NOT NULL,
  `action` varchar(20) NOT NULL,
  `date` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of bas_history
-- ----------------------------

-- ----------------------------
-- Table structure for `bas_history_hack`
-- ----------------------------
DROP TABLE IF EXISTS `bas_history_hack`;
CREATE TABLE `bas_history_hack` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `controller` varchar(255) DEFAULT NULL,
  `action` varchar(255) DEFAULT 'Active',
  `action_id` int(11) DEFAULT '99',
  `other_info` text,
  `date_created` int(11) DEFAULT '0',
  `date_created_string` varchar(255) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of bas_history_hack
-- ----------------------------

-- ----------------------------
-- Table structure for `bas_system_assigned_group`
-- ----------------------------
DROP TABLE IF EXISTS `bas_system_assigned_group`;
CREATE TABLE `bas_system_assigned_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `user_group` int(11) NOT NULL,
  `revision` int(4) NOT NULL DEFAULT '1',
  `date_created` int(11) NOT NULL DEFAULT '0',
  `user_created` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of bas_system_assigned_group
-- ----------------------------
INSERT INTO `bas_system_assigned_group` VALUES ('1', '1', '1', '1', '0', '0');

-- ----------------------------
-- Table structure for `bas_system_site_offline`
-- ----------------------------
DROP TABLE IF EXISTS `bas_system_site_offline`;
CREATE TABLE `bas_system_site_offline` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `status` varchar(11) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Active',
  `date_created` int(11) NOT NULL DEFAULT '0',
  `user_created` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of bas_system_site_offline
-- ----------------------------

-- ----------------------------
-- Table structure for `bas_system_task`
-- ----------------------------
DROP TABLE IF EXISTS `bas_system_task`;
CREATE TABLE `bas_system_task` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'TASK',
  `parent` int(11) NOT NULL DEFAULT '0',
  `controller` varchar(500) NOT NULL,
  `ordering` smallint(6) NOT NULL DEFAULT '9999',
  `icon` varchar(255) NOT NULL DEFAULT 'menu.png',
  `status` varchar(11) NOT NULL DEFAULT 'Active',
  `date_created` int(11) NOT NULL DEFAULT '0',
  `user_created` int(11) NOT NULL DEFAULT '0',
  `date_updated` int(11) DEFAULT NULL,
  `user_updated` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of bas_system_task
-- ----------------------------
INSERT INTO `bas_system_task` VALUES ('1', 'System Settings', 'MODULE', '0', '', '1', 'menu.png', 'Active', '1455625924', '1', '1455625924', '1');
INSERT INTO `bas_system_task` VALUES ('2', 'Module & Task', 'TASK', '1', 'Sys_module_task', '1', 'menu.png', 'Active', '1455625924', '1', '1455625924', '1');
INSERT INTO `bas_system_task` VALUES ('3', 'User Role', 'TASK', '1', 'Sys_user_role', '2', 'menu.png', 'Active', '1455625924', '1', '1455625924', '1');
INSERT INTO `bas_system_task` VALUES ('4', 'User Group', 'TASK', '1', 'Sys_user_group', '3', 'menu.png', 'Active', '1455625924', '1', '1455625924', '1');
INSERT INTO `bas_system_task` VALUES ('5', 'Assign User To Group', 'TASK', '1', 'Sys_assign_user_group', '4', 'menu.png', 'Active', '1466929864', '1', null, null);
INSERT INTO `bas_system_task` VALUES ('6', 'Site Offline', 'TASK', '1', 'Sys_site_offline', '5', 'menu.png', 'Active', '1466929894', '1', null, null);

-- ----------------------------
-- Table structure for `bas_system_user_group`
-- ----------------------------
DROP TABLE IF EXISTS `bas_system_user_group`;
CREATE TABLE `bas_system_user_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `status` varchar(11) NOT NULL DEFAULT 'Active',
  `ordering` tinyint(4) NOT NULL DEFAULT '99',
  `date_created` int(11) NOT NULL DEFAULT '0',
  `user_created` int(11) NOT NULL DEFAULT '0',
  `date_updated` int(11) DEFAULT NULL,
  `user_updated` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of bas_system_user_group
-- ----------------------------
INSERT INTO `bas_system_user_group` VALUES ('1', 'Super Admin', 'Active', '1', '1455625924', '1', '1455625924', '1');
INSERT INTO `bas_system_user_group` VALUES ('2', 'Admin', 'Active', '2', '1455777728', '1', null, null);

-- ----------------------------
-- Table structure for `bas_system_user_group_role`
-- ----------------------------
DROP TABLE IF EXISTS `bas_system_user_group_role`;
CREATE TABLE `bas_system_user_group_role` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_group_id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `action0` tinyint(2) DEFAULT '0',
  `action1` tinyint(2) DEFAULT '0',
  `action2` tinyint(2) DEFAULT '0',
  `action3` tinyint(2) DEFAULT '0',
  `action4` tinyint(2) DEFAULT '0',
  `action5` tinyint(2) DEFAULT '0',
  `action6` tinyint(2) DEFAULT '0',
  `revision` int(11) NOT NULL DEFAULT '1',
  `date_created` int(11) NOT NULL DEFAULT '0',
  `user_created` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of bas_system_user_group_role
-- ----------------------------
INSERT INTO `bas_system_user_group_role` VALUES ('1', '1', '2', '1', '1', '1', '1', '1', '1', null, '2', '1473507088', '1');
INSERT INTO `bas_system_user_group_role` VALUES ('2', '1', '3', '1', '1', '1', '1', '1', '1', null, '2', '1473507088', '1');
INSERT INTO `bas_system_user_group_role` VALUES ('3', '1', '4', '1', '1', '1', '1', '1', '1', null, '2', '1473507088', '1');
INSERT INTO `bas_system_user_group_role` VALUES ('4', '1', '5', '1', '1', '1', '1', '1', '1', null, '2', '1473507088', '1');
INSERT INTO `bas_system_user_group_role` VALUES ('5', '1', '6', '1', '1', '1', '1', '1', '1', null, '2', '1473507088', '1');
INSERT INTO `bas_system_user_group_role` VALUES ('7', '1', '2', '1', '1', '1', '1', '1', '1', '1', '1', '1476271159', '1');
INSERT INTO `bas_system_user_group_role` VALUES ('8', '1', '3', '1', '1', '1', '1', '1', '1', '1', '1', '1476271159', '1');
INSERT INTO `bas_system_user_group_role` VALUES ('9', '1', '4', '1', '1', '1', '1', '1', '1', '1', '1', '1476271159', '1');
INSERT INTO `bas_system_user_group_role` VALUES ('10', '1', '5', '1', '1', '1', '1', '1', '1', '1', '1', '1476271159', '1');
INSERT INTO `bas_system_user_group_role` VALUES ('11', '1', '6', '1', '1', '1', '1', '1', '1', '1', '1', '1476271159', '1');
