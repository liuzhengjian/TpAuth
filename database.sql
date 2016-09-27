-- phpMyAdmin SQL Dump
-- version 4.4.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: 2016-09-27 07:09:14
-- 服务器版本： 5.6.19
-- PHP Version: 5.6.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `tp5`
--

-- --------------------------------------------------------

--
-- 表的结构 `cck_auth_group`
--

CREATE TABLE IF NOT EXISTS `cck_auth_group` (
  `id` smallint(6) unsigned NOT NULL COMMENT '用户组id，自增主键',
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '用户组名称',
  `module` varchar(50) NOT NULL DEFAULT '' COMMENT '所属模块',
  `description` varchar(80) NOT NULL DEFAULT '' COMMENT '描述信息',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态1:可用0:不可用-1：删除',
  `rules` text COMMENT '用户组拥有的规则id，多个用,隔开',
  `create_time` datetime DEFAULT NULL,
  `update_time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户组表';

-- --------------------------------------------------------

--
-- 表的结构 `cck_auth_group_access`
--

CREATE TABLE IF NOT EXISTS `cck_auth_group_access` (
  `uid` smallint(6) unsigned NOT NULL COMMENT '用户id',
  `group_id` smallint(6) unsigned NOT NULL COMMENT '角色id'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户与用户组关联表';

-- --------------------------------------------------------

--
-- 表的结构 `cck_auth_rule`
--

CREATE TABLE IF NOT EXISTS `cck_auth_rule` (
  `id` mediumint(8) unsigned NOT NULL COMMENT '规则id,自增主键',
  `module` varchar(50) NOT NULL DEFAULT '' COMMENT '规则所属module',
  `type` tinyint(2) NOT NULL DEFAULT '1' COMMENT '1-url;2-主菜单',
  `name` varchar(80) NOT NULL DEFAULT '' COMMENT '规则唯一英文标识',
  `title` varchar(80) NOT NULL DEFAULT '' COMMENT '规则中文描述',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否有效(0:无效,1:有效)',
  `condition` varchar(300) NOT NULL DEFAULT '' COMMENT '规则附加条件'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='权限规则表';

-- --------------------------------------------------------

--
-- 表的结构 `cck_user`
--

CREATE TABLE IF NOT EXISTS `cck_user` (
  `id` smallint(6) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `create_time` datetime DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  `remember_token` varchar(255) NOT NULL DEFAULT '',
  `name` varchar(50) NOT NULL,
  `birth` date DEFAULT NULL,
  `gender` varchar(1) NOT NULL DEFAULT '',
  `national` varchar(20) NOT NULL DEFAULT '',
  `mobile` varchar(20) NOT NULL DEFAULT '',
  `qq` varchar(20) NOT NULL DEFAULT '',
  `email` varchar(50) NOT NULL DEFAULT '',
  `avatar` int(10) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '用户状态',
  `last_login_time` datetime DEFAULT NULL,
  `last_login_ip` varchar(30) NOT NULL DEFAULT '',
  `login_count` int(10) NOT NULL DEFAULT '0' COMMENT '登录次数',
  `orgs_id` smallint(6) NOT NULL DEFAULT '0' COMMENT '用户所属部门',
  `remark` varchar(255) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cck_auth_group`
--
ALTER TABLE `cck_auth_group`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cck_auth_group_access`
--
ALTER TABLE `cck_auth_group_access`
  ADD UNIQUE KEY `uid_group_id` (`uid`,`group_id`),
  ADD KEY `uid` (`uid`),
  ADD KEY `group_id` (`group_id`);

--
-- Indexes for table `cck_auth_rule`
--
ALTER TABLE `cck_auth_rule`
  ADD PRIMARY KEY (`id`),
  ADD KEY `module` (`module`,`status`,`type`);

--
-- Indexes for table `cck_user`
--
ALTER TABLE `cck_user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `index_users_on_username` (`username`),
  ADD KEY `index_users_on_remember_token` (`remember_token`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cck_auth_group`
--
ALTER TABLE `cck_auth_group`
  MODIFY `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户组id，自增主键';
--
-- AUTO_INCREMENT for table `cck_auth_rule`
--
ALTER TABLE `cck_auth_rule`
  MODIFY `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '规则id,自增主键';
--
-- AUTO_INCREMENT for table `cck_user`
--
ALTER TABLE `cck_user`
  MODIFY `id` smallint(6) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
