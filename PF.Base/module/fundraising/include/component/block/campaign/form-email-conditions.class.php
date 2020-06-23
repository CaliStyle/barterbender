<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Fundraising_Component_Block_Campaign_Form_Email_Conditions extends Phpfox_Component
{
    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
    	$this->template() ->setEditor(array('wysiwyg' => true));
		$iCampaignId = $this -> getParam('iCampaignId', 0);
		if (Phpfox::isModule('attachment')) {
           $this->setParam('attachment_share', array(
					'type' => 'fundraising',
					'id' => 'ynfr_edit_campaign_form',
					'edit_id' => $iCampaignId,
				)
			);
        }
    }
}

?>