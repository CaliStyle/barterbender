<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Petition_Component_Block_Helplogo extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
            $aHelp = $this->getParam('aHelp');
            if (!$aHelp)
            {
                    return false;
            }
  
            $this->template()->assign(array(
                    'aHelp' => $aHelp,
                    'corepath' => Phpfox::getParam('core.path')
                )
            );
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('petition.component_block_helplogo_clean')) ? eval($sPlugin) : false);
	}
}

?>