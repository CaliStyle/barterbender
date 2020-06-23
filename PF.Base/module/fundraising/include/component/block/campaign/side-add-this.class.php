<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Fundraising_Component_Block_Campaign_Side_Add_This extends Phpfox_Component
{
    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
        //create token for this current user
        $iId = $this->getParam('id');
        $aCampaign = Phpfox::getService('fundraising.campaign')->getCampaignById($iId);
		if(!$aCampaign)
		{
			return false;
		}
        $sToken = md5(Phpfox::getUserId());

        $this->template()->assign(array(
                'sHeader' => '',
                'aCampaign' => $aCampaign,
                'sToken' => $sToken,
                'sAddThisShareButton' => '',
                'sAddThisPubId' => setting('core.addthis_pub_id', 'younet'),
            )
        );

        // add support responsiveclean template
        if ( $this->template()->getThemeFolder() == 'ynresponsiveclean' ) {
            $this->template()->assign(array(
                    'sHeader' => 'shares',                    
                )
            );            
        }
        return 'block';
    }

}

?>