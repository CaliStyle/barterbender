<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

class Directory_Component_Block_DetailCoreVideos extends Phpfox_Component
{

    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
        $aYnDirectoryDetail = $this->getParam('aYnDirectoryDetail');
        $bCanViewVideoInBusiness = Phpfox::getService('directory.permission')->canViewVideoInBusiness($aYnDirectoryDetail['aBusiness']['business_id'],
            $bRedirect = false);

        if ($bCanViewVideoInBusiness == false) {
            $this->url()->send('subscribe', null,
                _p('directory.unable_to_view_this_item_due_to_privacy_settings_please_contact_owner_business'));
        }

        $hidden_select = '';
        $sModuleId = 'video';
        $sController = $sModuleId . '.share';

        $bCanAddVideoInBusiness = Phpfox::getService('directory.permission')->canAddVideoInBusiness($aYnDirectoryDetail['aBusiness']['business_id'],
            $bRedirect = false);
        $sYnAddParamForNavigateBack = Phpfox::getService('directory.helper')->getYnAddParamForNavigateBack();

        $this->template()->assign(array(
                'sPlaceholderKeyword' => _p('directory.search_videos'),
                'aYnDirectoryDetail' => $aYnDirectoryDetail,
                'bCanAddVideoInBusiness' => $bCanAddVideoInBusiness,
                'bCanViewVideoInBusiness' => $bCanViewVideoInBusiness,
                'hidden_select' => $hidden_select,
                'sYnAddParamForNavigateBack' => $sYnAddParamForNavigateBack,
                'sModuleId' => $sModuleId,
                'sController' => $sController,
                'sUrlAddVideo' => Phpfox::getLib('url')->makeUrl($sController, array('module' => 'directory', 'item' => $aYnDirectoryDetail['aBusiness']['business_id'])),
                'sCustomClassName' => 'ync-block'
            )
        );
    }

}
