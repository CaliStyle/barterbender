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
class Donation_Component_Controller_Admincp_Addbuttonimage extends Phpfox_Component {

    /**
     * Class process method which is used to execute this component.
     */
    public function process()
    {
        $oDonation = Phpfox::getService('donation');
        $iPageId = -1;
        $iMaxFileSize = Phpfox::getParam('donation.max_size_for_donation_image_button') === 0 ? null : ((Phpfox::getParam('donation.max_size_for_donation_image_button') / 1024) * 1048576);
        $sDefaultImagePath = $oDonation->getDonationButtonDefaultImagePath();
        // Update donation image.
        if ($aVals = $this->request()->getArray('aVals'))
        {
            // chekbox return "" or "on"
            // this variable mentions should we use it as default or not
            $bIsDefault = $this->request()->get('default_use_as_default') ? 0 : 1;
            // Update donation button.
            if (Phpfox::getService('donation.process')->updateDonationButton($bIsDefault))
            {
                $this->url()->send('admincp.donation.addbuttonimage', null, _p('donation.donation_image_successfully_updated'));
            }
        }
        // this will be excute after updating
        $sAdminImagePath = $oDonation->getDonationButtonAdminImagePath();
        $bIsUsingDefaultButtonImage = $oDonation->isUsingDefaultButtonImage();
        $this->template()
                ->assign(array(
                    'iPageId' => $iPageId,
                    'iMaxFileSize' => $iMaxFileSize,
                    'sAdminImagePath' => $sAdminImagePath,
                    'sDefaultImagePath' => $sDefaultImagePath,
                    'iMaxFileSize_filesize' => Phpfox::getLib('phpfox.file')->filesize($iMaxFileSize),
                    'bIsUsingDefaultButtonImage' => $bIsUsingDefaultButtonImage
                ))
                ->setHeader(array(
                    'donation.js' => 'module_donation',
                    'progress.js' => 'static_script'
                ))
                ->setTitle(_p('donation.add_donation_button_image'))
                ->setPhrase(array('core.select_a_file_to_upload'))
                ->setBreadcrumb(_p('Apps'), $this->url()->makeUrl('admincp.apps'))
                ->setBreadCrumb(_p('donation'), $this->url()->makeUrl('admincp.app',['id' => '__module_donation']))
                ->setBreadcrumb(_p('donation.add_donation_button_image'), $this->url()->makeUrl('admincp.donation.addbuttonimage'));
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