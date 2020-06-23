<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * @copyright		YouNetCo Company
 * @author  		MinhNTK
 */
class FoxFavorite_Component_Block_Module_Category extends Phpfox_Component
{
	public function process()
	{
		
		$aSettings = phpfox::getService('foxfavorite')->getSettings();
		$sUserName = $this->request()->get('req1');
		
		foreach($aSettings as $iKey => $aSetting)
		{
			$aSettings[$iKey]['url'] = phpfox::getLib('url')->makeUrl($sUserName.'.foxfavorite.module_'.$aSetting['title']);
		}
		$this->template()->assign(array(
						'aModules'=>$aSettings,
						'sHeader'=>'Module'
			));
		return 'block';
	}
}
?>