<?php
$installer = $this;
$installer->startSetup();

//Creates the vendor table to store all the vendors and adds three test vendors.
$installer->run("
	DROP TABLE IF EXISTS `{$installer->getTable('dropship/vendor')}`;
    CREATE TABLE `{$installer->getTable('dropship/vendor')}` (
      `dropship_vendor_id` int(11) NOT NULL auto_increment,
      `name` varchar(100) NOT NULL,
      `description` text,
      `shipping_carrier` varchar(10) NOT NULL,
      `shipping_method` varchar(100) NOT NULL,
      `address` varchar(100),
      `city` varchar(100),
      `state_code` varchar(10) NOT NULL,
      `postal_code` varchar(10) NOT NULL,
      `country_code` varchar(10) NOT NULL default 'US',
      `vendor_email_list`  varchar(255) NOT NULL,
	  `error_email_list`   varchar(255) NOT NULL,
      PRIMARY KEY  (`dropship_vendor_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$setup = new Mage_Eav_Model_Entity_Setup('vendor_setup');

$installer->endSetup();
?>