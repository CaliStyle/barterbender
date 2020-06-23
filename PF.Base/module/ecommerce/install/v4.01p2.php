<?php
function ecommerce_install401p2()
{
	$collation = Phpfox::getLib('database')->getRow("select COLLATION_NAME from information_schema.columns where TABLE_NAME = '".Phpfox::getT('ecommerce_product')."' and COLUMN_NAME = 'logo_path' LIMIT 1;");
	$character = Phpfox::getLib('database')->getRow("select CHARACTER_SET_NAME from information_schema.columns where TABLE_NAME = '".Phpfox::getT('ecommerce_product')."' and COLUMN_NAME = 'logo_path' LIMIT 1;");
    $oDb = Phpfox::getLib('phpfox.database');
	$oDb->query("ALTER TABLE `". Phpfox::getT('ecommerce_product') ."`
		 DEFAULT CHARACTER SET ".$character['CHARACTER_SET_NAME'].";");
	$oDb->query("ALTER TABLE `". Phpfox::getT('ecommerce_product') ."`
		 CHANGE `name` `name` VARCHAR(255) CHARACTER SET ".$character['CHARACTER_SET_NAME']." COLLATE ".$collation['COLLATION_NAME']."  NOT NULL;");
}

ecommerce_install401p2();

?>