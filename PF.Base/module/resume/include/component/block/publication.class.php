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
class Resume_Component_Block_Publication extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		if ($this -> request() -> get('req2') != 'view') {
			return false;
		}

		// Get publication
		$iResumeId = $this -> request() -> getInt('req3');
		$aPublications = Phpfox::getService("resume.publication") -> getAllPublication($iResumeId);
		foreach ($aPublications as $iKey => $aExp) {

			if (isset($aPublications[$iKey]["note_parsed"]))
			{
				$aPublications[$iKey]["note_parsed"] = nl2br($aPublications[$iKey]["note_parsed"],true);

			}
		}
		$this -> template() -> assign(array('aPublications' => $aPublications));
	}
}

?>