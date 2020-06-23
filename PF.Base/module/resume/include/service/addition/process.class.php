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
class Resume_Service_Addition_Process extends Phpfox_Service
{
	
	public function __construct()
	{
		$this->_sTable = Phpfox::getT('resume_addition');
	}
	public function add($aVals)
	{
		$oFilter = Phpfox::getLib('parse.input');
		$aSql = array(
			'sport' => $oFilter->clean(isset($aVals['sport'])?$aVals['sport']:''),
			'resume_id' => $aVals['resume_id'],
			'movies' => $oFilter->clean(isset($aVals['movies'])?$aVals['movies']:''),
			'interests' => $oFilter->clean(isset($aVals['interests'])?$aVals['interests']:''),
			'music' => $oFilter->clean(isset($aVals['music'])?$aVals['music']:''),

			'sport_parsed' => $oFilter->prepare(isset($aVals['sport'])?$aVals['sport']:''),
			'movies_parsed' => $oFilter->prepare(isset($aVals['movies'])?$aVals['movies']:''),
			'interestes_parsed' => $oFilter->prepare(isset($aVals['interests'])?$aVals['interests']:''),
			'music_parsed' => $oFilter->prepare(isset($aVals['music'])?$aVals['music']:''),

		);
		if (isset($aVals['website']))
		{
			if(count($aVals['website'])>0)
			{
				foreach($aVals['website'] as $key=>$email)
				{
					if(empty($email))
					{
						unset($aVals['website'][$key]);
					}
				}
				$aSql['website'] = serialize($aVals['website']);
			}
		}

		                                                                                                                                                                                                                                                       
		$iId = $this->database()->insert($this->_sTable ,$aSql);		
		return $iId;
	}
	
	public function update($aVals)
	{
		$oFilter = Phpfox::getLib('parse.input');
		$aSql = array(
			'sport' => $oFilter->clean($aVals['sport']),
			'movies' => $oFilter->clean($aVals['movies']),
			'interests' => $oFilter->clean($aVals['interests']),
			'music' => $oFilter->clean($aVals['music']),

			'sport_parsed' => $oFilter->prepare(isset($aVals['sport'])?$aVals['sport']:''),
			'movies_parsed' => $oFilter->prepare(isset($aVals['movies'])?$aVals['movies']:''),
			'interestes_parsed' => $oFilter->prepare(isset($aVals['interests'])?$aVals['interests']:''),
			'music_parsed' => $oFilter->prepare(isset($aVals['music'])?$aVals['music']:''),
		);
		
		if(count($aVals['website'])>0)
		{
			foreach($aVals['website'] as $key=>$email)
			{
				if(empty($email))
				{
					unset($aVals['website'][$key]);
				}
			}
			
			$aSql['website'] = serialize($aVals['website']);
		}
		else
		{
			$aSql['website'] ="";
		}
		                                                                                                                                                                                                                                                       
		$iId = $this->database()->update($this->_sTable ,$aSql,'resume_id='.$aVals['resume_id']);		
		return $iId;
	}
	
	/**
	 * Delete addition related to resume
	 * @param int $iId - the id of the resume need to be deleted
	 * @return true 
	 */
	public function delete($iId)
	{
		$this->database()->delete($this->_sTable, 'resume_id = ' . (int) $iId);
		return true;
	}
}

?>