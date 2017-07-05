-- UTF8

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `product_detail`
-- ----------------------------
DROP TABLE IF EXISTS `product_detail`;
CREATE TABLE `product_detail` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pId` int(10) unsigned NOT NULL,
  `recordId` int(10) unsigned NOT NULL,
  `version` int(10) unsigned NOT NULL,
  `file` varchar(100) NOT NULL,
  `author` varchar(30) NOT NULL COMMENT '代码提交人',
  `action` varchar(10) NOT NULL DEFAULT '?',
  `cTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Table structure for `product_record`
-- ----------------------------
DROP TABLE IF EXISTS `product_record`;
CREATE TABLE `product_record` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pId` int(11) NOT NULL,
  `version` int(11) NOT NULL,
  `isBeta` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '仿真机版本（全量）',
  `isPartBeta` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '仿真机版本（增量）',
  `isOnline` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '产品机版本（全量）',
  `isPartOnline` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '产品机版本（赠量）',
  `comment` varchar(200) NOT NULL,
  `author` varchar(30) NOT NULL COMMENT '变更提交人',
  `cTime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `idx_pid` (`pId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Table structure for `project`
-- ----------------------------
DROP TABLE IF EXISTS `project`;
CREATE TABLE `project` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL COMMENT '项目名称',
  `markPath` varchar(100) NOT NULL COMMENT '路径标识',
  `pRepo` varchar(100) NOT NULL COMMENT '生产环境仓库',
  `pType` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '生产环境仓库类型1-svn2-git',
  `pUser` varchar(30) NOT NULL COMMENT '生产环境账户',
  `pPwd` varchar(30) NOT NULL COMMENT '生产环境密码',
  `dRepo` varchar(100) NOT NULL COMMENT '开发环境仓库',
  `dType` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '仓库类型1-svn2-git',
  `dUser` varchar(30) NOT NULL COMMENT '开发环境账户',
  `dPwd` varchar(30) NOT NULL COMMENT '开发环境密码',
  `betaServers` text COMMENT '仿真环境服务器',
  `prodServers` text COMMENT '生产环境服务器',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '状态',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for `project_svn`
-- ----------------------------
DROP TABLE IF EXISTS `project_svn`;
CREATE TABLE `project_svn` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL COMMENT '项目名称',
  `markPath` varchar(100) NOT NULL COMMENT '路径标识',
  `pRepo` varchar(100) NOT NULL COMMENT '生产环境svn仓库',
  `pUser` varchar(30) NOT NULL COMMENT '生产环境svn账户',
  `pPwd` varchar(30) NOT NULL COMMENT '生产环境svn密码',
  `dRepo` varchar(100) NOT NULL COMMENT '开发环境svn仓库',
  `dUser` varchar(30) NOT NULL COMMENT '开发环境svn账户',
  `dPwd` varchar(30) NOT NULL COMMENT '开发环境svn密码',
  `betaServers` text COMMENT '仿真环境服务器',
  `productServers` text COMMENT '生产环境服务器',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '状态',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for `user`
-- ----------------------------
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL COMMENT '用户名',
  `svnName` varchar(60) NOT NULL,
  `pwd` char(32) NOT NULL COMMENT '密码',
  `salt` char(6) NOT NULL,
  `type` tinyint(3) unsigned NOT NULL DEFAULT '2' COMMENT '用户类型1普通用户2管理员',
  `projects` varchar(200) DEFAULT NULL COMMENT '项目',
  `rights` text COMMENT '权限',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '状态0无效1有效',
  `cTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `mTime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unq_name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of user
-- ----------------------------
INSERT INTO `user` VALUES ('1', 'admin', 'all', '6eb3df9f6007ef1ae2d6c90720dac16b', '84d075', '2', '', '', '1', '2017-07-01 14:43:01', '2017-07-02 18:14:14');