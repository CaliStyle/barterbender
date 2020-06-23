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
class Donation_Component_Controller_Admincp_Index extends Phpfox_Component {

    /**
     * Class process method which is used to execute this component.
     */
    public function process()
    {
        $iPageId = -1;
        $iActive = 0;
        $sEmail = '';
        $sContent = '';
        $sTermOfService = '';
        $sSubject = '';
        $aDonation = Phpfox::getService('donation')->getDonationConfig($iPageId, 1);
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
                    'sEmail' => $sEmail,
                    'iActive' => $iActive,
                    'content' => $sContent,
                    'sTermOfService' => $sTermOfService,
                    'sSubject' => $sSubject,
                    'aForms' => $aForms
                ))
                ->setHeader(array(
                    'donation.js' => 'module_donation'
                ))
                ->setTitle(_p('donation.manage_donation'))
                ->setBreadcrumb(_p('Apps'), $this->url()->makeUrl('admincp.apps'))
                ->setBreadCrumb(_p('donation'), $this->url()->makeUrl('admincp.app',['id' => '__module_donation']))
                ->setBreadcrumb(_p('donation.manage_donation'), $this->url()->makeUrl('admincp.donation'))
                ->setEditor();
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('donation.component_controller_index_clean')) ? eval($sPlugin) : false);
    }

}

?>