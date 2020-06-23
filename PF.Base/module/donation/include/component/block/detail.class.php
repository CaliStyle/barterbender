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
class Donation_Component_Block_Detail extends Phpfox_Component {

    /**
     * Class process method which is used to execute this component.
     */
    public function process()
    {
        $oDonation = Phpfox::getService('donation');
        $iPageId = $this->request()->getInt('iPageId');
        $sUrl = urlencode($this->request()->get('sUrl'));
        $iUserId = Phpfox::getUserId();
        $sContent = null;
        $sTermOfService = null;
        $sPageTitle = $oDonation->getPageDetail($iPageId);
        $iPageUserId = $oDonation->getUserIdOfPage($iPageId);
        // Get the donation.
        $aDonation = $oDonation->getDonationConfig($iPageId, $iPageUserId);
        if (!empty($aDonation))
        {
            if (Phpfox::getParam('core.allow_html'))
            {
                $sContent = $aDonation['content_parsed'];
                $sTermOfService = $aDonation['term_of_service_parsed'];
            }
            else
            {
                $sContent = $aDonation['content'];
                $sTermOfService = $aDonation['term_of_service'];
            }
        }
        if ($iPageUserId == $iUserId || Phpfox::isAdmin())
        {
            $bMyPage = true;
        }
        else
        {
            $bMyPage = false;
        }
        // In page.
        if (($iPageId > 0 || $iPageId == -1) && $sPageTitle != null)
        {
            $this->setParam('iPageId', $iPageId);
            $this->setParam('iUserId', $iUserId);
            $sText = _p('donation.page_donation_title_homepage');
            $this->template()->assign(array(
                'sText' => $sText,
                'iPageId' => $iPageId,
                'sUrl' => $sUrl,
                'aCurrentCurrencies' => $oDonation->getCurrentCurrencies('paypal'),
                'bMyPage' => $bMyPage,
                'iUserId' => $iUserId,
                'sContent' => $sContent,
                'sTermOfService' => $sTermOfService,
            ));
        }
        else
        {
            echo _p('donation.please_login_to_donate');
            exit();
        }
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('donation.component_block_detail_clean')) ? eval($sPlugin) : false);
    }

}

?>