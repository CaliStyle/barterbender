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
class Resume_Component_Block_Certification extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		if ($this -> request() -> get('req2') != 'view') {
			return false;
		}

		// Get education
		$iResumeId = $this -> request() -> getInt('req3');
		$aCertificates = Phpfox::getService("resume.certification") -> getAllCertification($iResumeId);
		// Get working time period for each experience
		foreach ($aCertificates as $iKey => $aExp) {
			if (isset($aCertificates[$iKey]["note_parsed"]))
			{
				$aCertificates[$iKey]["note_parsed"] = nl2br($aCertificates[$iKey]["note_parsed"],true);
			}
		}
		$this -> template() -> assign(array('aCertificates' => $aCertificates));
	}
}

?>