<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Contest_Service_Category_Process extends Phpfox_Service 
{
	/**
	 * Class constructor
	 */
	private $_iStringLengthCategoryName;

	public function __construct()
	{	
		$this->_sTable = Phpfox::getT('contest_category');
		$this->_iStringLengthCategoryName = 40;
        $this->_aLanguages = Phpfox::getService('language')->getAll();

    }

	public function updateOrder($aVals)
	{
		foreach ($aVals as $iId => $iOrder)
		{
			$this->database()->update($this->_sTable, array('ordering' => $iOrder), 'category_id = ' . (int) $iId);
		}
		
		$this->cache()->remove('contest', 'substr');
		
		return true;
	}

	public function delete($iId)
	{
		$aCategory = Phpfox::getService('contest.category')->getForEdit($iId);

		$aSubCategories = $this->database()->select('category_id')
			->from($this->_sTable)
			->where('parent_id = ' . (int) $iId)
			->execute('getRows');

		if (!empty($aSubCategories))
		{
			$aSubCategoryIds = array();

			foreach ($aSubCategories as $aItem)
			{
				$aSubCategoryIds[] = array_shift($aItem);
			}

			$sSubCategories = implode(',', $aSubCategoryIds);

			$this->database()->update($this->_sTable, array('parent_id' => $aCategory['parent_id']), 'category_id IN ('.$sSubCategories.')');
		}
		if (isset($aCategory['name']) && \Core\Lib::phrase()->isPhrase($aCategory['name'])){
			Phpfox::getService('language.phrase.process')->delete($aCategory['name'], true);
		}
		$this->database()->delete($this->_sTable, 'category_id = ' . (int) $iId);
		$this->database()->delete(Phpfox::getT('contest_category_data'), 'category_id = ' . (int) $iId);
		$this->cache()->remove('contest_category', 'substr');
		return true;
	}

	public function deleteAllContestsBelongToCategory($sId)
	{
		$aItems = $this->database()->select('d.contest_id')
				->from(Phpfox::getT('contest_category_data'), 'd')
				->where("d.category_id IN(" . $sId . ")")			
				->execute('getSlaveRows');
		if(!empty($aItems))
		{
			foreach($aItems as $aItem)
			{
				Phpfox::getService('fundraising.process')->delete($aItem['campaign_id']);
			}
		}
	}

    protected function addPhrase($aVals, $sName = 'name', $bVerify = true)
    {
        $langId =  current($this->_aLanguages)['language_id'];
        $aFirstLang = end($this->_aLanguages);

        //Add phrases
        $aText = [];
        //Verify name

        foreach ($this->_aLanguages as $aLanguage) {
            if (isset($aVals[$sName . '_' . $aLanguage['language_id']]) && !empty($aVals[$sName . '_' . $aLanguage['language_id']])) {
                $aText[$aLanguage['language_id']] = $aVals[$sName . '_' . $aLanguage['language_id']];
            }elseif(isset($aVals[$sName . '_' . $langId]) && !empty($aVals[$sName . '_' . $langId])){
                $aText[$aLanguage['language_id']] = $aVals[$sName . '_' . $langId];
            } elseif ($bVerify) {
                return Phpfox_Error::set(_p('provide_a_language_name_label',
                    ['language_name' => $aLanguage['title'],'label' => $sName]));
            } else {
                $bReturnNull = true;
            }
        }
        if (isset($bReturnNull) && $bReturnNull) {
            //If we don't verify value, phrase can't be empty. Return null for this case.
            return null;
        }
        $name = $aVals[$sName . '_' . $aFirstLang['language_id']];
        $phrase_var_name = 'contest_category_' . md5($name . PHPFOX_TIME);

        $aValsPhrase = [
            'var_name' => $phrase_var_name,
            'text' => $aText
        ];

        $finalPhrase = Phpfox::getService('language.phrase.process')->add($aValsPhrase);
        return $finalPhrase;
    }

	public function add($aVals, $sName = 'name')
	{
        $oParseInput = Phpfox::getLib('parse.input');
        $finalPhrase = $this->addPhrase($aVals, $sName);
        $iCategoryId = $this->database()->insert($this->_sTable, [
            'parent_id' => (!empty($aVals['parent_id']) ? (int) $aVals['parent_id'] : 0),
            'is_active' => 1,
            'name' => $finalPhrase,
            'name_url' => $oParseInput->cleanTitle($finalPhrase),
            'time_stamp' => PHPFOX_TIME
        ]);

        $this->cache()->removeGroup( 'contest_category');
        return $iCategoryId;

//        $oParseInput = Phpfox::getLib('parse.input');
//        $aLanguages = Phpfox::getService('language')->getAll();
//		$name = $aVals['name_'.$aLanguages[0]['language_id']];
//		$phrase_var_name = 'contest_category_' . md5('Contest/Groups Category'. $name . PHPFOX_TIME);
//
//		$iLimit = 40;
//
//		//Add phrases
//		$aText = [];
//		foreach ($aLanguages as $aLanguage){
//			if (isset($aVals['name_' . $aLanguage['language_id']]) && !empty($aVals['name_' . $aLanguage['language_id']])){
//				Phpfox::getService('ban')->checkAutomaticBan($aVals['name_' . $aLanguage['language_id']]);
//				$aText[$aLanguage['language_id']] = $aVals['name_' . $aLanguage['language_id']];
//			}
//			else {
//				return Phpfox_Error::set((_p('Provide a "{{ language_name }}" name.', ['language_name' => $aLanguage['title']])));
//			}
//
//			if (strlen($aVals['name_' . $aLanguage['language_id']]) > $this->_iStringLengthCategoryName) {
//				return Phpfox_Error::set(_p('Category "{{ language_name }}" name must be less than {{ limit }}', ['limit' => $iLimit, 'language_name' => $aLanguage['title']]));
//			}
//		}
//
//		$aValsPhrase = [
//			'var_name' => $phrase_var_name,
//			'text' => $aText
//		];
//
//		$finalPhrase = Phpfox::getService('language.phrase.process')->add($aValsPhrase);
//
//		$iId = $this->database()->insert($this->_sTable, array(
//										   'parent_id' => (!empty($aVals['parent_id']) ? (int)$aVals['parent_id'] : 0),
//										   'is_active' => 1,
//										   'name' => $oParseInput->clean($finalPhrase),
//										   'time_stamp' => PHPFOX_TIME
//													   )
//		);
//
//		$this->cache()->remove('contest_category', 'substr');

//		return $iId;
	}

	public function update($iId, $aVals)
	{
        $oParseInput = Phpfox::getLib('parse.input');
        $aLanguages = Phpfox::getService('language')->getAll();
		if (\Core\Lib::phrase()->isPhrase($aVals['name'])){
			$finalPhrase = $aVals['name'];
			foreach ($aLanguages as $aLanguage){
				if (isset($aVals['name_' . $aLanguage['language_id']]) && !empty($aVals['name_' . $aLanguage['language_id']])){
					Phpfox::getService('ban')->checkAutomaticBan($aVals['name_' . $aLanguage['language_id']]);
					if (strlen($aVals['name_' . $aLanguage['language_id']]) > $this->_iStringLengthCategoryName) {
						return Phpfox_Error::set(_p('Category "{{ language_name }}" name must be less than {{ limit }}', ['limit' => $this->_iStringLengthCategoryName, 'language_name' => $aLanguage['title']]));
					}
					$name = $aVals['name_' . $aLanguage['language_id']];
					Phpfox::getService('language.phrase.process')->updateVarName($aLanguage['language_id'], $aVals['name'], $name);
				}
				else {
					return Phpfox_Error::set((_p('Provide a "{{ language_name }}" name.', ['language_name' => $aLanguage['title']])));
				}
			}
		}
		else {
			$name = $aVals['name_' . $aLanguages[0]['language_id']];
			$phrase_var_name = 'contest_category_' . md5('Contest/Groups Category' . $name . PHPFOX_TIME);
			//Add phrases
			$aText = [];
			foreach ($aLanguages as $aLanguage) {
				if (isset($aVals['name_' . $aLanguage['language_id']]) && !empty($aVals['name_' . $aLanguage['language_id']])) {
					Phpfox::getService('ban')->checkAutomaticBan($aVals['name_' . $aLanguage['language_id']]);
					$aText[$aLanguage['language_id']] = $aVals['name_' . $aLanguage['language_id']];
				} else {
					return Phpfox_Error::set((_p('Provide a "{{ language_name }}" name.', ['language_name' => $aLanguage['title']])));
				}

				if (strlen($aVals['name_' . $aLanguage['language_id']]) > $this->_iStringLengthCategoryName) {
					return Phpfox_Error::set(_p('Category "{{ language_name }}" name must be less than {{ limit }}', ['limit' => $this->_iStringLengthCategoryName, 'language_name' => $aLanguage['title']]));
				}
			}

			$aValsPhrase = [
				'var_name' => $phrase_var_name,
				'text' => $aText
			];

			$finalPhrase = Phpfox::getService('language.phrase.process')->add($aValsPhrase);

		}
		if ($iId == $aVals['parent_id']) {
			return Phpfox_Error::set(_p('parent_category_and_child_is_the_same'));
		}
		$this->database()->update($this->_sTable, array('name' => $oParseInput->clean($finalPhrase), 'parent_id' => (int)$aVals['parent_id']), 'category_id = ' . (int)$iId);

		$this->cache()->remove('contest_category', 'substr');

		return true;
	}
	
	public function deleteMultiple($aIds)
	{
		foreach ($aIds as $iId)
		{
			$this->database()->delete($this->_sTable, 'category_id = ' . (int) $iId);
			$this->deletecontestBelongToCategory($iId);
			$this->database()->delete(Phpfox::getT('contest_category_data'), 'category_id = ' . (int) $iId);
		}
		return true;
	}	
	
	public function deletecontestBelongToCategory($sId)
	{
		$aItems = $this->database()->select('d.campaign_id')
				->from(Phpfox::getT('contest_category_data'), 'd')
				->where("d.category_id IN(" . $sId . ")")			
				->execute('getSlaveRows');
		if(!empty($aItems))
		{
			foreach($aItems as $aItem)
			{
				Phpfox::getService('contest.process')->delete($aItem['campaign_id']);
			}
		}
	}

	public function updateActivity($iId, $iType, $iSub)
	{
		Phpfox::isAdmin(true);
		$this->database()->update(($this->_sTable), array('is_active' => (int) ($iType == '1' ? 1 : 0)), 'category_id' . ' = ' . (int) $iId);

		$this->cache()->remove('contest_category', 'substr');
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
		if ($sPlugin = Phpfox_Plugin::get('contest.service_category_process__call'))
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