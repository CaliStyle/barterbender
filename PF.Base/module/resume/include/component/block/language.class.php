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
class Resume_Component_Block_Language extends Phpfox_Component
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

		// Get language
		$iResumeId  = $this -> request() -> getInt('req3');
		$aLanguages = Phpfox::getService("resume.language") -> getAllLanguage($iResumeId);

		foreach ($aLanguages as $iKey => $aExp) {

			if (isset($aLanguages[$iKey]["note_parsed"]))
			{
				$aLanguages[$iKey]["note_parsed"] = nl2br($aLanguages[$iKey]["note_parsed"],true);

			}
		}
		$this -> template() -> assign(array('aLanguages' => $aLanguages));
	}
}

?>