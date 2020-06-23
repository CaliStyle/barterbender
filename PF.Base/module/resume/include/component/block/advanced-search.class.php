<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 *
 * @copyright      YouNet Company
 * @author         VuDP, TienNPL
 * @package        Module_Resume
 * @version        3.01
 * 
 */
class Resume_Component_Block_Advanced_Search extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		$aItemData = array();
		
		$aItems  = Phpfox::getService('resume.category')->getCategories();
		$aLevels = Phpfox::getService('resume.level')->getLevels();
		
		$aItemData = array();
		$aVals = Phpfox::getService('resume')->getAdvSearchFields();

		if(!$aVals['form_flag'] || (!empty($aVals['submit']) && $aVals['submit'] == _p('resume.reset')) )
		{
			$aVals = array(
				'gender' => '',
				'level_id' => 0,
				'year_exp_from' => 0,
				'year_exp_to' => 0
			);
		}
		
		if(!empty($aVals['category']))
		{
			foreach($aVals['category'] as $iCatId)
			{
				$aItemData[] = $iCatId;
			}
		}

		// Set param for country iso
		
		if(is_array($aVals) && key_exists('country_iso',$aVals) && $aVals['country_iso'])
		{
		
			$this->setParam(array('country_child_value' => $aVals['country_iso']));
		}
		// Param for country child id
		if(is_array($aVals) && key_exists('country_child_id',$aVals) && $aVals['country_child_id'])
		{
			$this->setParam(array('country_child_id' => $aVals['country_child_id']));
		}	
	
		
		$this->template()->assign(array(
			'aItems' => $aItems,
			'aItemData' => $aItemData,
			'aLevels'=> $aLevels,
			'aForms'	=> $aVals
		));	
	}
}