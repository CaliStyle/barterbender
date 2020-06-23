<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Fundraising_Service_Help_Help extends Phpfox_Service 
{
	public function __construct()
	{
		$this->_sTable = Phpfox::getT('fundraising_help');
	}
	
	public function get($iPage = 0 , $iLimit = 5, $sOrder = 'ASC')
	{
		$aHelps = array();
		$iTotal = 0;
		
		$aRows =  $this->database()->select('ph.*')
				->from($this->_sTable,'ph')				
				->order('ph.ordering '. $sOrder)
				->execute('getSlaveRows');
		
		if (is_array($aRows) && count($aRows))
		{
			$iTotal = count($aRows);
			foreach ($aRows as $iKey => $aRow)
			{
				if($iKey < $iPage * $iLimit)
				{
					continue;
				}
				else if ($iKey >= $iPage * $iLimit + $iLimit)
				{
					break;
				}
				$aHelps[] = $aRow;
			}			
		}

		return array($iTotal, $aHelps);
	}
	
	public function getHelpForEdit($iId)
	{
		$aRow =  $this->database()->select('ph.*')
				->from($this->_sTable,'ph')
				->where('ph.help_id = ' . $iId)
				->execute('getSlaveRow');
		
		return $aRow;
	}
	
	public function __call($sMethod, $aArguments)
	{
		/**
		 * Check if such a plug-in exists and if it does call it.
		 */
		if ($sPlugin = Phpfox_Plugin::get('fundraising.service_help_help__call'))
		{
			return eval($sPlugin);
		}
			
		/**
		 * No method or plug-in found we must throw a error.
		 */
		Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
	}	
}

?>
