<?php

defined('PHPFOX') or exit('NO DICE!');

/**
 * create DATABASE For version 3.01
 * @by trilm
 *
 */

function ynd_install301p3()
{

		$aCategoryInfo = Phpfox::getLib('database')->select('dc.category_id,COUNT(dcd.business_id) as total_business' )
	            ->from(Phpfox::getT('directory_category'), 'dc')
	            ->leftjoin(Phpfox::getT("directory_category_data"), 'dcd', 'dc.category_id = dcd.category_id')
	            ->leftjoin(Phpfox::getT("directory_business"), 'dbus', 'dbus.business_id = dcd.business_id')
	            ->where('dbus.business_status IN (5,9)')
				->group('dc.category_id')
				->execute('getSlaveRows');

		Phpfox::getLib('database')->update(Phpfox::getT('directory_category'), array('used' => 0),'1=1');  

		if(count($aCategoryInfo)){
			foreach ($aCategoryInfo as $key => $category) {
				Phpfox::getLib('database')->update(Phpfox::getT('directory_category'), array('used' => (int)$category['total_business']),' category_id = ' . (int)$category['category_id']);  
			}		
		}

        Phpfox::getLib('cache')->remove('directory_category', 'substr');

}

ynd_install301p3();

?>