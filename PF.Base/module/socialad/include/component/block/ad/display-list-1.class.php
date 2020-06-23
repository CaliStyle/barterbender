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
class Socialad_Component_Block_Ad_Display_List_1 extends Phpfox_Component
{

	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
        $aBannerAds = $aHtmlAds = [];

        $sSaModuleId = Phpfox::getLib('module')->getModuleName();
        if(Phpfox::getService('socialad.session')->shouldShowBanner()) {
            $aBannerAds = Phpfox::getService('socialad.ad')->getToDisplayOnBlock([
                'user_id' => Phpfox::getUserId(),
                'block_id' => 1,
                'module_id' => $sSaModuleId,
                'banner' => true,
                'limit' => Phpfox::getParam('socialad.maxium_number_of_banner_ads')
            ]);
        }
        if(!count($aBannerAds)){
            return false;
        }

        $this->template()->assign(array(
            'aBannerAds' => $aBannerAds,
            'sYnsaModuleId' => $sSaModuleId,
            'iAjaxRefreshTime' => Phpfox::getParam('socialad.html_ad_ajax_refresh_time') * 1000,

        ));
        return 'block';
	}

	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		$this->setParam('bDisplayingHtml', false);
	
	}

}

