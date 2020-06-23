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
class Socialad_Component_Block_Tooltip extends Phpfox_Component 
{

	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		$sTooltipName = $this->getParam('sTooltipName');
		$sExtraClass = $this->getParam('sExtraClass', '');
		$sTitle = _p("{$sTooltipName}");
		$sDescription = _p("{$sTooltipName}_tooltip_description");

		$this->template()->assign(array( 
			'sTooltipTitle' => $sTitle,
			'sTooltipDescription' => $sDescription,
			'sExtraClass' => $sExtraClass
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

