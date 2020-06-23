<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 *
 * @copyright      YouNet Company
 * @author         TienNPL
 * @package        Module_FoxFeedsPro
 * @version        3.02
 * 
 */
class FoxFeedsPro_Service_Category_Process extends Phpfox_Service
{
	/**
	 * Class constructor
	 */
	private $_iStringLengthCategoryName;
	public function __construct()
	{	
		$this->_sTable = Phpfox::getT('ynnews_categories');
		$this->_iStringLengthCategoryName = 40;
        $this->_aLanguages = Phpfox::getService('language')->getAll();

    }
	
	/**
	 * Add new category into database
	 * @param array $aVals - array of category input information 
	 * @return integer $iId - the id of the  inserted category 
	 */
//	public function add($aVals)
//	{
//		$aLanguages = Phpfox::getService('language')->getAll();
//		$name = $aVals['name_'.$aLanguages[0]['language_id']];
//		$phrase_var_name = 'foxfeedspro_category_' . md5('Foxfeedspro/Groups Category'. $name . PHPFOX_TIME);
//		$iUserId = 0;
//		if(!Phpfox::isAdminPanel()) {
//			$iUserId = phpfox::getUserId();
//		}
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
//									   	'parent_id' => (!empty($aVals['parent_id']) ? (int)$aVals['parent_id'] : 0),
//									   	'is_active' => 1,
//									   	'name' => $finalPhrase,
//									   	'time_stamp' => PHPFOX_TIME,
//										'name_url' 	 => '',
//										'time_stamp' => PHPFOX_TIME,
//										'user_id' => $iUserId
//
//													   )
//		);
//
//		$this->cache()->remove('foxfeedspro', 'substr');
//
//		return $iId;
//	}

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
        $phrase_var_name = 'foxfeedspro_category_' . md5($name . PHPFOX_TIME);

        $aValsPhrase = [
            'var_name' => $phrase_var_name,
            'text' => $aText
        ];

        $finalPhrase = Phpfox::getService('language.phrase.process')->add($aValsPhrase);
        return $finalPhrase;
    }

    public function add($aVals, $sName = 'name') {
        $iUserId = 0;
		if(!Phpfox::isAdminPanel()) {
			$iUserId = phpfox::getUserId();
		}
        $finalPhrase = $this->addPhrase($aVals, $sName);
        $iCategoryId = $this->database()->insert($this->_sTable, [
            'parent_id' => (!empty($aVals['parent_id']) ? (int) $aVals['parent_id'] : 0),
            'is_active' => 1,
            'name' => $finalPhrase,
            'time_stamp' => PHPFOX_TIME,
            'name_url' 	 => '',
            'user_id' => $iUserId
        ]);

        $this->cache()->removeGroup('foxfeedspro_category');
        return $iCategoryId;
    }

	/**
	 * Update order of category list
	 * @param array $aVals - array of category order
	 * @return true
	 */
	public function updateOrder($aVals)
	{
		foreach ($aVals as $iId => $iOrder)
		{
			$this->database()->update($this->_sTable, array('ordering' => $iOrder), 'category_id = ' . (int) $iId);
		}
		
		$this->cache()->remove('foxfeedspro', 'substr');
		
		return true;
	}
	
	/** 
	 * Update category data
	 */
	public function update($iId, $aVals)
	{
		$aLanguages = Phpfox::getService('language')->getAll();
		if (Phpfox::isPhrase($aVals['name'])){
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
			$phrase_var_name = 'foxfeedspro_category_' . md5('Foxfeedspro/Groups Category' . $name . PHPFOX_TIME);
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

		if (isset($aVals['parent_id']) && $iId == $aVals['parent_id']) {
			return Phpfox_Error::set(_p('parent_category_and_child_is_the_same'));
		}
		$this->database()->update($this->_sTable, array('name' => $finalPhrase, 'parent_id' => (isset($aVals['parent_id'])) ? (int)$aVals['parent_id'] : 0), 'category_id = ' . (int)$iId);

		$this->cache()->remove('fevent', 'substr');

		return true;
	}
	/**
	 * Delete a category
	 * @param integer $iId - the id of the category needed to be deleted
	 * @return true
	 */
	public function delete($iId)
	{
		$aCategory = Phpfox::getService('foxfeedspro.category')->getForEdit($iId);

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
		if (isset($aCategory['name']) && Phpfox::isPhrase($aCategory['name'])){
			Phpfox::getService('language.phrase.process')->delete($aCategory['name'], true);
		}
		$this->database()->delete($this->_sTable, 'category_id = ' . (int) $iId);
		$this->database()->delete(Phpfox::getT('ynnews_category_data'), 'category_id = ' . (int) $iId);
		$this->cache()->remove('foxfeedspro', 'substr');
		return true;
	}

	public function addMyCategory($aVals)
	{
		if (empty($aVals['name'])) {
			return Phpfox_Error::set(_p('foxfeedspro.provide_a_category_name'));
		}
		$iUserId = 0;
		if (!Phpfox::isAdminPanel()) {
			$iUserId = phpfox::getUserId();
		}
		$oParseInput = Phpfox::getLib('parse.input');

		$iId = $this->database()->insert($this->_sTable, array(
			'parent_id'  => (!empty($aVals['parent_id']) ? (int) $aVals['parent_id'] : 0),
			'is_active'  => 1,
			'name' 		 => $oParseInput->clean($aVals['name'], 255),
			'name_url' 	 => $oParseInput->cleanTitle($aVals['name']),
			'time_stamp' => PHPFOX_TIME,
			'user_id' => $iUserId
		));
		$this->cache()->remove('foxfeedspro', 'substr');
		return $iId;
	}

	public function updateMyCategory($iId, $aVals)
	{
		if (empty($aVals['name'])) {
			return Phpfox_Error::set(_p('foxfeedspro.provide_a_category_name'));
		}
		$oParseInput = Phpfox::getLib('parse.input');
		$this->database()->update(
			$this->_sTable,
			array(
				'name' 		=> $oParseInput->clean($aVals['name'], 255),
				'name_url' 	=> $oParseInput->cleanTitle($aVals['name']),
				'parent_id' => isset($aVals['parent_id']) ? (int) $aVals['parent_id'] : 0
			),
			'category_id = ' . (int) $iId);

		$this->cache()->remove('foxfeedspro', 'substr');

		return true;
	}

		/**
	 * Add category id data in database
	 * @param <int> $iFeedId is the Id of the feed
	 * @param <int> $iCategoryId is the Id of the category
	 * @param <int> $iUserId is the Owner of that category (currently not used yet)
	 * @return true
	 */
	 
	public function addCategoryData($iFeedId, $iCategoryId, $iUserId = 0)
	{
		$aInsert = array(
			'feed_id'     => $iFeedId,
			'category_id' => $iCategoryId,
			'user_id'	  => $iUserId
		);
		
		$this-> database()->insert(Phpfox::getT('ynnews_category_data'), $aInsert);
		
		// Update category used counting
		$this -> database() ->updateCounter('ynnews_categories','used','category_id', $iCategoryId);
		
		$this->cache()->remove('foxfeedspro', 'substr');
		
		return true;
	}  

	/**
	 * Remove category id data in database
	 * @param <int> $iFeedId is the Id of the feed
	 * @param <int> $iCategoryId is the Id of the category
	 * @param <int> $iUserId is the Owner of that category (currently not used yet)
	 * @return true
	 */
	public function deleteCategoryData($iFeedId, $iCategoryId, $iUserId = 0)
	{
		
		$this-> database()->delete(Phpfox::getT('ynnews_category_data'), "feed_id = {$iFeedId} AND category_id = {$iCategoryId}");
		
		// Update category used counting
		$this -> database() ->updateCounter('ynnews_categories','used','category_id', $iCategoryId, TRUE);
		
		$this->cache()->remove('foxfeedspro', 'substr');
		
		return true;
	}

	public function updateActivity($iId, $iType, $iSub)
	{
		Phpfox::isAdmin(true);
		$this->database()->update(($this->_sTable), array('is_active' => (int) ($iType == '1' ? 1 : 0)), 'category_id' . ' = ' . (int) $iId);

		$this->cache()->remove('foxfeedspro', 'substr');
	}
}


?>