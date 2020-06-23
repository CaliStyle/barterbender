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
class Resume_Component_Block_Addition extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		if ($this -> request() -> get('req2') != 'view') 
		{
			return false;
		}

		// Get additional information
		$iResumeId = $this -> request() -> getInt('req3');
		$aAddition = Phpfox::getService("resume.addition") -> getAddition($iResumeId);

		 {
			$aFields = ["sport_parsed","movies_parsed","interestes_parsed","music_parsed"];
			foreach($aFields as $sField)
			{
				if (isset($aAddition[$sField]))
				{

					$aAddition[$sField] = nl2br($aAddition[$sField],true);

				}
			}

		}

		$this -> template() -> assign(array('aAddition' => $aAddition));
	}
}

?>