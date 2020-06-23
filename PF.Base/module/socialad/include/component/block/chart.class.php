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
class Socialad_Component_Block_Chart extends Phpfox_Component 
{

	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		$iAdId = $this->getParam('iChartAdId');
	
		$aPeriods = array(
			array('value' => 1, 'phrase' => _p('chart_today')),
			array('value' => 2, 'phrase' => _p('chart_yesterday')),
			array('value' => 3, 'phrase' => _p('chart_last_week')),
			array('value' => 4, 'phrase' => _p('chart_rangeofdates')),
		);


		$aParams = Phpfox::getService('socialad.helper')->getJsSetupParams();
		$aParams = json_encode($aParams);
		$this->template()->assign(array(
			'iChartAdId' => $iAdId,
			'aSaPeriods' => $aPeriods,
			'aParams'	 => $aParams
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

