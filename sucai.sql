-- phpMyAdmin SQL Dump
-- version 4.7.9
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: 2019-05-14 09:50:32
-- 服务器版本： 5.6.37-log
-- PHP Version: 7.1.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sucai`
--

-- --------------------------------------------------------

--
-- 表的结构 `attach`
--

CREATE TABLE `attach` (
  `attach_id` int(11) UNSIGNED NOT NULL COMMENT 'ID',
  `site_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '站点ID',
  `request_url` varchar(2500) NOT NULL DEFAULT '' COMMENT '请求url地址',
  `response_url` varchar(2500) NOT NULL DEFAULT '' COMMENT '返回url地址',
  `site_code_type` varchar(1000) NOT NULL DEFAULT '' COMMENT 'code类型',
  `site_code` varchar(1000) NOT NULL DEFAULT '' COMMENT '请求地址唯一标识',
  `filename` varchar(2500) NOT NULL DEFAULT '' COMMENT '文件名称',
  `filesize` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '文件大小',
  `savename` varchar(2500) NOT NULL DEFAULT '' COMMENT '保存路径',
  `downloads` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '下载次数',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '访问时间',
  `create_ip` varchar(20) NOT NULL DEFAULT '' COMMENT '访问ip',
  `update_time` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '最后访问时间',
  `update_ip` varchar(20) NOT NULL DEFAULT '' COMMENT '最后访问ip',
  `local_file` varchar(255) NOT NULL DEFAULT '' COMMENT '临时文件',
  `download_time` varchar(20) NOT NULL DEFAULT '' COMMENT '下载耗时',
  `upload_time` varchar(20) NOT NULL DEFAULT '' COMMENT '上传耗时',
  `button_name` varchar(255) NOT NULL DEFAULT '' COMMENT '下载按钮名称',
  `queue_error` varchar(2500) NOT NULL DEFAULT '' COMMENT '队列执行错误信息',
  `delete_time` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '数据删除时间',
  `cookie_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '获取该数据使用的COOKIE_ID',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0-新建，1-下载中，2-已下载，3-上传中，4-上传成功'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='附件';

-- --------------------------------------------------------

--
-- 表的结构 `card`
--

CREATE TABLE `card` (
  `card_id` varchar(255) NOT NULL DEFAULT '' COMMENT 'ID',
  `valid_time` int(11) NOT NULL DEFAULT '0' COMMENT '有效期，秒',
  `access_times` text NOT NULL COMMENT '单站点权限次数',
  `account_times` int(11) NOT NULL DEFAULT '0' COMMENT '账户总解析次数',
  `create_uid` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '生成用户',
  `use_uid` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '使用用户',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '解析时间',
  `create_ip` varchar(20) NOT NULL DEFAULT '' COMMENT '解析ip',
  `use_time` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '使用时间',
  `use_ip` varchar(20) NOT NULL DEFAULT '' COMMENT '使用ip',
  `from` varchar(255) NOT NULL DEFAULT '' COMMENT '权限刷新时间',
  `delete_time` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '数据删除时间',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1-可用，0-已使用，-1失效'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='充值卡';

-- --------------------------------------------------------

--
-- 表的结构 `jobs`
--

CREATE TABLE `jobs` (
  `id` int(11) UNSIGNED NOT NULL COMMENT 'ID',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '名称',
  `queue` varchar(255) NOT NULL DEFAULT '' COMMENT '类型',
  `payload` longtext NOT NULL COMMENT '执行任务文件',
  `attempts` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '已执行次数',
  `reserved` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '重发次数',
  `reserved_at` int(11) UNSIGNED DEFAULT NULL COMMENT '上次执行时间',
  `available_at` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '下次执行时间',
  `created_at` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建时间',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态：为1正常，为0禁用'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='队列';

--
-- 转存表中的数据 `jobs`
--

INSERT INTO `jobs` (`id`, `title`, `queue`, `payload`, `attempts`, `reserved`, `reserved_at`, `available_at`, `created_at`, `status`) VALUES
(1, '下载文件', 'download', '{\"job\":\"common\\/Download\",\"data\":\"\"}', 29, 1, 1557798617, 1549940197, 1549940196, 1),
(2, '上传文件', 'upload', '{\"job\":\"common\\/Upload\",\"data\":\"\"}', 29, 1, 1557798616, 1549940197, 1549940196, 1),
(3, '刷新Cookie', 'cookie', '{\"job\":\"common\\/RefreshCookie\",\"data\":\"\"}', 29, 1, 1557798618, 1549940197, 1549940196, 1),
(4, '刷新下载次数', 'default', '{\"job\":\"common\\/ResetTimes\",\"data\":\"\"}', 29, 1, 1557798621, 1549940197, 1549940196, 1),
(5, '系统任务', 'default', '{\"job\":\"common\\/Common\",\"data\":\"\"}', 29, 1, 1557798622, 1549940197, 1549940196, 1);

-- --------------------------------------------------------

--
-- 表的结构 `member`
--

CREATE TABLE `member` (
  `uid` int(11) UNSIGNED NOT NULL COMMENT '用户ID',
  `username` varchar(30) NOT NULL DEFAULT '' COMMENT '账户名，可用作登陆不能重复',
  `password` varchar(255) NOT NULL DEFAULT '' COMMENT '登录密码',
  `password_see` varchar(255) NOT NULL DEFAULT '' COMMENT '登录密码可见',
  `email` varchar(100) NOT NULL DEFAULT '' COMMENT '邮箱地址',
  `mobile` varchar(20) NOT NULL DEFAULT '' COMMENT '手机号',
  `email_verify` tinyint(1) NOT NULL DEFAULT '0' COMMENT '邮箱认证状态，0未认证，1已认证',
  `mobile_verify` tinyint(1) NOT NULL DEFAULT '0' COMMENT '手机认证状态，0未认证，1已认证',
  `balance` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '账户余额',
  `new_notice` mediumint(8) UNSIGNED NOT NULL DEFAULT '0' COMMENT '未读通知',
  `register_time` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '注册时间',
  `register_ip` varchar(20) NOT NULL DEFAULT '' COMMENT '注册ip',
  `last_time` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '最后访问时间',
  `last_ip` varchar(20) NOT NULL DEFAULT '' COMMENT '最后访问IP',
  `delete_time` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '数据删除时间',
  `out_time` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '用户过期时间',
  `type` varchar(255) NOT NULL DEFAULT 'member' COMMENT 'system-管理员，proxy-代理，member-会员',
  `site_access` text NOT NULL COMMENT '下载权限',
  `reset_times` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '权限刷新时间',
  `from` varchar(255) NOT NULL DEFAULT '' COMMENT '账号来源',
  `parse_times` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '账户已解析总次数',
  `parse_max_times` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '账户拥有总解析次数，0-无限制',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '-2-已注销，-1-禁止访问，0-待审核，1-正常'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='用户资料';

--
-- 转存表中的数据 `member`
--

INSERT INTO `member` (`uid`, `username`, `password`, `password_see`, `email`, `mobile`, `email_verify`, `mobile_verify`, `balance`, `new_notice`, `register_time`, `register_ip`, `last_time`, `last_ip`, `delete_time`, `out_time`, `type`, `site_access`, `reset_times`, `from`, `parse_times`, `parse_max_times`, `status`) VALUES
(1, 'admin', '$2y$10$a0j7Iv.eHOs2Wd3/CbPKNunWfBIDWmnVgtsVz6xfhKWFsZxzcCary', '', '', '', 0, 0, '0.00', 0, 1549771692, '::1', 1557797616, '171.114.168.19', 0, 0, 'system', '{\"1\":{\"day_used\":\"0\",\"max_used\":\"0\",\"day\":\"10\",\"all\":\"20\"},\"2\":{\"day_used\":\"0\",\"max_used\":\"0\",\"day\":\"10\",\"all\":\"20\"},\"3\":{\"day_used\":\"0\",\"max_used\":\"0\",\"day\":\"10\",\"all\":\"20\"},\"4\":{\"day_used\":\"0\",\"max_used\":\"0\",\"day\":\"10\",\"all\":\"20\"},\"5\":{\"day_used\":\"0\",\"max_used\":\"0\",\"day\":\"10\",\"all\":\"20\"},\"6\":{\"day_used\":\"0\",\"max_used\":\"0\",\"day\":\"10\",\"all\":\"20\"},\"7\":{\"day_used\":\"0\",\"max_used\":\"0\",\"day\":\"10\",\"all\":\"20\"},\"8\":{\"day_used\":\"0\",\"max_used\":\"0\",\"day\":\"10\",\"all\":\"20\"},\"9\":{\"day_used\":\"0\",\"max_used\":\"0\",\"day\":\"10\",\"all\":\"20\"},\"10\":{\"day_used\":\"0\",\"max_used\":\"0\",\"day\":\"10\",\"all\":\"20\"},\"11\":{\"day_used\":\"0\",\"max_used\":\"0\",\"day\":\"10\",\"all\":\"20\"},\"12\":{\"day_used\":\"0\",\"max_used\":\"0\",\"day\":\"10\",\"all\":\"20\"},\"13\":{\"day_used\":\"0\",\"max_used\":\"0\",\"day\":\"10\",\"all\":\"20\"},\"14\":{\"day_used\":\"0\",\"max_used\":\"0\",\"day\":\"10\",\"all\":\"20\"},\"15\":{\"day_used\":\"0\",\"max_used\":\"0\",\"day\":\"10\",\"all\":\"20\"},\"16\":{\"day_used\":\"0\",\"max_used\":\"0\",\"day\":\"10\",\"all\":\"20\"},\"17\":{\"day_used\":\"0\",\"max_used\":\"0\",\"day\":\"10\",\"all\":\"20\"},\"18\":{\"day_used\":\"0\",\"max_used\":\"0\",\"day\":\"10\",\"all\":\"20\"},\"19\":{\"day_used\":\"0\",\"max_used\":\"0\",\"day\":\"10\",\"all\":\"20\"},\"20\":{\"day_used\":\"0\",\"max_used\":\"0\",\"day\":\"10\",\"all\":\"20\"},\"21\":{\"day_used\":\"0\",\"max_used\":\"0\",\"day\":\"10\",\"all\":\"20\"}}', 20190514, '', 0, 0, 1);

-- --------------------------------------------------------

--
-- 表的结构 `member_access`
--

CREATE TABLE `member_access` (
  `access_id` int(11) UNSIGNED NOT NULL COMMENT 'ID',
  `uid` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '用户ID',
  `site_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '站点ID',
  `day_used` mediumint(8) NOT NULL DEFAULT '0' COMMENT '今日已使用',
  `all_used` mediumint(8) NOT NULL DEFAULT '0' COMMENT '共计使用',
  `day_max` mediumint(8) NOT NULL DEFAULT '0' COMMENT '单日最大解析次数',
  `all_max` mediumint(8) NOT NULL DEFAULT '0' COMMENT '共计最大解析次数',
  `out_time` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '权限过期时间',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0-权限被关闭，1-已开启权限'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='用户权限';

-- --------------------------------------------------------

--
-- 表的结构 `member_log`
--

CREATE TABLE `member_log` (
  `log_id` int(11) UNSIGNED NOT NULL COMMENT 'ID',
  `uid` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '用户ID',
  `site_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '站点ID',
  `parse_url` varchar(255) NOT NULL DEFAULT '' COMMENT '解析地址',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '解析时间',
  `create_ip` varchar(20) NOT NULL DEFAULT '' COMMENT '解析ip',
  `times` smallint(6) UNSIGNED NOT NULL DEFAULT '0' COMMENT '解析消耗次数',
  `delete_time` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '数据删除时间',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0-解析失败，1-解析成功'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='解析记录';

-- --------------------------------------------------------

--
-- 表的结构 `setting`
--

CREATE TABLE `setting` (
  `key` varchar(255) NOT NULL DEFAULT '' COMMENT '变量名',
  `value` mediumtext NOT NULL COMMENT '变量值'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='全局设置';

--
-- 转存表中的数据 `setting`
--

INSERT INTO `setting` (`key`, `value`) VALUES
('site_open', '1'),
('allow_register', '0'),
('must_login', '1'),
('site_name', '吾爱素材'),
('logo_name', '吾爱素材'),
('AccessKeyId', ''),
('AccessKeySecret', ''),
('Endpoint', 'http://oss-cn-shanghai.aliyuncs.com'),
('Bucket', ''),
('qq', '123456'),
('footer', '<footer>\r\n	<div class=\"container text-center\">\r\n		<p>© 2019 123456</p>\r\n		<p>\r\n			<span><a href=\"#\">素材解析</a>&nbsp;</span>\r\n			<span><a href=\"#\">网站首页</a></span>\r\n		</p>\r\n		<p>本站内容完全来自于互联网，并不对其进行任何编辑或修改。本站用户不能侵犯包括他人的著作权在内的知识产权以及其他权利</p>\r\n	</div>\r\n</footer>'),
('proxy_card_numbers', '100'),
('proxy_card_account_times', '10'),
('proxy_card_rule', '@@##**'),
('auto_save_file', '1'),
('parse_between_time', '15'),
('reset_cookie_times', '20190914'),
('email_host', 'smtp.qq.com'),
('email_port', '465'),
('email_username', ''),
('email_password', ''),
('email_fromname', ''),
('version', '20191012');

-- --------------------------------------------------------

--
-- 表的结构 `verify_code`
--

CREATE TABLE `verify_code` (
  `id` int(11) UNSIGNED NOT NULL COMMENT 'ID',
  `uid` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'uid',
  `email` varchar(255) NOT NULL DEFAULT '' COMMENT '邮箱地址',
  `code` varchar(255) NOT NULL DEFAULT '' COMMENT '验证码代码',
  `out_time` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '过期时间'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='验证码表';

-- --------------------------------------------------------

--
-- 表的结构 `web_site`
--

CREATE TABLE `web_site` (
  `site_id` smallint(6) UNSIGNED NOT NULL COMMENT 'ID',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '网站名称',
  `url` varchar(255) NOT NULL DEFAULT '' COMMENT '官网地址',
  `url_regular` varchar(500) NOT NULL DEFAULT '' COMMENT 'url特征',
  `download_url` varchar(255) NOT NULL DEFAULT '' COMMENT '下载地址',
  `bucket` varchar(255) NOT NULL DEFAULT '' COMMENT '存储使用的oss Bucket',
  `type` varchar(255) NOT NULL DEFAULT '' COMMENT '站点类型',
  `param` text NOT NULL COMMENT '额外配置参数',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态：为1正常，为0禁用'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='站点';

--
-- 转存表中的数据 `web_site`
--

INSERT INTO `web_site` (`site_id`, `title`, `url`, `url_regular`, `download_url`, `bucket`, `type`, `param`, `status`) VALUES
(1, '觅元素', 'http://www.51yuansu.com/', '51yuansu.com', 'http://www.51yuansu.com/sc/lcvnlvrkig.html', '', '', '', 1),
(2, '我图网', 'https://www.ooopic.com/', 'ooopic.com', 'https://www.ooopic.com/pic_27796570.html', '', '', '', 1),
(3, '千图网', 'https://www.58pic.com/', '58pic.com', 'http://www.58pic.com/newpic/33513860.html', '', '', '', 1),
(4, '昵图网', 'http://www.nipic.com/', 'nipic.com', 'http://www.nipic.com/show/22717419.html', '', '', '', 1),
(5, '90设计', 'http://90sheji.com/', '90sheji.com', 'http://90sheji.com/sucai/22176591.html', '', '', '', 1),
(6, '千库网', 'http://588ku.com/', '588ku.com', 'http://588ku.com/ycpng/11716645.html', '', '', '', 1),
(7, '包图网', 'https://ibaotu.com/', 'ibaotu.com', 'https://ibaotu.com/sucai/18002368.html', '', '', '', 1),
(8, '摄图网', 'http://699pic.com/', '699pic.com', 'http://699pic.com/tupian-501104528.html', '', '', '', 1),
(9, 'CSDN下载', 'https://download.csdn.net/', 'download.csdn.net', 'https://download.csdn.net/download/qgy879765368/10961370', '', '', '', 1),
(10, '稻壳儿', 'http://www.docer.com/', 'docer.com', 'http://detail.docer.com/4512476.html', '', '', '', 1),
(11, '百度文库', 'https://wenku.baidu.com/', 'wenku.baidu.com', 'https://wenku.baidu.com/view/72264a1b6d85ec3a87c24028915f804d2b16877f.html', '', '', '', 1),
(12, '17素材', 'http://www.17sucai.com/', '17sucai.com', 'http://www.17sucai.com/pins/32451.html', '', '', '', 1),
(13, '熊猫办公', 'http://www.tukuppt.com/', 'tukuppt.com', 'http://www.tukuppt.com/muban/pgxdkwmz.html', '', '', '', 1),
(14, '92素材', 'http://www.92sucai.com/', '92sucai.com', 'http://www.92sucai.com/ae/31712.html', '', '', '', 1),
(15, '演界网', 'http://www.yanj.cn/', 'yanj.cn', 'http://www.yanj.cn/goods-177783.html', '', '', '', 1),
(16, '彼岸图', 'http://pic.netbian.com/', 'pic.netbian.com', 'http://pic.netbian.com/tupian/23507.html', '', '', '', 1),
(17, '绘艺素材', 'https://www.huiyi8.com/', 'huiyi8.com', 'https://www.huiyi8.com/sc/1100690.html', '', '', '', 1),
(18, '图品汇', 'https://www.88tph.com/', '88tph.com', 'https://www.88tph.com/sucai/12508222.html', '', '', '', 1),
(19, '觅知网', 'https://www.51miz.com/', '51miz.com', 'http://www.51miz.com/sucai/999440.html', '', '', '', 1),
(20, '六图网', 'https://www.16pic.com/', '16pic.com', 'https://www.16pic.com/pic/pic_8717237.html', '', '', '', 1),
(21, '全图网', 'https://www.125pic.com/', '125pic.com', 'http://www.125pic.com/sucai/104678', '', '', '', 1),
(22, '风云办公', 'http://ppt.dwuva.com/', 'ppt.dwuva.com', 'http://ppt.dwuva.com/scb/a6b61897.html', '', '', '', 1),
(23, '风云办公2', 'http://www.ppt118.com', 'ppt118.com', 'http://www.ppt118.com/scb/a6b61897.html', '', '', '', 1),
(24, '易图网', 'http://www.yipic.cn/', 'yipic.cn', 'http://www.yipic.cn/sucai/4413479.html', '', '', '', 1),
(25, '图行天下', 'http://www.photophoto.cn/', 'photophoto.cn', 'http://www.photophoto.cn/pic/32218471.html', '', '', '', 1),
(26, '万素网', 'http://669pic.com/', '669pic.com', 'http://669pic.com/sc/173705.html', '', '', '', 1),
(27, '快图网', 'http://www.kuaipng.com', 'kuaipng.com', 'http://www.kuaipng.com/sucai/20889.html', '', '', '', 1);

-- --------------------------------------------------------

--
-- 表的结构 `web_site_cookie`
--

CREATE TABLE `web_site_cookie` (
  `cookie_id` mediumint(8) UNSIGNED NOT NULL COMMENT 'cookie_id',
  `site_id` smallint(6) UNSIGNED NOT NULL DEFAULT '0' COMMENT '网站ID',
  `third_id` smallint(6) UNSIGNED NOT NULL DEFAULT '0' COMMENT '网站ID',
  `name` varchar(500) NOT NULL DEFAULT '' COMMENT '名称，方便自己查看',
  `content` text NOT NULL COMMENT 'cookie内容',
  `used_times` smallint(6) UNSIGNED NOT NULL DEFAULT '0' COMMENT '今日已使用次数',
  `times` smallint(6) UNSIGNED NOT NULL DEFAULT '0' COMMENT '该COOKIE每日可用次数',
  `type` varchar(255) NOT NULL DEFAULT '' COMMENT '留空为素材站，third-为第三方站点',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态：为1正常，为0禁用'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='站点Cookie';

-- --------------------------------------------------------

--
-- 表的结构 `web_third`
--

CREATE TABLE `web_third` (
  `third_id` smallint(6) UNSIGNED NOT NULL COMMENT 'ID',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '网站名称',
  `url` varchar(255) NOT NULL DEFAULT '' COMMENT '官网地址',
  `url_regular` varchar(255) NOT NULL DEFAULT '' COMMENT '官网地址',
  `site_access` text NOT NULL COMMENT '站点下载权限',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态：为1正常，为0禁用'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='第三方站点';

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attach`
--
ALTER TABLE `attach`
  ADD PRIMARY KEY (`attach_id`);

--
-- Indexes for table `card`
--
ALTER TABLE `card`
  ADD PRIMARY KEY (`card_id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `member`
--
ALTER TABLE `member`
  ADD PRIMARY KEY (`uid`);

--
-- Indexes for table `member_access`
--
ALTER TABLE `member_access`
  ADD PRIMARY KEY (`access_id`);

--
-- Indexes for table `member_log`
--
ALTER TABLE `member_log`
  ADD PRIMARY KEY (`log_id`);

--
-- Indexes for table `setting`
--
ALTER TABLE `setting`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `verify_code`
--
ALTER TABLE `verify_code`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `web_site`
--
ALTER TABLE `web_site`
  ADD PRIMARY KEY (`site_id`);

--
-- Indexes for table `web_site_cookie`
--
ALTER TABLE `web_site_cookie`
  ADD PRIMARY KEY (`cookie_id`);

--
-- Indexes for table `web_third`
--
ALTER TABLE `web_third`
  ADD PRIMARY KEY (`third_id`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `attach`
--
ALTER TABLE `attach`
  MODIFY `attach_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID';

--
-- 使用表AUTO_INCREMENT `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID', AUTO_INCREMENT=6;

--
-- 使用表AUTO_INCREMENT `member`
--
ALTER TABLE `member`
  MODIFY `uid` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '用户ID', AUTO_INCREMENT=2;

--
-- 使用表AUTO_INCREMENT `member_access`
--
ALTER TABLE `member_access`
  MODIFY `access_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID';

--
-- 使用表AUTO_INCREMENT `member_log`
--
ALTER TABLE `member_log`
  MODIFY `log_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID';

--
-- 使用表AUTO_INCREMENT `verify_code`
--
ALTER TABLE `verify_code`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID';

--
-- 使用表AUTO_INCREMENT `web_site`
--
ALTER TABLE `web_site`
  MODIFY `site_id` smallint(6) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID', AUTO_INCREMENT=28;

--
-- 使用表AUTO_INCREMENT `web_site_cookie`
--
ALTER TABLE `web_site_cookie`
  MODIFY `cookie_id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'cookie_id';

--
-- 使用表AUTO_INCREMENT `web_third`
--
ALTER TABLE `web_third`
  MODIFY `third_id` smallint(6) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID';
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
