<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

/**
 * @copyright		[YOUNETCO]
 * @author  		NghiDV
 * @package  		Module_Donation
 * @version 		$Id: sample.class.php 1 2012-02-15 10:33:17Z YOUNETCO $
 */
class Donation_Component_Block_Donate extends Phpfox_Component {

    /**
     * Class process method which is used to execute this component.
     */
    public function process()
    {
        Phpfox::isUser();
        $oDonation = Phpfox::getService('donation');
		$bIsPages = ((defined('PHPFOX_IS_PAGES_VIEW') && Phpfox::isModule('pages')) ? true : false);
		$aUser = $this -> getParam('aUser');
		$iPageId = -1;
		if (empty($aUser) && $bIsPages)
		{
			$aPage = $this -> getParam('aPage');
			$iPageId = $aPage['page_id'];
		}
        $aConfig = $oDonation->getDonationOfPage($iPageId);
        $bNeedToBeConfig = false;
        if ($aConfig)
        {
            // have config and it is active
            if ($aConfig['is_active'] == 0)
            {
                if (Phpfox::isAdmin())
                {
                    $bNeedToBeConfig = true;
                }
                else
                {
                    return false;
                }
            }
        }
        else
        {
            if (Phpfox::isAdmin())
            {
                $bNeedToBeConfig = true;
            }
            else
            {
                return false;
            }
        }
        if ($bNeedToBeConfig === false && !$oDonation->checkPermissions('can_donate'))
        {
            //if not admin config and this user group cannot donate
            return false;
        }
        $sUrl = urlencode($this->url()->getFullUrl());
        $sImg = $oDonation->getDonationButtonImagePath();
        $this->template()
                ->assign(array(
                    'iPageId' => $iPageId,
                    'sUrl' => $sUrl,
                    'sImg' => $sImg,
                    'bNeedToBeConfig' => $bNeedToBeConfig
                ))
                ->setHeader(array(
                    'donation.js' => 'module_donation'
                        )
        );
		if($iPageId > 0){
			$urlSetting = Phpfox::getLib('url')->makeUrl('pages',array('add', 'id' =>$iPageId,'tab'=>'donation'));
			$iDonation = (int) $oDonation->isEnableDonation($iPageId);
			$iUserId = $oDonation->getUserIdOfPage($iPageId);
			$sPageTitle = $oDonation->getPageDetail($iPageId); 
			$sPageTitle = htmlspecialchars($sPageTitle);
			$sDonation = _p('donation.donation_for_page_page_name', array('page_name'=>$sPageTitle));
			$this->template()->assign(array(
							'urlSetting' => $urlSetting,
							'iDonation' => $iDonation,
							'iUserId' => $iUserId,
							'sDonation' => $sDonation
				)
			); 
		}
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('donation.component_block_donate_clean')) ? eval($sPlugin) : false);
    }

}

?>