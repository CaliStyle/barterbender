<?php
function ynecommerce_install401p3()
{
    $oDb = Phpfox::getLib('phpfox.database');

    if (!$oDb->isField(Phpfox::getT('ecommerce_category_data'), 'product_type')) {
        $oDb->query("ALTER TABLE `" . Phpfox::getT('ecommerce_category_data') . "` ADD `product_type` varchar(128) NOT NULL DEFAULT 'auction'");
    }

    if (!$oDb->isField(Phpfox::getT('ecommerce_custom_value'), 'product_type')) {
        $oDb->query("ALTER TABLE `" . Phpfox::getT('ecommerce_custom_value') . "` ADD `product_type` varchar(128) NOT NULL DEFAULT 'auction'");
    }

    if (!$oDb->isField(Phpfox::getT('ecommerce_product_image'), 'product_type')) {
        $oDb->query("ALTER TABLE `" . Phpfox::getT('ecommerce_product_image') . "` ADD `product_type` varchar(128) NOT NULL DEFAULT 'auction'");
    }

    if (!$oDb->isField(Phpfox::getT('ecommerce_order_product'), 'orderproduct_parent_id')) {
        $oDb->query("ALTER TABLE `" . Phpfox::getT('ecommerce_order_product') . "` ADD `orderproduct_parent_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'parent_id of this product. Like: store,...'");
    }

    if (!$oDb->isField(Phpfox::getT('ecommerce_order'), 'module_id')) {
        $oDb->query("ALTER TABLE `" . Phpfox::getT('ecommerce_order') . "` ADD `module_id` varchar(128) NOT NULL DEFAULT 'auction'");
    }

    if (!$oDb->isField(Phpfox::getT('ecommerce_order'), 'order_code')) {
        $oDb->query("ALTER TABLE `" . Phpfox::getT('ecommerce_order') . "` ADD `order_code` varchar(8) NOT NULL DEFAULT '1234ABCD'");
    }

    if (!$oDb->isField(Phpfox::getT('ecommerce_order'), 'order_buyfrom_id')) {
        $oDb->query("ALTER TABLE `" . Phpfox::getT('ecommerce_order') . "` ADD `order_buyfrom_id` int(10) unsigned NOT NULL DEFAULT '0'");
    }

    if (!$oDb->isField(Phpfox::getT('ecommerce_order'), 'order_buyfrom_type')) {
        $oDb->query("ALTER TABLE `" . Phpfox::getT('ecommerce_order') . "` ADD `order_buyfrom_type` varchar(128) NOT NULL DEFAULT 'user'");
    }

    if (!$oDb->isField(Phpfox::getT('ecommerce_invoice'), 'item_type')) {
        $oDb->query("ALTER TABLE `" . Phpfox::getT('ecommerce_invoice') . "` ADD `item_type` varchar(64) NOT NULL DEFAULT 'auction'");
    }

    if ($oDb->isField(Phpfox::getT('ecommerce_invoice'), 'type')) {
        $oDb->query("ALTER TABLE `" . Phpfox::getT('ecommerce_invoice') . "` CHANGE `type`  `type` ENUM(  'product',  'store',  'feature',  'product_feature') DEFAULT 'product'");
    }

    if (!$oDb->isField(Phpfox::getT('ecommerce_cart_product'), 'cartproduct_attribute_id')) {
        $oDb->query("ALTER TABLE  `" . Phpfox::getT('ecommerce_cart_product') . "` ADD  `cartproduct_attribute_id` int(10) NOT NULL DEFAULT  '0'");
    }

    if (!$oDb->isField(Phpfox::getT('ecommerce_order_product'), 'orderproduct_attribute_id')) {
        $oDb->query("ALTER TABLE  `" . Phpfox::getT('ecommerce_order_product') . "` ADD  `orderproduct_attribute_id` int(10) NOT NULL DEFAULT  '0'");
    }

    if ($oDb->isField(Phpfox::getT('ecommerce_cart_product'), 'cartproduct_module')) {
        $oDb->query("ALTER TABLE  `" . Phpfox::getT('ecommerce_cart_product') . "` CHANGE COLUMN `cartproduct_module` `cartproduct_module` varchar(255)");
    }

    if(!$oDb->isField(Phpfox::getT('ecommerce_order_product'),'orderproduct_module'))
    {
        $oDb->query("ALTER TABLE  `".Phpfox::getT('ecommerce_order_product')."` ADD  `orderproduct_module` varchar(255) NOT NULL DEFAULT  'auction'");
    }
}

ynecommerce_install401p3();

?>