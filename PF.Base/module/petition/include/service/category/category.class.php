<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Petition_Service_Category_Category extends Phpfox_Service 
{
	/**
	 * Class constructor
	 */	
	public function __construct()
	{
		$this->_sTable = Phpfox::getT('petition_category');
	}
		
	public function get($aConds, $sSort = 'c.name ASC', $iPage = '', $iLimit = '')
	{		
		(($sPlugin = Phpfox_Plugin::get('petition.service_category_category_get_start')) ? eval($sPlugin) : false);
		
		$iCnt = $this->database()->select('COUNT(*)')
			->from(Phpfox::getT('petition_category'), 'c')
			->leftJoin(Phpfox::getT('user'), 'u', 'u.user_id = c.user_id')
			->where($aConds)
			->order($sSort)
			->execute('getSlaveField');	
			
		$aItems = array();
		if ($iCnt)
		{		
			$aItems = $this->database()->select('c.*, ' . Phpfox::getUserField())
				->from(Phpfox::getT('petition_category'), 'c')
				->leftJoin(Phpfox::getT('user'), 'u', 'u.user_id = c.user_id')
				->where($aConds)
				->order($sSort)
				->limit($iPage, $iLimit, $iCnt)
				->execute('getSlaveRows');
				
			foreach ($aItems as $iKey => $aItem)
			{
				$aItems[$iKey]['link'] = ($aItem['user_id'] ? Phpfox::getLib('url')->permalink($aItem['user_name'] . '.petition.category', $aItem['category_id'], (Phpfox::isPhrase($aItem['name']) ? _p($aItem['name']) : Phpfox_Locale::instance()->convert($aItem['name']))) : Phpfox::getLib('url')->permalink('petition.category', $aItem['category_id'], (Phpfox::isPhrase($aItem['name']) ? _p($aItem['name']) : Phpfox_Locale::instance()->convert($aItem['name']))));
				$aItems[$iKey]['name'] = (Phpfox::isPhrase($aItem['name']) ? _p($aItem['name']) : Phpfox_Locale::instance()->convert($aItem['name']));
			}
		}
			
		(($sPlugin = Phpfox_Plugin::get('petition.service_category_category_get_end')) ? eval($sPlugin) : false);

		return array($iCnt, $aItems);
	}
		
	public function getCategories($aConds, $sSort = 'c.name ASC')
	{		
		(($sPlugin = Phpfox_Plugin::get('petition.service_category_category_getcategories_start')) ? eval($sPlugin) : false);
		
		$aItems = $this->database()->select('c.category_id, c.name, c.name, c.user_id')
			->from(Phpfox::getT('petition_category'), 'c')
			->where($aConds)
			->group('c.category_id')
			->order($sSort)
			->execute('getSlaveRows');			
			
		foreach($aItems as &$aItem){
			$aItem['name'] = (Phpfox::isPhrase($aItem['name']) ? _p($aItem['name']) : Phpfox_Locale::instance()->convert($aItem['name']));
		}	
		
		(($sPlugin = Phpfox_Plugin::get('petition.service_category_category_getcategories_end')) ? eval($sPlugin) : false);
		
		return $aItems;
	}
	
	public function getCategory($iId)
	{
		$aCategory = $this->database()->select('*')
			->from(Phpfox::getT('petition_category'))
			->where('category_id = ' . (int) $iId)
			->execute('getSlaveRow');
			
		return (isset($aCategory['category_id']) ? $aCategory : false);
	}
	
	public function getCategoriesById($sId)
	{		
		if (!$sId)
		{
			return array();
		}		
		
		$aItems = $this->database()->select('d.petition_id, d.category_id, c.name AS category_name, c.user_id')
			->from(Phpfox::getT('petition_category_data'), 'd')
			->join(Phpfox::getT('petition_category'), 'c', 'd.category_id = c.category_id')
			->where("d.petition_id IN(" . $sId . ")")			
			->execute('getSlaveRows');
		
		$aCategories = array();
		foreach ($aItems as $aItem)
		{
			$aCategories[$aItem['petition_id']][] = $aItem;
		}

		return $aCategories;
	}	
		
	public function delete($iId)
	{
		Phpfox::isAdmin(true);
		$aRow = $this->database()->select('category_id, user_id')
			->from($this->_sTable)
			->where('category_id = ' . (int) $iId)
			->execute('getRow');
			
		if (!isset($aRow['category_id']))
		{
			return false;
		}

		$this->database()->delete($this->_sTable, 'category_id = ' . (int) $aRow['category_id']);
		Phpfox::getService('petition.category.process')->deletePetitionBelongToCategory( (int) $aRow['category_id']);
		$this->database()->delete(Phpfox::getT('petition_category_data'), 'category_id = ' . (int) $aRow['category_id']);		
		
		return true;
	}		    
			
	public function addCategoryForPetition($iPetitionId, $aCategories, $bUpdateUsageCount = true)
	{
		if (count($aCategories))
		{
			$aCache = array();
			foreach ($aCategories as $iKey => $iId)
			{
				if (!is_numeric($iId))
				{
					continue;
				}
				
				if (isset($aCache[$iId]))
				{
					continue;
				}
				
				$aCache[$iId] = true;
				
				$this->database()->insert(Phpfox::getT('petition_category_data'), array('petition_id' => $iPetitionId, 'category_id' => $iId));				
				if ($bUpdateUsageCount === true)
				{
					$this->database()->updateCount('petition_category_data', 'category_id = ' . (int) $iId, 'used', 'petition_category', 'category_id = ' . (int) $iId);
				}
			}			
		}		
	}
	
	public function updateCategoryForPetition($iPetitionId, $aCategories, $bUpdateUsageCount)
	{
		$aRows = $this->database()->select('category_id')
			->from(Phpfox::getT('petition_category_data'))
			->where('petition_id = ' . (int) $iPetitionId)
			->execute('getRows');		
		                		    
		if (count($aRows))
		{
			foreach ($aRows as $aRow)
			{				
				$this->database()->delete(Phpfox::getT('petition_category_data'), "petition_id = " . (int) $iPetitionId . " AND category_id = " . (int) $aRow["category_id"]);
				if($bUpdateUsageCount)
				{
					$this->database()->query("
						UPDATE " . Phpfox::getT('petition_category') . "
						SET used = used - 1
						WHERE category_id = " . $aRow["category_id"] . "
					");	
				}				
			}			
		}		

		$this->addCategoryForPetition($iPetitionId, $aCategories, $bUpdateUsageCount);			
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
		if ($sPlugin = Phpfox_Plugin::get('petition.service_category_category__call'))
		{
			return eval($sPlugin);
		}
			
		/**
		 * No method or plug-in found we must throw a error.
		 */
		Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
	}


	public function getForEdit($iId)
	{
		$aRow = $this->database()->select('*')->from($this->_sTable)->where('category_id = '.(int)$iId)->execute('getRow');
		if (!isset($aRow['category_id']))
		{
			return false;
		}
		if (substr($aRow['name'], 0, 7) == '{phrase' && substr($aRow['name'], -1) == '}') {
			$aRow['name'] = preg_replace('/\s+/', ' ', $aRow['name']);
			$aRow['name'] = str_replace([
				"{phrase var='",
				"{phrase var=\"",
				"'}",
				"\"}"
			], "", $aRow['name']);
		}
		$aLanguages = Language_Service_Language::instance()->getAll();
		foreach ($aLanguages as $aLanguage){
			$sPhraseValue = (Core\Lib::phrase()->isPhrase($aRow['name'])) ? _p($aRow['name'], [], $aLanguage['language_id']) : $aRow['name'];
			$aRow['name_' . $aLanguage['language_id']] = $sPhraseValue;
		}
		return $aRow;
	}
}

?>
