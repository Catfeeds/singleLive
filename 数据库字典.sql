
--
-- Banner管理表 针对手机端端6大模块
--

CREATE TABLE `ms_banner` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` char(1) COLLATE utf8_bin NOT NULL COMMENT '模块类型 h-客房 f-餐饮 e-环境 a-体验活动 m-会员俱乐部 t-套餐',
  `imgs` text COLLATE utf8_bin COMMENT '关联files表id',
  `add_time` int(11) NOT NULL COMMENT '添加时间',
  `update_time` int(11) NOT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='banner表' AUTO_INCREMENT=1 ;

--
-- 表的结构 `ms_drawback`
--

CREATE TABLE IF NOT EXISTS `ms_drawback` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `orderId` int(11) DEFAULT NULL COMMENT '订单id',
  `createTime` int(11) DEFAULT NULL COMMENT '操作时间',
  `money` int(10) DEFAULT NULL COMMENT '退款金额',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=2 ;


-- --------------------------------------------------------

--
-- 表的结构 `files` 文件保存表
--

CREATE TABLE `ms_files` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `savepath` text NOT NULL COMMENT '保存路径',
  `savename` text NOT NULL COMMENT '保存名称',
  `name` varchar(200) NOT NULL COMMENT '原始名称',
  `size` varchar(10) NOT NULL COMMENT '文件的大小',
  `type` varchar(10) NOT NULL COMMENT '文件的MIME类型',
  `ext` varchar(10) NOT NULL COMMENT '文件的后缀类型',
  `md5` varchar(32) NOT NULL COMMENT 'md5哈希验证',
  `sha1` varchar(50) NOT NULL COMMENT 'sha1哈希验证',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0正常 1无用',
  PRIMARY KEY (`id`),
  KEY `status` (`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

--
-- 表的结构 `perm`
--

CREATE TABLE `ms_perm` (
  `perm_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `perm_type` varchar(20) COLLATE utf8_bin NOT NULL COMMENT '权限名称',
  `perm_url` varchar(30) COLLATE utf8_bin NOT NULL,
  `status` char(1) NOT NULL COMMENT '权限的分类 0后台权限',
  `perm_parentid` int(11) NOT NULL COMMENT '父级id',
  PRIMARY KEY (`perm_id`),
  KEY `perm_type` (`perm_type`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=40 ;

--
-- 转存表中的数据 `perm`
--

INSERT INTO `ms_perm` (`perm_id`, `perm_type`, `perm_url`, `status`, `perm_parentid`) VALUES
(1, '首页', 'Index', 0, 0),
(2, '会员管理', '', 0,0),
(3, '客房管理', '', 0,0),
(4, '优惠套餐', '', 0,0),
(5, '订单管理', '', 0,0),
(6, '财务管理', '', 0,0),
(7, '内容管理', '', 0,0),
(8, '管理员管理', '', 0,0),
(9, '电子券管理', '', 0,0),
(10, '消息管理', '', 0,0),
(11, '系统设置', '', 0,0),
(12, '会员列表', 'MemberList', 0,2),
(13, '级别管理', 'Grades', 0,2),
(14, '会员晋级', 'UsersLvup', 0,2),
(15, '积分统计', 'SorceCount', 0,2),
(16, '会员俱乐部', 'UserClub', 0,2),
(17, '客房分类', 'HouseType', 0,3),
(18, '客房列表', 'HouseList', 0,3),
(19, '套餐分类', 'Package', 0,4),
(20, '套餐列表', 'PackageList', 0,4),
(21, '订单列表', 'OrderList', 0,5),
(22, '退款订单', 'BackMoneyList', 0,5),
(23, '客户统计', 'UsersCount', 0,6),
(24, '订单统计', 'OrderCount', 0,6),
(25, '财务统计', 'FinanceCount', 0,6),
(26, '内容管理列表', 'ContentManage', 0,7),
(27, '权限管理', 'Perm', 0,8),
(28, '管理员', 'PermRoot', 0,8),
(29, '电子券列表', 'Coupon', 0,9),
(30, '电子券转赠记录', 'CouponGive', 0,9),
(31, '电子券使用记录', 'CouponUsed', 0,9),
(32, '系统消息', 'SystemNews', 0,10),
(33, '活动消息', 'ActiveNews', 0,10),
(34, '网站设置', 'WebSite', 0,11),
(35, '备份数据库', 'DB', 0,11),
(36, '还原数据库', 'DBReduction', 0,11),
(37, '密码修改', 'Pwd', 0,11),
(38, '参数设置', 'Parameter', 0,11),
(39, '常见问题列表', 'Problem', 0,7);





-- --------------------------------------------------------

--
-- 表的结构 `perm_role`
--

CREATE TABLE `ms_perm_role` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `perm_id` int(11) NOT NULL COMMENT '权限id',
  `role_id` int(11) NOT NULL COMMENT '权限组id',
  PRIMARY KEY (`id`),
  KEY `perm_id` (`perm_id`),
  KEY `role_id` (`role_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;


--
-- 表的结构 `role`
--

CREATE TABLE `ms_role` (
  `role_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `role_type` varchar(20) COLLATE utf8_bin NOT NULL COMMENT '权限组名称',
  `role_info` varchar(50) COLLATE utf8_bin NOT NULL,
  `hotel` int(11) NOT NULL COMMENT '0后台管理组',
  `role_sta` char(1) COLLATE utf8_bin NOT NULL COMMENT '0正常 1禁用 9删除',
  PRIMARY KEY (`role_id`),
  KEY `role_sta` (`role_sta`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=2 ;


-- --------------------------------------------------------

--
-- 表的结构 `role_root`
--

CREATE TABLE `ms_role_root` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `root_id` int(11) NOT NULL COMMENT '人员id',
  `role_id` int(11) NOT NULL COMMENT '权限组id',
  PRIMARY KEY (`id`),
  KEY `root_id` (`root_id`),
  KEY `role_id` (`role_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=2 ;

--
-- 转存表中的数据 `role_root`
--

INSERT INTO `ms_role_root` (`id`, `root_id`, `role_id`) VALUES
(1, 1, 1);

-- --------------------------------------------------------

--
-- 表的结构 `root`
--

CREATE TABLE `ms_root` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` varchar(20) COLLATE utf8_bin NOT NULL COMMENT '用户名',
  `infoname` varchar(20) COLLATE utf8_bin NOT NULL COMMENT '管理员名称',
  `realname` varchar(20) COLLATE utf8_bin NOT NULL COMMENT '真实姓名',
  `number` varchar(50) COLLATE utf8_bin NOT NULL COMMENT '联系方式',
  `pwd` char(32) COLLATE utf8_bin NOT NULL COMMENT '登录密码',
  `status` tinyint(1) COLLATE utf8_bin NOT NULL COMMENT '0正常 1禁用 9删除',
  `admin` int(11) NOT NULL COMMENT '0超级管理员 root::id子账号',
  PRIMARY KEY (`id`),
  KEY `root` (`status`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=2 ;

--
-- 转存表中的数据 `root`
--

INSERT INTO `ms_root` (`id`, `name`, `infoname`, `realname`, `number`, `pwd`, `status`, `admin`) VALUES
(1, 'admin', '总管理员', '卓诚', '12345678910', '21232f297a57a5a743894a0e4a801fc3', '1', 0);

-- --------------------------------------------------------

--
-- 表的结构 `root_login`
--

CREATE TABLE `ms_root_login` (
  `login_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `root_id` int(11) NOT NULL COMMENT 'root::id',
  `login_time` varchar(30) COLLATE utf8_bin NOT NULL COMMENT '登录时间',
  `login_ip` varchar(30) COLLATE utf8_bin NOT NULL COMMENT 'IP',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0后台管理登录记录 1酒店管理登录记录',
  PRIMARY KEY (`login_id`),
  KEY `root_id` (`root_id`),
  KEY `login_time` (`login_time`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

--
--  用户表
--
CREATE TABLE `ms_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键 唯一标示',
  `realname` varchar(50) COLLATE utf8_bin NOT NULL COMMENT '真实姓名',
  `mobile` varchar(12) COLLATE utf8_bin NOT NULL COMMENT '用户手机号信息',
  `sex` tinyint(1) NOT NULL COMMENT '性别 1男 2女',
  `idCard` varchar(18) COLLATE utf8_bin NOT NULL COMMENT '身份号',
  `Email` varchar(30) COLLATE utf8_bin NOT NULL COMMENT '电子邮箱',
  `password` varchar(32) COLLATE utf8_bin NOT NULL COMMENT '密码32位(加密)',
  `no_md5` varchar(32) COLLATE utf8_bin NOT NULL COMMENT '无加密',
  `regLevel` int(11) NOT NULL COMMENT '注册级别id',
  `nowLevel` int(11) NOT NULL COMMENT '现级别id',
  `headImg` int(11) NOT NULL COMMENT '头像照片id',
  `createTime` int(11) NOT NULL,
  `updateTime` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL COMMENT '状态 1-正常 2-禁用 3-删除',
  `cardType` varchar(30) COLLATE utf8_bin NOT NULL COMMENT '证件类型 直接存文字'
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;



-- 系统配置表

CREATE TABLE `ms_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `key` varchar(100) COLLATE utf8_bin NOT NULL COMMENT '配置名',
  `value` longtext COLLATE utf8_bin NOT NULL COMMENT '配置值',
  `status` tinyint(1) COLLATE utf8_bin NOT NULL COMMENT '状态 0:后台配置 hotel::id:酒店配置',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;


-- 系统消息表

CREATE TABLE `ms_news` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `title` text COLLATE utf8_bin NOT NULL COMMENT '标题',
  `body` text COLLATE utf8_bin NOT NULL COMMENT '内容',
  `createTime` int(11) COLLATE utf8_bin NOT NULL COMMENT '发布日期',
  `updateTime` int(11) NOT NULL COMMENT '更新时间',
  `obj` char(10) COLLATE utf8_bin NOT NULL COMMENT 'all-全部用户 single-单个用户',
  `mobile` varchar(11) COLLATE utf8_bin NOT NULL DEFAULT '0' COMMENT '用户手机号(选择单个用户时),默认是0',
  `status` tinyint(1) COLLATE utf8_bin NOT NULL COMMENT '状态 1:正常 2:已禁用 9:删除',
  `type` varchar(10) COLLATE utf8_bin NOT NULL COMMENT 'sys-系统消息 act-活动消息',
  `users` int(11) NOT NULL COMMENT '0-群发消息 其他数字-指定用户的id',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;


-- 系统消息于用户连接表

CREATE TABLE `ms_news_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `news` int(11) NOT NULL  COMMENT 'news::id',
  `users` int(11) NOT NULL  COMMENT 'users::id',
  `status` tinyint(1) COLLATE utf8_bin NOT NULL COMMENT '状态 0:未读 1:已读 9:删除',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;



--
-- 表的结构 `ms_order_flow` 订单流水表
--

CREATE TABLE IF NOT EXISTS `ms_order_flow` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `orderhotel_id` int(11) NOT NULL COMMENT 'order_hotel的订单id',
  `hotel` int(11) NOT NULL COMMENT '对应酒店id',
  `orderhotel_time` int(11) NOT NULL COMMENT 'order_hotel订单的创建时间',
  `money` float(10,2) NOT NULL COMMENT '交易金额',
  `orderhotel_used` int(11) NOT NULL COMMENT '在酒店本次所用时间',
  `hotelName`  varchar(200)  COLLATE utf8_bin NOT NULL COMMENT '酒店名称',
  `roomName` varchar(30) COLLATE utf8_bin NOT NULL COMMENT '房间类型',
  `userName` varchar(30) COLLATE utf8_bin NOT NULL COMMENT '用户姓名',
  `orderNo` varchar(20) COLLATE utf8_bin NOT NULL COMMENT '订单编号',
  `startTime` int(11) COLLATE utf8_bin NOT NULL COMMENT '开始入住时间',
  `endTime` int(11) NOT NULL COMMENT '入住结束时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1; 



-- ---------------------------------------------------------------------------
--  新表全部加这
-- ---------------------------------------------------------------------------
--
--  客房、套餐分类表
--
CREATE TABLE IF NOT EXISTS `ms_house_cate` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(30) COLLATE utf8_bin NOT NULL COMMENT '分类名称',
  `mark` text COLLATE utf8_bin NOT NULL COMMENT '备注信息',
  `add_time` int(11) COLLATE utf8_bin NOT NULL COMMENT '插入时间',
  `update_time` int(11) COLLATE utf8_bin NOT NULL COMMENT '修改时间',
  `status` tinyint(1) NOT NULL COMMENT '状态 1-正常 2-禁用 3-删除',
  `type` char(1) COLLATE utf8_bin NOT NULL COMMENT 'h-客房 t-套餐',
  PRIMARY KEY (`id`)
)ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1;

--
--  客房管理表
--
CREATE TABLE IF NOT EXISTS `ms_house` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category` int(11) NOT NULL COMMENT '客房分类id',
  `name` varchar(30) COLLATE utf8_bin NOT NULL COMMENT '客房名称',
  `equipment` text COLLATE utf8_bin NOT NULL COMMENT '房间设备',
  `money` float(10,2) NOT NULL COMMENT '房间金额',
  `mark` text COLLATE utf8_bin NOT NULL COMMENT '房间描述',
  `back` text COLLATE utf8_bin NOT NULL COMMENT '订房须知',
  `come` text COLLATE utf8_bin NOT NULL COMMENT '入住通知',
  `change` text COLLATE utf8_bin NOT NULL COMMENT '更改订单',
  `sorce` int(11) NOT NULL COMMENT '反还积分',
  `paper` char(1) COLLATE utf8_bin NOT NULL COMMENT '是否可以使用电子卷 y-是 n-否',
  `imgs` varchar(50) COLLATE utf8_bin NOT NULL COMMENT '客房banner',
  `add_time` int(11) COLLATE utf8_bin NOT NULL COMMENT '插入时间',
  `update_time` int(11) COLLATE utf8_bin NOT NULL COMMENT '修改时间',
  `status` tinyint(1) NOT NULL COMMENT '状态字段 1-正常 2-已禁用 3-已删除',
  `total_num` int(11) NOT NULL COMMENT '房间总数',
  `word` text COLLATE utf8_bin NOT NULL COMMENT '房间简介',
  `pic` int(11) NOT NULL COMMENT '封面图',
  `push` tinyint(1) NOT NULL DEFAULT '2' COMMENT '是否设置为首页推荐: 1-推荐 2-不推荐',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1;

--
--  餐饮、环境、体验活动、会员俱乐部管理表(内容管理表)
--
CREATE TABLE IF NOT EXISTS `ms_environment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) COLLATE utf8_bin NOT NULL COMMENT '标题',
  `mark` text COLLATE utf8_bin NOT NULL COMMENT '描述',
  `imgs` varchar(50) COLLATE utf8_bin NOT NULL COMMENT 'banner',
  `add_time` int(11) COLLATE utf8_bin NOT NULL COMMENT '插入时间',
  `update_time` int(11) COLLATE utf8_bin NOT NULL COMMENT '修改时间',
  `type` char(1) COLLATE utf8_bin NOT NULL COMMENT 'f-餐饮 e-环境 a-体验活动 m-会员俱乐部',
  `word` text COLLATE utf8_bin NOT NULL COMMENT '简介',
  `pic` int(11) NOT NULL COMMENT '封面图',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1;

--
--  套餐内容设置表
--
CREATE TABLE IF NOT EXISTS `ms_package_set` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL COMMENT '关联套餐管理表的id',
  `title` varchar(30) COLLATE utf8_bin NOT NULL COMMENT '标题',
  `money` float(10,2) COLLATE utf8_bin NOT NULL COMMENT '单价',
  `attr` varchar(30) COLLATE utf8_bin NOT NULL COMMENT '规格/数量 这个字段就是让客户自己起名字',
  PRIMARY KEY (`id`)
)ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1;

--
--  套餐管理表
--
CREATE TABLE IF NOT EXISTS `ms_package` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category` int(11) NOT NULL COMMENT '套餐分类id',
  `title` varchar(30) COLLATE utf8_bin NOT NULL COMMENT '房间名称',
  `limit` int(4) NOT NULL COMMENT '每人限购份数',
  `mark` text COLLATE utf8_bin NOT NULL COMMENT '预定须知',
  `content` text COLLATE utf8_bin NOT NULL COMMENT '套餐详情',
  `packMoney` float(10,2) COLLATE utf8_bin NOT NULL COMMENT '套餐价',
  `sorce` int(11) NOT NULL COMMENT '反还积分数',
  `pic` int(11) NOT NULL COMMENT '封面图片',
  `add_time` int(11) NOT NULL COMMENT '添加时间',
  `update_time` int(11) NOT NULL COMMENT '修改时间',
  `status` tinyint(1) NOT NULL COMMENT '1-正常 2-禁用 3-删除',
  `total_num` int(11) NOT NULL COMMENT '套餐总数(实际就是房间总数)',
  `paper` char(1) COLLATE utf8_bin NOT NULL COMMENT '是否可以使用电子卷 y-是 n-否',
  `word` text COLLATE utf8_bin NOT NULL COMMENT '简介',
  `push` tinyint(1) NOT NULL DEFAULT '2' COMMENT '是否设置为首页推荐: 1-推荐 2-不推荐',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1;



--
--  常见问题管理表
--
CREATE TABLE IF NOT EXISTS `ms_problem` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) COLLATE utf8_bin NOT NULL COMMENT '问题名称',
  `content` text COLLATE utf8_bin NOT NULL COMMENT '问题答案',
  `add_time` int(11) COLLATE utf8_bin NOT NULL COMMENT '插入时间',
  `update_time` int(11) COLLATE utf8_bin NOT NULL COMMENT '修改时间',
   PRIMARY KEY (`id`)
)ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1;

--
--  级别设置表
--
CREATE TABLE IF NOT EXISTS `ms_grades` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) COLLATE utf8_bin NOT NULL COMMENT '级别名称',
  `sorce` varchar(255) COLLATE utf8_bin NOT NULL COMMENT '所需积分',
  `pic` int(11) NOT NULL COMMENT '图片id',
  `content` text COLLATE utf8_bin NOT NULL COMMENT '详情介绍',
  `sort` int(11) NOT NULL COMMENT '排序',
  `status` tinyint(1) NOT NULL COMMENT '1-正常 2-禁用 3-删除',
  PRIMARY KEY (`id`),
  UNIQUE KEY(`sort`)
)ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1;

--
--  会员升级记录表
--
CREATE TABLE IF NOT EXISTS `ms_user_lvup` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userID` int(255) NOT NULL COMMENT '会员id',
  `before` int(255) NOT NULL COMMENT '升级前级别',
  `after` int(255) NOT NULL COMMENT '升级后级别',
  `createTime` int(255) NOT NULL COMMENT '升级时间',
  `achs` int(11) NOT NULL  COMMENT '花费积分',
  `admin` int(11) NOT NULL DEFAULT '0' COLLATE utf8_bin COMMENT '说明 0:自动升级 admins::id 管理员修改',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;


--
--  会员积分变更记录表
--
CREATE TABLE IF NOT EXISTS `ms_user_sorce` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userID` int(11) NOT NULL COMMENT '会员id',
  `type` char(10) COLLATE utf8_bin NOT NULL COMMENT 'consume-消费返积分 exchange-兑换电子卷 lvup-购买积分卡升级',
  `sorce` int(11) NOT NULL DEFAULT '0' COMMENT '积分数',
  `method` char(10) COLLATE utf8_bin NOT NULL COMMENT 'plus-加 sub-减',
  `createTime` int(11) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;


--
--  订单表
--
CREATE TABLE IF NOT EXISTS `ms_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userID` int(11) NOT NULL COMMENT '用户id',
  `roomID` int(11) NOT NULL COMMENT '房间id，关联客房id和套餐里的房间id',
  `orderNo` varchar(16) COLLATE utf8_bin NOT NULL COMMENT '订单编号',
  `username` varchar(30) COLLATE utf8_bin NOT NULL COMMENT '填写人姓名',
  `mobile` varchar(11) COLLATE utf8_bin NOT NULL COMMENT '填写人手机号码',
  `sex` tinyint(1) NOT NULL COMMENT '填写人性别',
  `email` varchar(30) COLLATE utf8_bin NOT NULL COMMENT '填写人邮箱',
  `person` int(3) NOT NULL COMMENT '成年人数量',
  `child` int(3) NOT NULL COMMENT '儿童数量',
  `price` float(10,2) COLLATE utf8_bin NOT NULL COMMENT '订单金额',
  `mark` text COLLATE utf8_bin NOT NULL COMMENT '备注信息',
  `inTime` int(11) NOT NULL COMMENT '入住时间',
  `outTime` int(11) NOT NULL COMMENT '离开时间',
  `coupon` int(11) NOT NULL COMMENT '关联优惠券id 若用户成功勾选了,则有,若无,则写入0',
  `num` int(5) NOT NULL COMMENT '购买数量(仅用于套餐) 非套餐(客房)默认写入1',
  `status` tinyint(1) NOT NULL COMMENT '付款状态 8-待付款 9-已入住 1-已支付 2-已完成 3-已超时(设置时间内未完成支付) 4-已取消 5-退款审核中(用户),由此状态总后台显示确认退款和驳回 6-已退款 7-已驳回',
  `createTime` int(11) NOT NULL COMMENT '订单生成时间',
  `updateTime` int(11) NOT NULL COMMENT '订单更新时间',
  `date` DATE NOT NULL COMMENT '下单时间(标准日期格式)',
  `type` char(1) COLLATE utf8_bin NOT NULL COMMENT 'k-客房 t-套餐',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;


--
--  购买房间时间记录表 (此表就是为了统计每天剩余的房间数量)
--
CREATE TABLE IF NOT EXISTS `ms_room_date` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `createDate` DATE NOT NULL COMMENT '入住时间',
  `roomID` int(11) NOT NULL COMMENT '房间id',
  `order` int(11) NOT NULL COMMENT '订单数量(只要有已经支付的订单就加1,退款则减1)',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

--
--  电子卷管理表--现在改需求了,只能积分来兑换
--
CREATE TABLE IF NOT EXISTS `ms_coupon` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) COLLATE utf8_bin NOT NULL COMMENT '电子卷名称',
  `money` float(10,2) COLLATE utf8_bin NOT NULL COMMENT '电子卷金额',
  `exprie_start` date NOT NULL COMMENT '开始时间',
  `exprie_end` date NOT NULL COMMENT '截止时间',
  `num` int(11) NOT NULL COMMENT '电子卷数量(库存)',
  `notDate` text COLLATE utf8_bin NOT NULL COMMENT '电子卷不可使用日期格式:月份-日期,月份-日期,...',
  `year` varchar(10) COLLATE utf8_bin NOT NULL COMMENT '电子卷不可使用年份,默认统一获取当前年份',
  `sorce` varchar(255) COLLATE utf8_bin NOT NULL COMMENT '兑换所需积分',
  `mark` text COLLATE utf8_bin NOT NULL COMMENT '描述(使用说明)',
  `hcate` varchar(50) COLLATE utf8_bin NOT NULL COMMENT '可以使用电子卷的客房分类id',
  `tcate` varchar(50) COLLATE utf8_bin NOT NULL COMMENT '可以使用电子卷的套餐分类id',
  `pic` int(11) NOT NULL COMMENT '电子卷图片',
  `status` tinyint(1) NOT NULL COMMENT '1-正常 2-禁用 3-删除',
  `add_time` int(11) NOT NULL COMMENT '添加时间',
  `update_time` int(11) NOT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

--
--  电子卷兑换记录表
--
CREATE TABLE IF NOT EXISTS `ms_coupon_exchange` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cID` int(11) NOT NULL COMMENT '关联ms_coupon的id,电子卷id',
  `createTime` int(11) NOT NULL COMMENT '兑换时间',
  `updateTime` int(11) NOT NULL COMMENT '更新时间(包括使用和转增)',
  `userID` int(11) NOT NULL COMMENT '兑换用户id',
  `status` tinyint(1) NOT NULL COMMENT '使用状态  1-未使用 2-已使用 3-已转赠',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

--
--  电子卷转赠记录表
--
CREATE TABLE IF NOT EXISTS `ms_coupon_give` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cID` int(11) NOT NULL COMMENT '关联ms_coupon的id,电子卷id',
  `sendID` int(11) NOT NULL COMMENT '转移者id',
  `acceptID` int(11) NOT NULL COMMENT '接受者id',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

--
--  电子卷使用记录表
--
CREATE TABLE IF NOT EXISTS `ms_coupon_used` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userID` int(11) NOT NULL COMMENT '用户id',
  `orderNO` varchar(16) COLLATE utf8_bin NOT NULL COMMENT '订单编号',
  `roomID` int(11) NOT NULL COMMENT '房间id,关联客房id和套餐里的房间id',
  `createTime` int(11) NOT NULL COMMENT '创建时间',
  `cID` int(11) NOT NULL COMMENT '关联ms_coupon的id,电子卷id',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;







