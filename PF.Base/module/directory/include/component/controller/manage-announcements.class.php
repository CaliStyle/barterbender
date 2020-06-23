<?php

defined('PHPFOX') or exit('NO DICE!');

class Directory_Component_Controller_Manage_Announcements extends Phpfox_Component
{
	public function process()
	{
        Phpfox::getService('directory.helper')->buildMenu();
        $iEditedBusinessId = 0;
        $iAnnouncementId = 0;
        if ($this->request()->getInt('id')) {
            $iEditedBusinessId = $this->request()->getInt('id');
            $iAnnouncementId = $this->request()->getInt('idpost');
            $this->setParam('iBusinessId',$iEditedBusinessId);
        }

        if(!(int)$iEditedBusinessId){
                   $this->url()->send('directory');
        }
        
        $aBusiness = Phpfox::getService('directory')->getQuickBusinessById($iEditedBusinessId);

        // check permission 
        if(!Phpfox::getService('directory.permission')->canEditBusiness($aBusiness['user_id'],$iEditedBusinessId) ||
            !Phpfox::getService('directory.permission')->canManageAnnouncement($iEditedBusinessId)
          ){
                $this->url()->send('subscribe');
          }
        $sView = 'maincontent';
        $oValid = array();

        if ($this->request()->get('view') ) {
            $sView = $this->request()->get('view');
        }


        $aValidation = array(
            'announcement_title' => array(
                'def' => 'required',
                'title' => _p('directory.fill_in_a_title_for_your_page')
            ),
            'announcement_content' => array(
                'def' => 'required',
                'title' => _p('directory.add_some_content_to_your_page')
            )       
        );
        
        $oValid = Phpfox::getLib('validator')->set(array(
                'sFormName' => 'js_add_announcements', 
                'aParams' => $aValidation
            )
        );

        if($sView == 'maincontent'){
            list($iCnt,$aAnnouncements) = Phpfox::getService('directory')->getAnnouncementsByBusinessId($iEditedBusinessId);
               $this->template()->assign(array(
                                            'aAnnouncements'  =>  $aAnnouncements,
                                            'iCnt' => $iCnt
                                            ));
        }
        else
        if($sView == 'edit'){
                $aAnnouncement = Phpfox::getService('directory')->getAnnouncementsByIdForEdit($iAnnouncementId);
                $this->template()->assign(array(
                                            'idpost' =>  $iAnnouncementId, 
                                            'aForms'          =>  $aAnnouncement
                                            ));
        }

     

        if ($aVals = $this->request()->getArray('val'))
        {
             if(isset($aVals['add_announcements']) && $oValid->isValid($aVals) ){
                if(Phpfox::getService('directory.process')->addNewAnnouncements($aVals)) {                        
                    $this->url()->send("directory.manage-announcements",array('id' => $iEditedBusinessId),_p('directory.add_announcements_successfully'));
                }
             }

             if(isset($aVals['edit_announcements']) && $oValid->isValid($aVals) ){
                if(Phpfox::getService('directory.process')->editNewAnnouncements($aVals)) {     
                    $this->url()->send("directory.manage-announcements",array('id' => $iEditedBusinessId),_p('directory.update_announcements_successfully'));
                }
             }
        }

        $this->template()
                ->setEditor()
                ->setPhrase(array(
                    'directory.are_you_sure_you_want_to_delete_this_announcement',
                    'directory.delete_announcement',
                    'directory.delete'
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
            'iBusinessid'     => $iEditedBusinessId,
            'sView'           =>  $sView,
            'sCreateJs'       => $oValid->createJS(),
            'sGetJsForm'      => $oValid->getJsForm(),
        ));
        $this->template()->setBreadcrumb(_p('directory.manage_announcements'), $this->url()->permalink('directory.edit','id_'.$iEditedBusinessId));

        Phpfox::getService('directory.helper')->loadDirectoryJsCss();

    }

    public function clean()
    {
        
    }

}
?>