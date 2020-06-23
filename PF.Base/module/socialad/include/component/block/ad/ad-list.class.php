<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright		[YOUNET_COPPYRIGHT]
 * @author  		MinhTA
 * @package  		Module_socialad
 */

// Add and edit request both go here 
class Socialad_Component_Block_Ad_Ad_List extends Phpfox_Component 
{

	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		$aCore = $this->request()->get('core');
		$iItemPerPage = 10;
		$iPage = 1;
		$aConds = array();

		$aOrders = array(
			'ad_start_time' => 'down', 
			'ad_total_impression' => 'down', 
			'ad_total_click' => 'down', 
			'ad_total_unique_click' => 'down', 
			'ad_total_reach' => 'down', 
			'ad_total_running_day' => 'down', 
		);
		$aExtra['order'] = 'ad.ad_last_edited_time DESC';
		$sOrderingField = '';
		$sOrderingType = '';
		if($aVals = $this->getParam('aQueryParam')) {
			if(isset($aVals['keyword']) && $aVals['keyword']) {
				$sKeywordParse = Phpfox::getLib('parse.input')->clean($aVals['keyword']);
				$aConds[] = 'ad.ad_title like \'%' . $sKeywordParse . '%\' ';
			}

			if(isset($aVals['ad_status']) && $aVals['ad_status']) {
				$aConds[] = 'ad.ad_status = ' . $aVals['ad_status'];
			}

			if(isset($aVals['ad_campaign_id']) && $aVals['ad_campaign_id']) {
				$aConds[] = 'ad.ad_campaign_id = ' . $aVals['ad_campaign_id'];
			}

			if(isset($aVals['ad_type']) && $aVals['ad_type']) {
				$aConds[] = 'ad.ad_type = ' . $aVals['ad_type'];
			}

			if(isset($aVals['page']) && $aVals['page']) {
				$iPage = $aVals['page'];
			}

			if(isset($aVals['order']) && $aVals['order']) {
				$aPart = explode('|', $aVals['order']);
				$sFieldName = $aPart[0];
				$sOrder = $aPart[1];
				$aOrders[$sFieldName] = $sOrder == 'ASC' ? 'down' : 'up'; //if we are showing ascending, next order should be descending (down)
				$aExtra['order'] = "ad.{$sFieldName} {$sOrder} ";
				$sOrderingField = $sFieldName;
				$sOrderingType = $sOrder == 'ASC' ? 'up' : 'down'; //if we are showing ascending, next order should be descending (down)
			}
		}

		$aExtra['limit'] = $iItemPerPage;
		$aExtra['page'] = $iPage; // without count, page is offset
		
		$isAdmin = true;
		if(!isset($aCore['is_admincp'])){
			if(!Phpfox::isAdminPanel()) {		
				$isAdmin = false;
				$aConds[] = 'ad.ad_user_id = ' . Phpfox::getUserId();
			}
		} else if($aCore['is_admincp'] !=  1){
			// check for ajax request 
			$isAdmin = false;
			$aConds[] = 'ad.ad_user_id = ' . Phpfox::getUserId();
		}
		$aAds = Phpfox::getService('socialad.ad')->getAds($aConds, $aExtra);
		foreach($aAds as &$aAd) {
			$aAd = Phpfox::getService('socialad.ad')->retrievePermission($aAd);
		}

		$this->setParam('aPagingParams', array(
			'total_all_result' => Phpfox::getService('socialad.ad')->count($aConds),
			'total_result' => count($aAds),
			'page' => $iPage,
			'limit' => $iItemPerPage
		));
        foreach ($aAds as &$Ad)
        {
            if((int)$Ad['package_price'] == 0) {
                if(Phpfox::getUserParam('socialad.approve_ad')) {
                    $sPhrase = _p('submit_for_approval');
                } else {
                    $sPhrase = _p('submit');
                }
            } else {
                $sPhrase =  _p('place_order');
            }
            $Ad['action_placeorder'] = $sPhrase;
        }
		$this->template()->assign(array(
			'aSaAds' => $aAds,
			'isAdmin' => $isAdmin,
			'aSaOrders' => $aOrders,
			'sOrderingField' => $sOrderingField,
			'sOrderingType' => $sOrderingType,
		));
	}

	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
	
	}

}

