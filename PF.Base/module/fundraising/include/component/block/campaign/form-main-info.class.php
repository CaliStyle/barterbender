<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Fundraising_Component_Block_Campaign_Form_Main_Info extends Phpfox_Component
{
    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
		$this->template()->assign(array(
				'sCategories' => Phpfox::getService('fundraising.category')->get(),
                'corepath' => Phpfox::getParam('core.path'),
			)
		);	
    }
}

?>