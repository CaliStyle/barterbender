<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');
/**
 * 
 * 
 * @copyright       [YOUNET_COPYRIGHT]
 * @author          YouNet Company
 * @package         YouNet_JobPosting
 */
class JobPosting_Component_Block_Company_Employee extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		$aEmployee = array();
		$iPages = 1;
		$aCompany = $this->getParam('aCompany');
		
		$sCond = "uf.company_id = ".$aCompany['company_id'];
		$iLimit = 6;
		list($iCntEmployee, $aEmployee) = Phpfox::getService('jobposting.company')->searchEmployees($sCond, $iPages, $iLimit);
		
		$this->template()->assign(array(
				'sHeader' => _p('employees'),
				'aEmployee' => $aEmployee,
			)
		);
		
		if(count($aEmployee)==0)
			return false;
		return 'block';
		
	}
}

?>