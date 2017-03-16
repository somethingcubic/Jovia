DROP TABLE IF EXISTS `shop_admin`;
CREATE TABLE IF NOT EXISTS `shop_admin`(
	`adminid` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID',
	`adminuser` VARCHAR(32) NOT NULL DEFAULT '' COMMENT '管理员账号',
	`adminpass` CHAR(32) NOT NULL DEFAULT '' COMMENT '管理员密码',
	`adminemail` VARCHAR(50) NOT NULL DEFAULT '' COMMENT '管理员邮箱',
	`logintime` INT UNSIGNED NOT NULL DEFAULT '0' COMMENT '登录时间',
	`loginip` BIGINT NOT NULL DEFAULT '0' COMMENT '登录IP',
	`createtime` INT UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建时间',
	PRIMARY KEY (`adminid`),
	UNIQUE KEY shop_admin_adminuser_adminpass (`adminuser` , `adminpass`),
	UNIQUE KEY shop_admin_adminuser_adminemail (`adminuser` , `adminemail`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `shop_admin`(adminuser,adminpass,adminemail,createtime) VALUES ('admin',md5('admin'),'test@imooc.com',UNIX_TIMESTAMP());

DROP TABLE IF EXISTS `shop_user`;
CREATE TABLE IF NOT EXISTS `shop_user`(
	`userid` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID',
	`username` VARCHAR(32) NOT NULL DEFAULT '',
	`userpass` CHAR(32) NOT NULL DEFAULT '',
	`useremail` VARCHAR(100) NOT NULL DEFAULT '',
	`createtime` INT UNSIGNED NOT NULL DEFAULT '0',
	UNIQUE shop_user_username_userpass(`username`,`userpass`),
	UNIQUE shop_user_useremail_userpass(`useremail`,`userpass`),
	PRIMARY KEY (`userid`)
)ENGINE = InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `shop_profile`;
CREATE TABLE IF NOT EXISTS `shop_profile`(
	`id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID',
	`truename` VARCHAR(32) NOT NULL DEFAULT '' COMMENT '真实姓名',
	`age` TINYINT UNSIGNED NOT NULL DEFAULT '0' COMMENT '年龄',
	`sex` ENUM('0', '1', '2') NOT NULL DEFAULT '0' COMMENT '性别',
	`birthday` DATE NOT NULL DEFAULT '2016-01-01' COMMENT '生日',
	`nickname` VARCHAR(32) NOT NULL DEFAULT '' COMMENT '昵称',
	`company` VARCHAR(100) NOT NULL DEFAULT '' COMMENT '公司',
	`userid` BIGINT UNSIGNED NOT NULL DEFAULT '0' COMMENT '用户的ID',
	`createtime` INT UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建时间',
	PRIMARY KEY (`id`),
	UNIQUE shop_profile_userid(`userid`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `shop_category`;
CREATE TABLE IF NOT EXISTS `shop_category`(
  `cateid` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(32) NOT NULL DEFAULT '',
  `pid` BIGINT UNSIGNED NOT NULL DEFAULT '0',
  `path` VARCHAR(255) NOT NULL DEFAULT '',
  `level` BIGINT UNSIGNED NOT NULL DEFAULT '0',
  `createtime` INT UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY(`cateid`),
  KEY shop_category_parentid(`pid`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `shop_product`;
CREATE TABLE IF NOT EXISTS `shop_product`(
	`productid` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
	`cateid` BIGINT UNSIGNED NOT NULL DEFAULT '0',
	`title` VARCHAR(200) NOT NULL DEFAULT '',
	`desc` TEXT,
	`cover` VARCHAR(200) NOT NULL DEFAULT '',
	`issale` ENUM('0', '1') NOT NULL DEFAULT '0',
	`ishot` ENUM('0', '1') NOT NULL DEFAULT '0',
	`istui` ENUM('0', '1') NOT NULL DEFAULT '0',
	`ison` ENUM('0', '1') NOT NULL DEFAULT '1',
	`createtime` INT UNSIGNED NOT NULL DEFAULT '0',
	PRIMARY KEY(`productid`),
	KEY shop_product_cateid(`cateid`),
	KEY shop_product_ison(`ison`),
	KEY shop_product_issale(`issale`),
	KEY shop_product_ishot(`ishot`),
	KEY shop_product_istui(`istui`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `shop_variant`;
CREATE TABLE IF NOT EXISTS `shop_variant`(
  `variantid` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '属性ID',
  `cateid` BIGINT UNSIGNED NOT NULL DEFAULT '0' COMMENT '分类ID',
  `title` VARCHAR(50) NOT NULL DEFAULT '' COMMENT '属性名称',
  `varianttype` ENUM('1','2') NOT NULL DEFAULT '1' COMMENT '属性类型：1、唯一 2、可选',
  PRIMARY KEY(`variantid`),
  KEY shop_varient_varientid_cateid (`variantid`,`cateid`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `shop_product_variant`;
CREATE TABLE IF NOT EXISTS `shop_product_variant`(
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `productid` BIGINT UNSIGNED NOT NULL DEFAULT '0' COMMENT '产品ID',
  `variantid` BIGINT UNSIGNED NOT NULL DEFAULT '0' COMMENT '属性ID',
  `variantnum` bigint(20) unsigned NOT NULL,
  `variantvalue` VARCHAR(200) NOT NULL DEFAULT '' COMMENT '属性值',
  PRIMARY KEY(`id`),
  KEY shop_product_variant_productid_variantid(`productid`,`variantid`),
  KEY shop_product_variant_variantid_variantvalue(`variantid`,`variantvalue`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `shop_product_spec`;
CREATE TABLE IF NOT EXISTS `shop_product_spec`(
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `productid` BIGINT UNSIGNED NOT NULL DEFAULT '0' COMMENT '产品ID',
  `sku` VARCHAR(200) NOT NULL COMMENT '唯一识别码',
  `tripid` BIGINT UNSIGNED NOT NULL COMMENT '对应的tripid',
  `price` DECIMAL(10,2) NOT NULL COMMENT '出售价格',
	`pic` varchar(200) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY shop_product_spec_productid(`productid`),
  KEY shop_product_spec_sku_tripid(`sku`,`tripid`),
  KEY shop_product_spec_sku_price(`sku`,`price`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `shop_cart`;
CREATE TABLE IF NOT EXISTS `shop_cart`(
	`cartid` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`productid` BIGINT UNSIGNED NOT NULL DEFAULT '0',
	`sku` VARCHAR(200) NOT NULL DEFAULT '0',
	`productnum` INT UNSIGNED NOT NULL DEFAULT '0',
	`price` DECIMAL(10,2) NOT NULL DEFAULT '0.00',
	`userid` BIGINT UNSIGNED NOT NULL DEFAULT '0',
	`createtime` INT UNSIGNED NOT NULL DEFAULT '0',
	KEY shop_cart_productid(`productid`),
	KEY shop_cart_userid(`userid`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `shop_order`;
CREATE TABLE IF NOT EXISTS `shop_order`(
	`orderid` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`userid` BIGINT UNSIGNED NOT NULL DEFAULT '0',
	`addressid` BIGINT UNSIGNED NOT NULL DEFAULT '0',
	`amount` DECIMAL(10,2) NOT NULL DEFAULT '0.00',
	`status` INT UNSIGNED NOT NULL DEFAULT '0',
	`txn_id` VARCHAR(100) NOT NULL DEFAULT '',
	`payext` TEXT,
	`createtime` INT UNSIGNED NOT NULL DEFAULT '0',
	`updatetime` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	KEY shop_order_userid(`userid`)
)ENGINE=InnoDB DEFAULT CHARSET='utf8';

DROP TABLE IF EXISTS `shop_order_detail`;
CREATE TABLE IF NOT EXISTS `shop_order_detail`(
	`detailid` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`productid` BIGINT UNSIGNED NOT NULL DEFAULT '0',
	`productsku` VARCHAR(200) NOT NULL COMMENT '唯一识别码',
	`price` DECIMAL(10,2) NOT NULL DEFAULT '0.00',
	`productnum` INT UNSIGNED NOT NULL DEFAULT '0',
	`orderid` BIGINT UNSIGNED NOT NULL DEFAULT '0',
	`createtime` INT UNSIGNED NOT NULL DEFAULT '0',
	KEY shop_order_detail_productid_productsku(`productid`,`productsku`),
	KEY shop_order_detail_orderid(`orderid`)
)ENGINE=InnoDB DEFAULT CHARSET='utf8';


DROP TABLE IF EXISTS `shop_address`;
CREATE TABLE IF NOT EXISTS `shop_address`(
	`addressid` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`firstname` VARCHAR(32) NOT NULL DEFAULT '',
	`lastname` VARCHAR(32) NOT NULL DEFAULT '',
	`company` VARCHAR(100) NOT NULL DEFAULT '',
	`address` TEXT,
	`postcode` CHAR(6) NOT NULL DEFAULT '',
	`email` VARCHAR(100) NOT NULL DEFAULT '',
	`telephone` VARCHAR(20) NOT NULL DEFAULT '',
	`userid` BIGINT UNSIGNED NOT NULL DEFAULT '0',
	`createtime` INT UNSIGNED NOT NULL DEFAULT '0',
	KEY shop_address_userid(`userid`)
)ENGINE=InnoDB DEFAULT CHARSET='utf8';









