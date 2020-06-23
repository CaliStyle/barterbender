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
class Resume_Component_Block_Experience extends Phpfox_Component {
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process() {
		if ($this -> request() -> get('req2') != 'view') {
			return false;
		}

		// Get experience
		$iResumeId = $this -> request() -> getInt('req3');
		$aExperience = Phpfox::getService("resume.experience") -> getAllExperience($iResumeId);

		// Get working time period for each experience
		foreach ($aExperience as $iKey => $aExp) {
			if ($aExp['is_working_here']) {
				$aExp['end_month'] = date("m");
				$aExp['end_year'] = date("Y");
			}
			$iYearPeriod = $aExp['end_year'] - $aExp['start_year'];
			$iMonthPeriod = $aExp['end_month'] - $aExp['start_month'];
			
			if($iMonthPeriod < 0)
			{
				$iMonthPeriod = $iMonthPeriod + 12;
				$iYearPeriod  = $iYearPeriod - 1;
			}
			
			$aExperience[$iKey]['period'] = " ";
			
			if($iYearPeriod > 1)
			{
				$aExperience[$iKey]['period'] .= $iYearPeriod . " " . _p("resume.years") . " ";
			}
			elseif($iYearPeriod == 1)
			{
				$aExperience[$iKey]['period'] .= $iYearPeriod . " " . _p("resume.lowercase_year") . " ";
			}
			
			if($iMonthPeriod > 1)
			{
				$aExperience[$iKey]['period'] .= $iMonthPeriod . " " . _p("resume.months") . " ";
			}
			elseif($iMonthPeriod == 1)
			{
				$aExperience[$iKey]['period'] .= $iMonthPeriod . " " . _p("resume.month") . " ";
			}
			if (isset($aExperience[$iKey]["description_parsed"]))
				$aExperience[$iKey]["description_parsed"] = nl2br($aExperience[$iKey]["description_parsed"],true);
		}
		$this -> template() -> assign(array('aExperience' => $aExperience));
	}

}
?>
