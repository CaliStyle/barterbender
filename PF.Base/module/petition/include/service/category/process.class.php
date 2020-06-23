<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Petition_Service_Category_Process extends Phpfox_Service 
{
	/**
	 * Class constructor
	 */	
	public function __construct()
	{	
		$this->_sTable = Phpfox::getT('petition_category');
	}
	
	public function update($iId, $aVals)
	{
		$aLanguages = Language_Service_Language::instance()->getAll();
		if (Phpfox::isPhrase($aVals['name'])){
			$finalPhrase = $aVals['name'];
			foreach ($aLanguages as $aLanguage){
				if (isset($aVals['name_' . $aLanguage['language_id']]) && !empty($aVals['name_' . $aLanguage['language_id']])) {
					Phpfox::getService('ban')->checkAutomaticBan($aVals['name_' . $aLanguage['language_id']]);
					Language_Service_Phrase_Process::instance()->updateVarName($aLanguage['language_id'], $aVals['name'], $aVals['name_' . $aLanguage['language_id']]);
				} else {
					return Phpfox_Error::set((_p('Provide a "{{ language_name }}" name.', ['language_name' => $aLanguage['title']])));
				}
				if (strlen($aVals['name_' . $aLanguage['language_id']]) > 255) {
					return Phpfox_Error::set(_p('Category "{{ language_name }}" name must be less than {{ limit }}', ['limit' => 255, 'language_name' => $aLanguage['title']]));
				}
			}
		}
		else {
			$name = $aVals['name_' . $aLanguages[0]['language_id']];
			$phrase_var_name = 'petition_category_' . md5('Petition Category' . $name . PHPFOX_TIME);
			$aText = [];
			foreach ($aLanguages as $aLanguage) {
				if (isset($aVals['name_' . $aLanguage['language_id']]) && !empty($aVals['name_' . $aLanguage['language_id']])) {
					Phpfox::getService('ban')->checkAutomaticBan($aVals['name_' . $aLanguage['language_id']]);
					$aText[$aLanguage['language_id']] = $aVals['name_' . $aLanguage['language_id']];
				} else {
					return Phpfox_Error::set((_p('Provide a "{{ language_name }}" name.', ['language_name' => $aLanguage['title']])));
				}
				if (strlen($aVals['name_' . $aLanguage['language_id']]) > 255) {
					return Phpfox_Error::set(_p('Category "{{ language_name }}" name must be less than {{ limit }}', ['limit' => 255, 'language_name' => $aLanguage['title']]));
				}
			}
			$aValsPhrase = [
				'var_name' => $phrase_var_name,
				'text' => $aText
			];
			$finalPhrase = Language_Service_Phrase_Process::instance()->add($aValsPhrase);
		}
		$this->database()->update($this->_sTable, array('name' => $finalPhrase), 'category_id = ' . (int)$iId);
		return true;
	}
	
	public function add($aVals, $iUserId = null)
	{
		$aLanguages = Language_Service_Language::instance()->getAll();
		$name = $aVals['name_'.$aLanguages[0]['language_id']];
		$phrase_var_name = 'petition_category_' . md5('Petition Category'. $name . PHPFOX_TIME);

		$aText = [];
		foreach ($aLanguages as $aLanguage){
			if (isset($aVals['name_' . $aLanguage['language_id']]) && !empty($aVals['name_' . $aLanguage['language_id']])){
				Phpfox::getService('ban')->checkAutomaticBan($aVals['name_' . $aLanguage['language_id']]);
				$aText[$aLanguage['language_id']] = $aVals['name_' . $aLanguage['language_id']];
			}
			else {
				return Phpfox_Error::set((_p('Provide a "{{ language_name }}" name.', ['language_name' => $aLanguage['title']])));
			}
			if (strlen($aVals['name_' . $aLanguage['language_id']]) > 255) {
				return Phpfox_Error::set(_p('Category "{{ language_name }}" name must be less than {{ limit }}', ['limit' => 255, 'language_name' => $aLanguage['title']]));
			}
		}
		$aValsPhrase = [
			'var_name' => $phrase_var_name,
			'text' => $aText
		];
		$finalPhrase = Language_Service_Phrase_Process::instance()->add($aValsPhrase);
		$iId = $this->database()->insert($this->_sTable, array(
				'name' => $finalPhrase,
				'user_id' => ($iUserId === null ? Phpfox::getUserId() : $iUserId),
				'added' => PHPFOX_TIME
			)
		);

		return $iId;
	}	
	
	public function deleteMultiple($aIds)
	{
		foreach ($aIds as $iId)
		{
			//Delete phrase of category
			$aCategory = $this->database()->select('*')
				->from($this->_sTable)
				->where('category_id=' . (int) $iId)
				->execute('getSlaveRow');
			if (isset($aCategory['name']) && Phpfox::isPhrase($aCategory['name'])){
				Language_Service_Phrase_Process::instance()->delete($aCategory['name'], true);
			}
			$this->database()->delete($this->_sTable, 'category_id = ' . (int) $iId);
			$this->database()->delete(Phpfox::getT('petition_category_data'), 'category_id = ' . (int) $iId);
		}
		return true;
	}	
	
	/**
	 * If a call is made to an unknown method attempt to connect
	 * it to a specific plug-in with the same name thus allowing 
	 * plug-in developers the ability to extend classes.
	 *
	 * @param string $sMethod is the name of the method
	 * @param array $aArguments is the array of arguments of being passed
	 */
	public function __call($sMethod, $aArguments)
	{
		/**
		 * Check if such a plug-in exists and if it does call it.
		 */
		if ($sPlugin = Phpfox_Plugin::get('petition.service_category_process__call'))
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