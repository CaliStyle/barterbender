<?php

defined('PHPFOX') or exit('NO DICE!');

class Directory_Component_Controller_Dashboard extends Phpfox_Component
{
    public function process()
    {

        Phpfox::getService('directory.helper')->buildMenu();
        /*redirect to appropriate controller*/
        $id = $this->request()->getInt('id');

        if (!(int)$id) {
            $this->url()->send('directory');
        }
        $aBusiness = Phpfox::getService('directory')->getQuickBusinessById($id);
        // check permission
        if (
        !Phpfox::getService('directory.permission')->canEditBusiness($aBusiness['user_id'], $id)
        ) {
            $this->url()->send('subscribe');
        }

        if (Phpfox::getService('directory.permission')->canManageEditInfoDashBoard($id, $aBusiness)) {
            $this->url()->send("directory.edit", array('id' => $id), '');
        } else
            if (Phpfox::getService('directory.permission')->canManageCoverPhotosDashBoard($id, $aBusiness)) {
                $this->url()->send("directory.cover-photos", array('id' => $id), '');
            } else
                if (Phpfox::getService('directory.permission')->canManagePagesDashBoard($id, $aBusiness)) {
                    $this->url()->send("directory.manage-pages", array('id' => $id), '');
                } else
                    if (Phpfox::getService('directory.permission')->canAddMemberRoleDashBoard($id, $aBusiness)) {
                        $this->url()->send("directory.manage-member-roles", array('id' => $id), '');
                    } else
                        if (Phpfox::getService('directory.permission')->canConfigureSettingRoleDashBoard($id, $aBusiness)) {
                            $this->url()->send("directory.member-role-settings", array('id' => $id), '');
                        } else
                            if (Phpfox::getService('directory.permission')->canManageAnnouncement($id, $aBusiness)) {
                                $this->url()->send("directory.manage-announcements", array('id' => $id), '');
                            } else
                                if (Phpfox::getService('directory.permission')->canManageModule($id, $aBusiness)) {
                                    $this->url()->send("directory.manage-modules", array('id' => $id), '');
                                } else
                                    if (Phpfox::getService('directory.permission')->canChangeBusinessTheme($id, $aBusiness)) {
                                        $this->url()->send("directory.manage-business-theme", array('id' => $id), '');
                                    } else
                                        if (Phpfox::getService('directory.permission')->canUpdatePackage($id, $aBusiness)) {
                                            $this->url()->send("directory.manage-packages", array('id' => $id), '');
                                        } else {
                                            $this->url()->send("directory.insight", array('id' => $id), '');
                                        }

    }

    public function clean()
    {

    }
}
