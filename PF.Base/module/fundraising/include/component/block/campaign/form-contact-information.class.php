<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Fundraising_Component_Block_Campaign_Form_Contact_Information extends Phpfox_Component
{
    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
    	$this->template() ->setEditor(array('wysiwyg' => true));
		$iCampaignId = $this -> getParam('iCampaignId', 0);
		$aCampaign = Phpfox::getService('fundraising.campaign') -> getCampaignForEdit($iCampaignId);
		$this->template() ->setHeader('cache', array(
						'<script type="text/javascript">$Behavior.setCountryContact = function() {$("#js_fundraising_block_contact_information #js_country_iso_option_'.$aCampaign['contact_country_iso'].'").attr("selected","selected"); }</script>'
						));
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