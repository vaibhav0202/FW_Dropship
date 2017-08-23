<?php
$installer = $this;
$installer->startSetup();

//Creates the vendor table to store all the vendors and adds three test vendors.
$installer->run("ALTER TABLE `{$installer->getTable('dropship/vendor')}` CHANGE `shipping_method` VARCHAR(100) NOT NULL");

$installer->endSetup();
?>