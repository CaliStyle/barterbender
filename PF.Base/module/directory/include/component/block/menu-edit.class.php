<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

class Directory_Component_Block_Menu_Edit extends Phpfox_Component
{

    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
        $sTabView = $this->request()->get('req2');
        $id = $this->request()->get('id');
        $aBusiness = Phpfox::getService('directory')->getQuickBusinessById($id);
        if(!$aBusiness)
        {
            Phpfox::getLib('url')->send('directory', null, _p('directory.business_not_found'));
        }
       
        $aPermission = array();
        $isClaiming = ($aBusiness['type'] == 'claiming' && Phpfox::getService('directory.helper')->getConst('business.status.claimingdraft') == $aBusiness['business_status']) ? 1 : 0;
        $aPermission['insight'] = ($isClaiming == 0) ? 1 : 0 ;

        if(Phpfox::getService('directory.permission')->canManageEditInfoDashBoard($id, $aBusiness)){
            $aPermission['edit'] = 1 ;
        }
        if(Phpfox::getService('directory.permission')->canManageCoverPhotosDashBoard($id, $aBusiness)){
            $aPermission['coverphotos'] = 1 ;
        }
        if(Phpfox::getService('directory.permission')->canManagePagesDashBoard($id, $aBusiness) && $isClaiming == 0){
            $aPermission['managepages'] = 1 ;

        }
        if(Phpfox::getService('directory.permission')->canAddMemberRoleDashBoard($id, $aBusiness) && $isClaiming == 0){
            $aPermission['managememberroles'] = 1 ;

        }
        if(Phpfox::getService('directory.permission')->canConfigureSettingRoleDashBoard($id, $aBusiness) && $isClaiming == 0){
            $aPermission['memberrolesettings'] = 1;
        }
        if(Phpfox::getService('directory.permission')->canManageAnnouncement($id, $aBusiness) && $isClaiming == 0){
            $aPermission['manageannouncements'] = 1;
        }
        if(Phpfox::getService('directory.permission')->canManageModule($id, $aBusiness) && $isClaiming == 0){
            $aPermission['managemodules'] = 1 ;
        }
        if(Phpfox::getService('directory.permission')->canChangeBusinessTheme($id, $aBusiness) && $isClaiming == 0){
            $aPermission['managebusinesstheme'] = 1;
        }
        if(Phpfox::getService('directory.permission')->canUpdatePackage($id, $aBusiness) && $isClaiming == 0){
            $aPermission['managepackages'] = 1;
        }

/*        print_r($aPermission);
        die;*/

        $this->template()->assign(array(
                'sHeader' => '',
                'sTabView'      => $sTabView,
                'aPermission'   => $aPermission,
                'sCustomClassName' => 'ync-block'
            )
        );

        return 'block';
    }

}

?>