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
class Socialad_Component_Block_Report_List extends Phpfox_Component 
{

	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		$aQuery = $this->getParam('aQueryParam');
		$aQuery['item_per_page'] = 1000;

		$aResults = Phpfox::getService('socialad.ad.statistic')->report($aQuery);

		$this->setParam('aPagingParams', array(
			'total_all_result' => $aResults['total_all_result'],
			'total_result' => $aResults['total_result'],
			'page' => $aResults['page'],
			'limit' => $aResults['limit'],
			'bNoResultText' => true
		));

		$this->template()->assign(array(
			'aSaRows' => $aResults['aRows']
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

