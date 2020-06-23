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
class Socialad_Component_Controller_Ad_Add extends Phpfox_Component 
{

	private $_iPackageId;
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{

		if(!Phpfox::getService('socialad.permission')->canCreateAd($bRedirect = true)) { // redirect and permission checking function
		}
		if(($aVals = $this->request()->getArray('val'))) { //handle submit form 
			$this->_handleSubmitForm($aVals); 
		}  

		$iAdId = $this->request()->get('id');

		if(!$iAdId) {// if this is an add new package request 	

			if(!( $iPackageId = $this->request()->get('package') )) { // should choose package first
				Phpfox::getLib('url')->send('socialad.package.choose');
			}
			if($iSimilar = $this->request()->getInt('createsimilar'))
            {
                $iNewAdId = Phpfox::getService('socialad.ad.process')->createSimilarAdFrom($iSimilar,$iPackageId);
                $sUrl = Phpfox::getLib('url')->makeUrl('socialad.ad.add', array('id' => $iNewAdId));
                Phpfox::getLib('url')->send($sUrl);
            }
			// it is a request to create a new ad
			$this->_renderAddNewAd($iPackageId);

		} else { // if this is an edit package request 
			if(Phpfox::getService('socialad.permission')->canEditAd($iAdId) == false){
				Phpfox::getLib('url')->send('socialad.ad');
			}
			$this->_renderEditAd($iAdId);
		}
        Phpfox::getService('socialad.helper')->loadSocialAdJsCss();
		$this->template()->setHeader('<script type="text/javascript">var sUserOffset = '. Phpfox::getTimeZone(). ';</script>')
        ->assign(
            array(
                    'sCorePath' => Phpfox::getParam('core.path')
                )
        )->setPhrase([
            'example_ad_title',
            'example_ad_text'
        ]);

	}

	private function _renderAddNewAd($iPackageId) {
		$this->setParam('iSaPackageId', $iPackageId);

		$this->template()->assign(array(
			'iSaPackageId' => $iPackageId,
			'iDefaultAdType' => Phpfox::getService('socialad.helper')->getConst('ad.type.feed', 'id'),
			'iDefaultItemTypeId' => Phpfox::getService('socialad.helper')->getConst('ad.itemtype.external_url', 'id'),
			'sSaSubmitPhrase' => $this->_getSubmitPhrase($iPackageId)

		));

		$this->_render();
		$this->template()->setTitle(_p('creating_an_ad'))
			->setBreadcrumb(_p('ad'), $this->url()->makeUrl('socialad.ad'))
			->setBreadcrumb( _p('creating_an_ad'), $this->url()->makeUrl('socialad.ad.add'), true);


	}

	private function _getSubmitPhrase($iPackageId, $iAdId = false) {
		$sPhrase = '';
		if($iAdId) {
			$aAd = Phpfox::getService('socialad.ad')->getAdById($iAdId);
			if(in_array($aAd['ad_status'], array(
				Phpfox::getService('socialad.helper')->getConst('ad.status.running'),
				Phpfox::getService('socialad.helper')->getConst('ad.status.paused'),
			))) { 
				return  false;
			}
		}
		$aPackage = Phpfox::getService('socialad.package')->getPackageById($iPackageId);
		if($aPackage['package_is_free']) {
			if(Phpfox::getUserParam('socialad.approve_ad')) {
				$sPhrase = _p('submit_for_approval');
			} else {
				$sPhrase = _p('submit');
			}
		} else {
			$sPhrase =  _p('place_order');
		}

		return $sPhrase;

	}

	private function _renderEditAd($iAdId) {

		$aAd = Phpfox::getService('socialad.ad')->getAdById($iAdId);

		$this->setParam('aSaAd', $aAd); // set ad for display block
		$this->setParam('aSaBasicInfoAd', $aAd); // set ad for  basic infor

		$this->setParam('iSaPackageId', $aAd['ad_package_id']);
		$this->setParam('iSaItemTypeId', $aAd['ad_item_type']);
		$aAd['placement_module'] = Phpfox::getService("socialad.ad.placement")->getModulesOfAd($aAd["ad_id"]);
		$aAd['audience_location'] = Phpfox::getService("socialad.ad.audience")->getLocationsOfAd($aAd["ad_id"]);

		$aAd = $this->_adaptAdDataWithFormData($aAd);
		$this->template()->assign(array(
			'aForms' => $aAd,
			'iSaPackageId' => $aAd['ad_package_id'],
			'iDefaultAdType' => $aAd['ad_type'],
			'iDefaultItemTypeId' => $aAd['ad_item_type'],
			'sSaSubmitPhrase' => $this->_getSubmitPhrase($aAd['ad_package_id'], $aAd['ad_id'])
		));

		$this->template()->setTitle(_p('edit_ad'))
			->setBreadcrumb(_p('ad'), $this->url()->makeUrl('socialad.ad'))
			->setBreadcrumb( _p('edit_ad'), $this->url()->makeUrl('socialad.ad.add', array('id' => $aAd['ad_id'])), true);

		$this->_render();

	}

	private function _render() {
		$aCampaignStatus = Phpfox::getService('socialad.campaign')->getAllCampaignStatus();
		$aCampaigns = Phpfox::getService('socialad.campaign')->getAllCampaignsOfUser(Phpfox::getUserId(), $aCampaignStatus['active']['id']);

		$aExtra = array(
			'campaign_id' => 0,
			'campaign_name' => _p('create_a_new_campaign')
		);
		array_unshift($aCampaigns, $aExtra); // add create a new campaign into select box
		$this->template()->assign(array(
			'aSaCampaigns' => $aCampaigns,
			'sSocialadImageRoot' => Phpfox::getService('socialad.ad.image')->getCoreImageUrl(),

			'sTermsAndConditions' => Phpfox::getService('socialad.custominfor')->getTermsAndConditions()
		));

	}

	private function _handleSubmitForm($aVals) {
		if (!$aVals) { 
			return false;
		}

        if(!isset($aVals['ad_package_id']) || $aVals['ad_package_id'] < 1)
        {
            Phpfox::addMessage(_p('Package is not valid'));
            return Phpfox_Url::instance()->send('socialad.package.choose');
        }
        else
        {
            $aPackage =  Phpfox::getService('socialad.package')->getPackageById($aVals['ad_package_id']);
            if(empty($aPackage) || !$aPackage['package_is_active'] || $aPackage['package_is_deleted'])
            {
                Phpfox::addMessage(_p('Can\'t find the package you are choosing'));
                return Phpfox_Url::instance()->send('socialad.package.choose');
            }
        }

		$iAdId = Phpfox::getService('socialad.ad.process')->handleSubmitForm($aVals);

		if($iAdId) {
			// send a request to render edit package through REST API
			if(isset($aVals['action_save'])) {

				Phpfox::getLib('url')->send('socialad.ad.detail', array('id' => $iAdId));
			} else if(isset($aVals['action_review'])) {

				Phpfox::getLib('url')->send('socialad.ad.review', array('id' => $iAdId));
			} else if(isset($aVals['action_placeorder'])) {

				$iNextStatus = Phpfox::getService('socialad.ad.process')->placeOrder($iAdId);
				switch($iNextStatus) {
				case Phpfox::getService('socialad.helper')->getConst('ad.status.running') :
				case Phpfox::getService('socialad.helper')->getConst('ad.status.pending') : 
				case Phpfox::getService('socialad.helper')->getConst('ad.status.approved') : 
					Phpfox::getLib('url')->send('socialad.ad.detail', array('id' => $iAdId));
					break;

				case Phpfox::getService('socialad.helper')->getConst('ad.status.unpaid') : 
					Phpfox::getLib('url')->send('socialad.payment.choosemethod', array('id' => $iAdId));
					break;

				}

			}
		}

	}

	private function _adaptAdDataWithFormData($aAd)
	{
        $aTimeType = Phpfox::getService('socialad.ad')->getTimeTypes();
        
        foreach ($aTimeType as $k => $sTimeType)
        {
        	$tmp = 0;
        	if((int)$aAd[$sTimeType] > 0){
	            $aAd[$sTimeType] = Phpfox::getService('socialad.helper')->convertToUserTimeZone($aAd[$sTimeType]);	           
	            $tmp = $aAd[$sTimeType];
        	} else {
        		$tmp = Phpfox::getService('socialad.helper')->convertToUserTimeZone(PHPFOX_TIME);	           
        	}

            $aAd[$sTimeType.'_day'] = date('j', $tmp);
    		$aAd[$sTimeType.'_month'] = date('n', $tmp);
    		$aAd[$sTimeType.'_year'] = date('Y', $tmp);
    		$aAd[$sTimeType.'_hour'] = date('H', $tmp);
    		$aAd[$sTimeType.'_minute'] = date('i', $tmp);
        }

		if(!$aAd['ad_expect_start_time']) { // we use expect start time to determine continuous or not
			$aAd['is_continuous'] = true;
		} else {
			$aAd['is_continuous'] = false;
		}
        
		return $aAd;
	}

	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
	}

}

