<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Petition_Component_Block_Direct extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
            if (defined('PHPFOX_IS_USER_PROFILE') || defined('PHPFOX_IS_PAGES_VIEW'))
            {
               return false;
            }
		$aDirect = Phpfox::getService('petition')->getDirectSign();
		
		if (!$aDirect)
		{
			return false;
		}
		$aDirect['can_sign'] = Phpfox::getService('petition')->canSign($aDirect);					   
		$this->template()->assign(array(
		'corepath' => Phpfox::getParam('core.path'),
				'sHeader' => Phpfox::getPhrase('petition.direct_sign'),
				'aDirect' => $aDirect,
                        'corepath'=>phpfox::getParam('core.path')
			)
		);
		
		return 'block';		
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('petition.component_block_direct_clean')) ? eval($sPlugin) : false);
	}
}

?>