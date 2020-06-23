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
class Donation_Component_Block_Config extends Phpfox_Component {

    /**
     * Class process method which is used to execute this component.
     */
    public function process()
    {
        $iPageId = $this->request()->getInt('iPageId');
        $bAjaxCall = $this->getParam('bAjaxCall', false);
        $iActive = 1;
        $sEmail = '';
        $sContent = '';
        $sTermOfService = '';
        $sSubject = '';
        $aDonation = Phpfox::getService('donation')->getDonationConfig($iPageId, Phpfox::getUserId());
        if ($aDonation)
        {
            $iActive = $aDonation['is_active'];
            $sEmail = $aDonation['email'];
            $sContent = $aDonation['content'];
            $sTermOfService = $aDonation['term_of_service'];
            $sSubject = $aDonation['subject'];
        }
        $aForms['email_content'] = isset($aDonation['email_content']) ? $aDonation['email_content'] : '';
        $this->template()
                ->assign(array(
            'iPageId' => $iPageId,
            'bAjaxCall' => $bAjaxCall,
            'sEmail' => $sEmail,
            'iActive' => $iActive,
            'content' => $sContent,
            'sTermOfService' => $sTermOfService,
            'sSubject' => $sSubject,
            'aForms' => $aForms
        ))
                ->setEditor();
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('donation.component_block_config_clean')) ? eval($sPlugin) : false);
    }

}

?>