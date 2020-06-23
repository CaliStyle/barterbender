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


class Socialad_Component_Controller_Report_Export extends Phpfox_Component 
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		$aQuery = $this->request()->get('val');

		if(!Phpfox::isUser() || !$aQuery) {
			return false;
		}

		$aQuery['item_per_page'] = 10000;
		$aResults = Phpfox::getService('socialad.ad.statistic')->report($aQuery);

		$sType = isset($aQuery['export_type']) ? $aQuery['export_type'] : 'xls';
		Phpfox::getService('socialad.export')->export($aResults['aRows'], $sType);

		// download function exits php execution process after finishing
	}

	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
	
	}

}

