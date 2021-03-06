<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for setting up storefront tables
 **/
class Migration20150327144022ComStorefront extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__storefront_product_meta'))
		{
			/* `#__storefront_collections` */
			$query = "DROP TABLE IF EXISTS `#__storefront_collections`";
			$this->db->setQuery($query);
			$this->db->query();

			/*Table structure for table `#__storefront_collections` */
			$query = "CREATE TABLE `#__storefront_collections` (
			  `cId` char(50) NOT NULL,
			  `cName` varchar(64) DEFAULT NULL,
			  `cParent` int(16) DEFAULT NULL,
			  `cActive` tinyint(1) DEFAULT NULL,
			  `cType` char(10) DEFAULT NULL,
			  PRIMARY KEY (`cId`),
			  KEY `cActive` (`cActive`),
			  KEY `cParent` (`cParent`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8";
			$this->db->setQuery($query);
			$this->db->query();

			/* `#__storefront_coupon_actions` */
			$query = "DROP TABLE IF EXISTS `#__storefront_coupon_actions`";
			$this->db->setQuery($query);
			$this->db->query();

			/*Table structure for table `#__storefront_coupon_actions` */
			$query = "CREATE TABLE `#__storefront_coupon_actions` (
			  `cnId` int(16) NOT NULL,
			  `cnaAction` char(25) DEFAULT NULL,
			  `cnaVal` char(255) DEFAULT NULL,
			  UNIQUE KEY `cnId` (`cnId`,`cnaAction`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8";
			$this->db->setQuery($query);
			$this->db->query();

			/* `#__storefront_coupon_conditions` */
			$query = "DROP TABLE IF EXISTS `#__storefront_coupon_conditions`";
			$this->db->setQuery($query);
			$this->db->query();

			/*Table structure for table `#__storefront_coupon_conditions` */
			$query = "CREATE TABLE `#__storefront_coupon_conditions` (
			  `cnId` int(16) NOT NULL,
			  `cncRule` char(100) DEFAULT NULL,
			  `cncVal` char(255) DEFAULT NULL
			) ENGINE=MyISAM DEFAULT CHARSET=utf8";
			$this->db->setQuery($query);
			$this->db->query();

			/* `#__storefront_coupon_objects` */
			$query = "DROP TABLE IF EXISTS `#__storefront_coupon_objects`";
			$this->db->setQuery($query);
			$this->db->query();

			/*Table structure for table `#__storefront_coupon_objects` */
			$query = "CREATE TABLE `#__storefront_coupon_objects` (
			  `cnId` int(16) NOT NULL,
			  `cnoObjectId` int(16) DEFAULT NULL,
			  `cnoObjectsLimit` int(5) DEFAULT '0' COMMENT 'How many objects can be applied to. 0 - unlimited',
			  UNIQUE KEY `cnId` (`cnId`,`cnoObjectId`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8";
			$this->db->setQuery($query);
			$this->db->query();

			/* `#__storefront_coupons` */
			$query = "DROP TABLE IF EXISTS `#__storefront_coupons`";
			$this->db->setQuery($query);
			$this->db->query();

			/*Table structure for table `#__storefront_coupons` */
			$query = "CREATE TABLE `#__storefront_coupons` (
			  `cnId` int(16) NOT NULL AUTO_INCREMENT,
			  `cnCode` char(25) DEFAULT NULL,
			  `cnDescription` char(255) DEFAULT NULL,
			  `cnExpires` date DEFAULT NULL,
			  `cnUseLimit` int(5) unsigned DEFAULT NULL,
			  `cnObject` char(15) NOT NULL,
			  `cnActive` tinyint(1) DEFAULT '1',
			  PRIMARY KEY (`cnId`),
			  UNIQUE KEY `Unique code` (`cnCode`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8";
			$this->db->setQuery($query);
			$this->db->query();

			/* `#__storefront_option_groups` */
			$query = "DROP TABLE IF EXISTS `#__storefront_option_groups`";
			$this->db->setQuery($query);
			$this->db->query();

			/*Table structure for table `#__storefront_option_groups` */
			$query = "CREATE TABLE `#__storefront_option_groups` (
			  `ogId` int(16) NOT NULL AUTO_INCREMENT,
			  `ogName` char(16) DEFAULT NULL,
			  PRIMARY KEY (`ogId`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8";
			$this->db->setQuery($query);
			$this->db->query();

			/* `#__storefront_options` */
			$query = "DROP TABLE IF EXISTS `#__storefront_options`";
			$this->db->setQuery($query);
			$this->db->query();

			/*Table structure for table `#__storefront_options` */
			$query = "CREATE TABLE `#__storefront_options` (
			  `oId` int(16) NOT NULL AUTO_INCREMENT,
			  `ogId` int(16) DEFAULT NULL COMMENT 'Foreign key to option-groups',
			  `oName` char(255) DEFAULT NULL,
			  PRIMARY KEY (`oId`),
			  UNIQUE KEY `ogId` (`ogId`,`oName`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8";
			$this->db->setQuery($query);
			$this->db->query();

			/* `#__storefront_product_collections` */
			$query = "DROP TABLE IF EXISTS `#__storefront_product_collections`";
			$this->db->setQuery($query);
			$this->db->query();

			/*Table structure for table `#__storefront_product_collections` */
			$query = "CREATE TABLE `#__storefront_product_collections` (
			  `cllId` int(16) NOT NULL AUTO_INCREMENT,
			  `pId` int(16) NOT NULL,
			  `cId` char(50) NOT NULL,
			  PRIMARY KEY (`cllId`,`pId`,`cId`),
			  UNIQUE KEY `pId` (`pId`,`cId`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8";
			$this->db->setQuery($query);
			$this->db->query();

			/*Table structure for table `#__storefront_product_meta` */
			$query = "CREATE TABLE `#__storefront_product_meta` (
			  `pmId` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `pId` int(11) NOT NULL,
			  `pmKey` varchar(100) NOT NULL DEFAULT '',
			  `pmValue` varchar(100) NOT NULL DEFAULT '',
			  PRIMARY KEY (`pmId`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8";
			$this->db->setQuery($query);
			$this->db->query();

			/* `#__storefront_product_option_groups` */
			$query = "DROP TABLE IF EXISTS `#__storefront_product_option_groups`";
			$this->db->setQuery($query);
			$this->db->query();

			/*Table structure for table `#__storefront_product_option_groups` */
			$query = "CREATE TABLE `#__storefront_product_option_groups` (
			  `pId` int(16) NOT NULL,
			  `ogId` int(16) NOT NULL,
			  PRIMARY KEY (`pId`,`ogId`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8";
			$this->db->setQuery($query);
			$this->db->query();

			/* `#__storefront_product_types` */
			$query = "DROP TABLE IF EXISTS `#__storefront_product_types`";
			$this->db->setQuery($query);
			$this->db->query();

			/*Table structure for table `#__storefront_product_types` */
			$query = "CREATE TABLE `#__storefront_product_types` (
			  `ptId` int(16) NOT NULL AUTO_INCREMENT,
			  `ptName` char(128) DEFAULT NULL,
			  `ptModel` char(25) DEFAULT 'normal',
			  PRIMARY KEY (`ptId`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8";
			$this->db->setQuery($query);
			$this->db->query();

			// Data for table `#__storefront_product_types` */
			$query = "INSERT INTO `#__storefront_product_types` (`ptId`, `ptName`, `ptModel`)
			VALUES
				(1,'Shippable merchandise','normal'),
				(20,'Course','membership'),
				(21,'Membership','membership'),
				(22,'Access Group Membership','membership')";
			$this->db->setQuery($query);
			$this->db->query();

			/* `#__storefront_products` */
			$query = "DROP TABLE IF EXISTS `#__storefront_products`";
			$this->db->setQuery($query);
			$this->db->query();

			/*Table structure for table `#__storefront_products` */
			$query = "CREATE TABLE `#__storefront_products` (
			  `pId` int(16) NOT NULL AUTO_INCREMENT,
			  `pAlias` char(255) DEFAULT NULL,
			  `ptId` int(16) NOT NULL COMMENT 'Product type ID. Foreign key to product_types table',
			  `pName` char(128) DEFAULT NULL,
			  `pTagline` tinytext,
			  `pDescription` text,
			  `pFeatures` text,
			  `pActive` tinyint(1) DEFAULT '1',
			  `pAllowMultiple` tinyint(1) DEFAULT '1',
			  PRIMARY KEY (`pId`),
			  KEY `pActive` (`pActive`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8";
			$this->db->setQuery($query);
			$this->db->query();

			/* `#__storefront_sku_meta` */
			$query = "DROP TABLE IF EXISTS `#__storefront_sku_meta`";
			$this->db->setQuery($query);
			$this->db->query();

			/*Table structure for table `#__storefront_sku_meta` */
			$query = "CREATE TABLE `#__storefront_sku_meta` (
			  `smId` int(16) NOT NULL AUTO_INCREMENT,
			  `sId` int(16) NOT NULL,
			  `smKey` varchar(100) DEFAULT NULL,
			  `smValue` text,
			  PRIMARY KEY (`smId`),
			  UNIQUE KEY `sId` (`sId`,`smKey`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8";
			$this->db->setQuery($query);
			$this->db->query();

			/* `#__storefront_sku_options` */
			$query = "DROP TABLE IF EXISTS `#__storefront_sku_options`";
			$this->db->setQuery($query);
			$this->db->query();

			/*Table structure for table `#__storefront_sku_options` */
			$query = "CREATE TABLE `#__storefront_sku_options` (
			  `sId` int(16) NOT NULL,
			  `oId` int(16) NOT NULL,
			  PRIMARY KEY (`sId`,`oId`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8";
			$this->db->setQuery($query);
			$this->db->query();

			/* `#__storefront_skus` */
			$query = "DROP TABLE IF EXISTS `#__storefront_skus`";
			$this->db->setQuery($query);
			$this->db->query();

			/*Table structure for table `#__storefront_skus` */
			$query = "CREATE TABLE `#__storefront_skus` (
			  `sId` int(16) NOT NULL AUTO_INCREMENT,
			  `pId` int(16) DEFAULT NULL COMMENT 'Foreign key to products',
			  `sSku` char(16) DEFAULT NULL,
			  `sWeight` decimal(10,2) DEFAULT NULL,
			  `sPrice` decimal(10,2) DEFAULT NULL,
			  `sDescriprtion` text,
			  `sFeatures` text,
			  `sTrackInventory` tinyint(1) DEFAULT '0',
			  `sInventory` int(11) DEFAULT '0',
			  `sEnumerable` tinyint(1) DEFAULT '1',
			  `sAllowMultiple` tinyint(1) DEFAULT '1',
			  `sActive` tinyint(1) DEFAULT '1',
			  PRIMARY KEY (`sId`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__storefront_product_meta'))
		{
			$query = "DROP TABLE IF EXISTS `#__storefront_collections`";
			$this->db->setQuery($query);
			$this->db->query();

			$query = "DROP TABLE IF EXISTS `#__storefront_coupon_actions`";
			$this->db->setQuery($query);
			$this->db->query();

			$query = "DROP TABLE IF EXISTS `#__storefront_coupon_conditions`";
			$this->db->setQuery($query);
			$this->db->query();

			$query = "DROP TABLE IF EXISTS `#__storefront_collections`";
			$this->db->setQuery($query);
			$this->db->query();

			$query = "DROP TABLE IF EXISTS `#__storefront_coupon_objects`";
			$this->db->setQuery($query);
			$this->db->query();

			$query = "DROP TABLE IF EXISTS `#__storefront_coupons`";
			$this->db->setQuery($query);
			$this->db->query();

			$query = "DROP TABLE IF EXISTS `#__storefront_option_groups`";
			$this->db->setQuery($query);
			$this->db->query();

			$query = "DROP TABLE IF EXISTS `#__storefront_options`";
			$this->db->setQuery($query);
			$this->db->query();

			$query = "DROP TABLE IF EXISTS `#__storefront_product_collections`";
			$this->db->setQuery($query);
			$this->db->query();

			$query = "DROP TABLE IF EXISTS `#__storefront_product_meta`";
			$this->db->setQuery($query);
			$this->db->query();

			$query = "DROP TABLE IF EXISTS `#__storefront_product_option_groups`";
			$this->db->setQuery($query);
			$this->db->query();

			$query = "DROP TABLE IF EXISTS `#__storefront_product_types`";
			$this->db->setQuery($query);
			$this->db->query();

			$query = "DROP TABLE IF EXISTS `#__storefront_products`";
			$this->db->setQuery($query);
			$this->db->query();

			$query = "DROP TABLE IF EXISTS `#__storefront_sku_meta`";
			$this->db->setQuery($query);
			$this->db->query();

			$query = "DROP TABLE IF EXISTS `#__storefront_sku_options`";
			$this->db->setQuery($query);
			$this->db->query();

			$query = "DROP TABLE IF EXISTS `#__storefront_skus`";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
