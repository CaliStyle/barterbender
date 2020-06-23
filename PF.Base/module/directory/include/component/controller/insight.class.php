<?php

defined('PHPFOX') or exit('NO DICE!');

class Directory_Component_Controller_Insight extends Phpfox_Component
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
        if (empty($aBusiness)) {
            return Phpfox_Error::display(_p('unable_to_find_the_business'));
        }
        $aBusiness = Phpfox::getService('directory')->retrieveMoreInfoFromBusiness($aBusiness,'');
        // check permission
        // if user don't have permission,it will redirect to subscribe
        // if not,redirect to insight page 
        if(!Phpfox::getService('directory.permission')->canEditBusiness($aBusiness['user_id'],$iEditedBusinessId)
          ){
                $this->url()->send('subscribe');
          }
          
        //$aPackageBusiness = Phpfox::getService('directory.package')->getById($aBusiness['package_id']);
        $aPackageBusiness = json_decode($aBusiness['package_data'],true);
        
        if($aBusiness['package_end_time'] != ''){
            $aBusiness['expire_date'] = Phpfox::getService('directory.helper')->convertTime($aBusiness['package_end_time']);
        }
        else
        {
            $aBusiness['expire_date'] =  _p('directory.never_expire');
        }
        if($aBusiness['feature_start_time'] <= PHPFOX_TIME &&  $aBusiness['feature_end_time'] >= PHPFOX_TIME){
            $aBusiness['featured'] = true;
        }
        else{
            $aBusiness['featured'] = false;
        }
        if(4294967295 == $aBusiness['feature_end_time'])
        {
            $aBusiness['is_unlimited'] = 1;   
            $aBusiness['feature_expired_date'] = '';
        }
        else if($aBusiness['feature_start_time'] <= PHPFOX_TIME && $aBusiness['feature_end_time'] >= PHPFOX_TIME)
        {
            $aBusiness['is_unlimited'] = 0;
            $aBusiness['feature_expired_date'] = Phpfox::getService('directory.helper')->convertTime($aBusiness['feature_end_time']);   
        }

        $aTransaction = Phpfox::getService('directory')->getLastestPayment($iEditedBusinessId);
        if(count($aTransaction)){

            $aBusiness['time_paid'] = Phpfox::getService('directory.helper')->convertTime($aTransaction['time_stamp']);
        }

        if($aBusiness['time_approved'] != ''){
                $aBusiness['time_approved'] = Phpfox::getService('directory.helper')->convertTime($aBusiness['time_approved']);
        }


        $aBusiness['count_member'] = Phpfox::getService('directory')->getCountMemberOfBusiness($iEditedBusinessId);
      
        $aBusiness['count_follower'] = Phpfox::getService('directory')->getCountFollowerOfBusiness($iEditedBusinessId);
       
        $aBusiness['count_pages'] = Phpfox::getService('directory')->getCountPageOfBusiness($iEditedBusinessId);

        $aModules = Phpfox::getService('directory')->getPageModuleForManage($iEditedBusinessId);

        // get data for menu (which is as same as dashboard)        
        $aModules = Phpfox::getService('directory')->getPageModuleForManage($aBusiness['business_id']);
        $aModuleView  = array();
        $IsModuleActive = false;
        $sView = '';
        foreach ($aModules[0] as  $iModuleId => $aModule) {
            $aItem = Phpfox::getService('directory')->getPageByBusinessModuleId($aBusiness['business_id'],$iModuleId);
             if(isset($aItem['module_name'])){
                $aModuleView[$aItem['module_name']] =  $aItem; 

                $sTitle = $aBusiness['name'];
                if (!empty($sTitle))
                {
                    if (preg_match('/\{phrase var\=(.*)\}/i', $sTitle, $aMatches) && isset($aMatches[1]))
                    {
                        $sTitle = str_replace(array("'", '"', '&#039;'), '', $aMatches[1]);
                        $sTitle = _p($sTitle);
                    }
                    
                    $sTitle = Phpfox::getLib('url')->cleanTitle($sTitle);
                }

                $aModuleView[$aItem['module_name']]['link'] =   Phpfox::getLib('url')->makeUrl('directory.detail'.'.'.$aBusiness['business_id'].'.'.$sTitle.'.'.$aItem['module_name']); 
                
                $aModuleView[$aItem['module_name']]['active'] =  false ;
                if($sView == '' && $aModuleView[$aItem['module_name']]['module_landing']){
                    $aModuleView[$aItem['module_name']]['active'] =  true ;
                    $IsModuleActive = true;
                }
                else
                if($sView == $aItem['module_name']){
                    $aModuleView[$aItem['module_name']]['active'] =  true ;
                    $IsModuleActive = true;

                }
             }
        }

        // get number of items in business 
        $aNumberOfItem = array(
            'photos' => Phpfox::getService('directory')->getNumberOfItemInBusiness($aBusiness['business_id'], 'photos'), 
            'videos' => Phpfox::getService('directory')->getNumberOfItemInBusiness($aBusiness['business_id'], 'v'),
            'musics' => Phpfox::getService('directory')->getNumberOfItemInBusiness($aBusiness['business_id'], 'musics'), 
            'blogs' => Phpfox::getService('directory')->getNumberOfItemInBusiness($aBusiness['business_id'], 'blogs'), 
            'polls' => Phpfox::getService('directory')->getNumberOfItemInBusiness($aBusiness['business_id'], 'polls'), 
            'coupons' => Phpfox::getService('directory')->getNumberOfItemInBusiness($aBusiness['business_id'], 'coupons'), 
            'events' => Phpfox::getService('directory')->getNumberOfItemInBusiness($aBusiness['business_id'], 'events'), 
            'jobs' => Phpfox::getService('directory')->getNumberOfItemInBusiness($aBusiness['business_id'], 'jobs'), 
            'marketplace' => Phpfox::getService('directory')->getNumberOfItemInBusiness($aBusiness['business_id'], 'marketplace'), 
        );

        $this->template()
                ->setEditor()
                ->setPhrase(array(
                    'directory.reviews',
                    'directory.members',
                    'directory.followers',
                    'directory.comments',
                    'directory.likes',
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
                    'jquery.flot.js' => 'module_directory',
                ))
                ;

        $this->template()->assign(array(
            'aBusiness'         =>  $aBusiness,
            // 'aModuleCount'  =>  $aModuleCount,
            'aModuleView'  =>  $aModuleView,
            'aNumberOfItem'  =>  $aNumberOfItem,
            'iBusinessid' => $iEditedBusinessId,
        ));  
        if(isset($aPackageBusiness['package_id']))
        {
            $this->template()->assign(array(
                'aPackageBusiness'  =>  $aPackageBusiness,
            ));  
        }
        $this->template()->setBreadcrumb(_p('directory.insight'), $this->url()->permalink('directory.edit','id_'.$iEditedBusinessId));

        Phpfox::getService('directory.helper')->loadDirectoryJsCss();

    }

    public function clean()
    {
        
    }

}
?>