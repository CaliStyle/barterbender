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


class Socialad_Component_Controller_Ad_Detail extends Phpfox_Component 
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
	    define('PHPFOX_APP_DETAIL_PAGE',true);
		$iAdId = $this->request()->get('id');

		Phpfox::getService('socialad.permission')->canViewDetailAd($iAdId, $bRedirect= true);

		Phpfox::getService('socialad.ad')->checkAd($iAdId);

		$aAd = Phpfox::getService('socialad.ad')->getAdById($iAdId);

		$aAd = Phpfox::getService('socialad.ad')->retrievePermission($aAd);    
 
		if((int)$aAd['package_price'] == 0) {
            if(Phpfox::getUserParam('socialad.approve_ad')) {
                $sPhrase = _p('submit_for_approval');
            } else {
                $sPhrase = _p('submit');
            }
        } else {
            $sPhrase =  _p('place_order');
        }
        $aAd['action_placeorder'] = $sPhrase;
		$this->template()->assign(array(
			'aSaDetailAd' => $aAd,
		));

		$aParams = Phpfox::getService('socialad.helper')->getJsSetupParams();

		$this->template()->setHeader(array(
			'ynsocialad.js' => 'module_socialad',
			'ynsocialad.ajaxForm.js' => 'module_socialad',
			'jquery.validate.js' => 'module_socialad',
			'chosen.jquery.min.js' => 'module_socialad',
			'ajax-chosen.js' => 'module_socialad',
			'<script type="text/javascript">$Behavior.loadYnsocialAdSetupParam = function() { ynsocialad.setParams(\''. json_encode($aParams) .'\'); }</script>'
		));
		$this->template()->assign(array(
			'sCorePath' => Phpfox::getParam('core.path')
		));
		$this->template()->setTitle($aAd['ad_title'])
			->setBreadcrumb(_p('ad'), $this->url()->makeUrl('socialad.ad'))
			->setBreadcrumb($aAd['ad_title'], $this->url()->makeUrl('socialad.ad.detail', array('id'=> $aAd['ad_id'])), true);
	}

	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		$this->template()->clean(array(
				'aSaDetailAd',
			)
		);
	
		(($sPlugin = Phpfox_Plugin::get('socialad.Socialad_Component_Controller_Index_clean')) ? eval($sPlugin) : false);
	}

}

