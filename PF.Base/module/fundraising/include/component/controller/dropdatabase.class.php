<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Fundraising_Component_Controller_Dropdatabase extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		/*
	 * a little trick here is
	 * SELECT concat('TRUNCATE TABLE ', TABLE_NAME, ';')
		FROM INFORMATION_SCHEMA.TABLES
		WHERE TABLE_NAME LIKE 'inventory%'
	 * to generate truncate query to delete all tables
	 */

//		$aRows = Phpfox::getLib('database')->select("concat('DROP TABLE ', TABLE_NAME, ';') as drop_query")
//					->from('INFORMATION_SCHEMA.TABLES')
//					->where('TABLE_NAME like \'' . Phpfox::getT('fundraising') . '%\' AND table_schema = \'' . Phpfox::getParam(array('db', 'name')) . '\'')
//					->execute('getSlaveRows');
//		foreach($aRows as $aRow)
//		{
//			Phpfox::getLib('database')->query($aRow['drop_query']);
//		}
//		
//		exit;
	}
	
}

?>
