<?php

defined('PHPFOX') or exit('NO DICE!');

class Directory_Component_Controller_Manage_Member_Roles extends Phpfox_Component
{


	public function process()
	{
        Phpfox::getService('directory.helper')->buildMenu();
        $iEditedBusinessId = 0;
        if ($this->request()->getInt('id')) {
            $iEditedBusinessId = $this->request()->getInt('id');
            $this->setParam('iBusinessId',$iEditedBusinessId);
        }

        if(!(int)$iEditedBusinessId){
                   $this->url()->send('directory');
        }
        $aBusiness = Phpfox::getService('directory')->getQuickBusinessById($iEditedBusinessId);

        // check permission 
        if(!Phpfox::getService('directory.permission')->canEditBusiness($aBusiness['user_id'],$iEditedBusinessId) ||
            !Phpfox::getService('directory.permission')->canAddMemberRoleDashBoard($iEditedBusinessId)
          ){
                $this->url()->send('subscribe');
          }
          
        $aMemberRoles = Phpfox::getService('directory')->getMemberRolesByBusinessId($iEditedBusinessId);
     
            /*echo '<pre>';
            print_r($aMemberRoles);
            die;*/
        if ($aVals = $this->request()->getArray('val'))
        {

        }

        $this->template()
                ->setEditor()
                ->setPhrase(array(
                    'directory.add_new_role',
                    'directory.edit_role',
                    'directory.delete_role',
                    'directory.delete',
                    'directory.confirm_delete_role_member',
                ))
                ->setHeader('cache', array(
                    'pager.css' => 'style_css',
                    'jquery/plugin/jquery.highlightFade.js' => 'static_script',
                    'switch_legend.js' => 'static_script',
                    'switch_menu.js' => 'static_script',
                    'quick_edit.js' => 'static_script',
                    'progress.js' => 'static_script',
                    'share.js' => 'module_attachment',
                    'country.js' => 'module_core',
                ))
                ;

        $this->template()->assign(array(
            'aMemberRoles'  =>  $aMemberRoles,
            'iBusinessid' => $iEditedBusinessId,
        ));
        $this->template()->setBreadcrumb(_p('directory.manage_member_roles'), $this->url()->permalink('directory.edit','id_'.$iEditedBusinessId));

        Phpfox::getService('directory.helper')->loadDirectoryJsCss();

    }

    public function clean()
    {
        
    }

}
?>