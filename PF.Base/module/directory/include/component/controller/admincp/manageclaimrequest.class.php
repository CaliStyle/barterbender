<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Directory_Component_Controller_Admincp_manageclaimrequest extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{

		// Page Number & Limit Per Page
        $iPage = $this->request()->getInt('page');
		$iPageSize = 10; 
		$aVals = array();
		$aConds = array();
		
    	// Search Filter
        $oSearch = Phpfox::getLib('search')->set(array(
			'type' 	 => 'request',
			'search' => 'search',
		));

        $aValsDate = $this->request()->get('val');
        if (isset($aValsDate)) {
            if(isset($aValsDate['from_month'])) {
                $aVals['fromdate'] = $aValsDate['from_month'] . '/' . $aValsDate['from_day'] . '/' . $aValsDate['from_year'];
            }
            if(isset($aValsDate['to_month'])) {
                $aVals['todate'] = $aValsDate['to_month'] . '/' . $aValsDate['to_day'] . '/' . $aValsDate['to_year'];
            }
        }
        $aVals['from_day'] = Phpfox::getTime('j');
        $aVals['from_month'] = Phpfox::getTime('n');
        $aVals['from_year'] = Phpfox::getTime('Y');
        $aVals['to_day'] = Phpfox::getTime('j');
        $aVals['to_month'] = Phpfox::getTime('n');
        $aVals['to_year'] = Phpfox::getTime('Y');
		$aVals['title'] 		= $oSearch->get('title');	  
		$aVals['username'] 		= $oSearch->get('username');
		$aVals['submit'] 		= $oSearch->get('submit');

		if($aVals['title'])
		{
			$aConds[] = "AND dbus.name like '%{$aVals['title']}%'";
		}

		if($aVals['username'])
		{
			$aConds[] = "AND u.full_name like '%{$aVals['username']}%'";
		}

		if($aVals['fromdate'])
		{
			$iFromTime = strtotime($aVals['fromdate']);
			$aConds[] = "AND dbus.timestamp_claimrequest >= {$iFromTime}";
		}

		if($aVals['todate'])
		{
			$iToTime = strtotime($aVals['todate'])+23*60*60+59*60+59;
			$aConds[] = "AND dbus.timestamp_claimrequest <= {$iToTime}";
		}

        list($iCount,$aList) = Phpfox::getService('directory')->getClaimRequest($aConds, $iPage,$iPageSize);
        foreach ($aList as $key => $value) {
        	$timestamp_claimrequest = Phpfox::getService('directory.helper')->convertToUserTimeZone($value['timestamp_claimrequest']);
        	$aList[$key]['timestamp_claimrequest_convert'] = Phpfox::getService('directory.helper')->convertTime($timestamp_claimrequest);
        }

		if ($iBusinessId = $this->request()->getInt('approve'))
		{
			if (Phpfox::getService('directory.process')->approveClaimRequest($iBusinessId))
			{
				$aBusiness = Phpfox::getService('directory')->getQuickBusinessById($iBusinessId);
	            // send email to owner
	            $aUser = Phpfox::getService('user')->getUser($aBusiness['user_id']);
	            $language_id = $aUser['language_id'] == null ? 'en' : $aUser['language_id'];
	            $email = $aUser['email'];
	            $aEmail = Phpfox::getService('directory.mail')->getEmailMessageFromTemplate(3 , $language_id , $iBusinessId, $aBusiness['user_id']);
	            Phpfox::getService('directory.mail.send')->send($aEmail['subject'], $aEmail['message'], $email);

				$this->url()->send('admincp.directory.manageclaimrequest', array(), _p('directory.approve_successfully'));
			}
		}

		if ($iBusinessId = $this->request()->getInt('deny'))
		{
			if (Phpfox::getService('directory.process')->denyClaimRequest($iBusinessId))
			{
				$this->url()->send('admincp.directory.manageclaimrequest', array(), _p('directory.deny_successfully'));
			}
		}

			Phpfox::getLib('pager')->set(array(
						'page'  => $iPage, 
						'size'  => $iPageSize, 
						'count' => $iCount
			));

		$this->template()->setTitle(_p('directory.manage_claim_request'))
            ->setBreadCrumb(_p("Apps"), $this->url()->makeUrl('admincp.apps'))
            ->setBreadCrumb(_p('controller_directory'), $this->url()->makeUrl('admincp.app').'?id=__module_directory')
			->setBreadcrumb(_p('directory.manage_claim_request'))
			->assign(array(
					'aList' => $aList,
					'aForms'		=> $aVals, 
					'sCorePath' => Phpfox::getParam('core.path'),
						)
		)
		->setHeader(array(
			))
		;
			
	}
	
}

?>