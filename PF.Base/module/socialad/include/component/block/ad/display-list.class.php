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
class Socialad_Component_Block_Ad_Display_List extends Phpfox_Component 
{

	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		$aAds = $this->getParam('aDisplayAds');
        if(!count($aAds)){
            return false;
        }
		$bDisplayingHtml = $this->getParam('bDisplayingHtml', false);

		$sDisplayDivId = 'js_ynsa_display_ad';
		if($bDisplayingHtml) {
			$sDisplayDivId = 'js_ynsa_display_html_ad';
		}
		$aAdDisplay = $this->getParam('aAdDisplay',[]);
        foreach($aAds as $key => $aAd)
        {
            if(in_array($aAd['ad_id'],$aAdDisplay))
            {
                unset($aAds[$key]);
            }
            else{
                $aAdDisplay[] = $aAd['ad_id'];
            }
        }
        $this->setParam('aAdDisplay',$aAdDisplay);
		$this->template()->assign(array( 
			'aDisplayAds' => $aAds,
			'bDisplayingHtml' => $bDisplayingHtml,
			'sDisplayDivId' => $sDisplayDivId,
			'iYnsaBlockId' => $this->getParam('iYnsaBlockId'),
			'sYnsaModuleId' => $this->getParam('sYnsaModuleId'),
			'iAjaxRefreshTime' => Phpfox::getParam('socialad.html_ad_ajax_refresh_time') * 1000,
			'bIsContentOnly' => $this->getParam('bIsContentOnly', false)

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

