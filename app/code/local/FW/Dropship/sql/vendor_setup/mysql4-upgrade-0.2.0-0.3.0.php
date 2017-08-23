<?php
$installer = $this;
$installer->startSetup();
try {
	$installer->run("ALTER TABLE `{$installer->getTable('dropship/vendor')}` ADD `vendor_po_code` VARCHAR(100) AFTER name;");
} catch (Exception $ex) {
	Mage::logException($ex);
}
$installer->endSetup();