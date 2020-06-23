<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Directory_Component_Controller_Admincp_managebusiness extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
        	// Page Number & Limit Per Page
            $iPage = $this->request()->getInt('page');
			$iPageSize = 10;
			
			// Variables
			$aVals = array();
			$aConds = array();
			$aConds[] = "AND dbus.business_status != " . (int)Phpfox::getService('directory.helper')->getConst('business.status.deleted');
			
        	// Search Filter
            $oSearch = Phpfox::getLib('search')->set(array(
					'type' 	 => 'request',
					'search' => 'search',
			));

            if ($this->request()->get('search') == 'pending') {
                $aConds[] = "AND dbus.business_status = " . (int)Phpfox::getService('directory.helper')->getConst('business.status.pending');
            }

			$aVals['title'] 		= $oSearch->get('title');	  
			$aVals['owner'] 		= $oSearch->get('owner');	  
			$aVals['creator'] 		= $oSearch->get('creator');	  
			$aVals['category_id']  = $oSearch->get('category_id');
			$aVals['status']  = $oSearch->get('status');
			$aVals['feature']= $oSearch->get('feature');
			$aVals['submit'] 		= $oSearch->get('submit');
			    
			if($aVals['title'])
			{
				$aConds[] = "AND dbus.name like '%{$aVals['title']}%'";
			}
			if($aVals['owner'])
			{
				$aConds[] = "AND u.full_name like '%{$aVals['owner']}%'";
			}
			if($aVals['creator'])
			{
				$aConds[] = "AND u2.full_name like '%{$aVals['creator']}%'";
			}
			if($aVals['category_id'] && $aVals['category_id'] != 0)
			{
				$aConds[] = "AND dc.category_id = {$aVals['category_id']}";
			}
			if($aVals['status'])
			{
				switch ($aVals['status']) {
					case 'published':
						$aConds[] = "AND (( dbus.business_status = " . (int)Phpfox::getService('directory.helper')->getConst('business.status.running')
									. ' ) OR ( dbus.business_status = '  . (int)Phpfox::getService('directory.helper')->getConst('business.status.approved') . ' )) ';
						break;
					case 'denied':
						$aConds[] = "AND dbus.business_status = " . (int)Phpfox::getService('directory.helper')->getConst('business.status.denied');
						break;	
					case 'expired':
						$aConds[] = "AND dbus.business_status = " . (int)Phpfox::getService('directory.helper')->getConst('business.status.completed');
						break;					
					case 'pending':
						$aConds[] = "AND dbus.business_status = " . (int)Phpfox::getService('directory.helper')->getConst('business.status.pending');
						break;					
					case 'pending_for_claiming':
						$aConds[] = "AND dbus.type = 'claiming' AND dbus.business_status = " . (int)Phpfox::getService('directory.helper')->getConst('business.status.draft');
						break;					
					case 'claiming':
						$aConds[] = "AND dbus.business_status = " . (int)Phpfox::getService('directory.helper')->getConst('business.status.pendingclaiming');
						break;					
				}				
			}
			if($aVals['feature'])
			{
				switch ($aVals['feature']) {
					case 'featured':
						$aConds[] = "AND ( dbus.feature_start_time <= " . PHPFOX_TIME . ' AND dbus.feature_end_time >= ' . PHPFOX_TIME . ' ) ';
						break;					
					case 'not_featured':
						$aConds[] = "AND ( dbus.feature_start_time > " . PHPFOX_TIME . ' OR dbus.feature_end_time < ' . PHPFOX_TIME . ' ) ';
						break;					
				}
			}

			list($iCount,$aList) = Phpfox::getService('directory')->getManageBusiness($aConds, $iPage,$iPageSize);

			// Set pager
			phpFox::getLib('pager')->set(array(
						'page'  => $iPage, 
						'size'  => $iPageSize, 
						'count' => $iCount
			));
			            
            $this -> template() -> setTitle(_p('directory.manage_businesses'));
                        
			$aCategories = Phpfox::getService('directory.category')->getForBrowse(NULL);

			foreach ($aList as $key => $aVal) {
				if(4294967295 == $aVal['feature_end_time'])
	            {
	            	$aList[$key]['is_unlimited'] = 1;	
	            	$aList[$key]['expired_date'] = '';
	            }
	            else{
	            	$aList[$key]['is_unlimited'] = 0;
	            	$aList[$key]['expired_date'] = Phpfox::getService('directory.helper')->convertTime($aList[$key]['feature_end_time']);	
	            }
			}
			$this -> template()
                ->setBreadCrumb(_p("Apps"), $this->url()->makeUrl('admincp.apps'))
                ->setBreadCrumb(_p('controller_directory'), $this->url()->makeUrl('admincp.app').'?id=__module_directory')
                -> setBreadcrumb(_p('directory.manage_businesses'), $this->url()->makeUrl('admincp.directory.managebusiness'));
			$this -> template() -> assign(array(
					'aList' => $aList,
					'aCategories' => $aCategories,
					'aForms'		=> $aVals,
				));		

            $this->template()->setHeader(array(
            	'managebusiness.js'			 => 'module_directory',
            ));
            $this->template()->setPhrase(array(
            	'directory.are_you_sure',
            	'directory.yes',
            	'directory.no',
            	'directory.transfer_owner',
            	'directory.confirm_feature_business_unlimited',
            	'directory.directory_confirm_feature_business_limited'
            ));
	}
	
}

?>