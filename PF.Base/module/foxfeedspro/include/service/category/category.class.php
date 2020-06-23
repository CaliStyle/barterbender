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
class FoxFeedsPro_Service_Category_Category extends Phpfox_Service
{
	private $_sOutput = '';
	
	private $_iCnt = 0;
	
	private $_sDisplay = 'select';
	
	protected $_module = 'foxfeedspro';              //name of module
    
    protected $_col_name = 'name';              //name of column 'category name'
    
    protected $_col_id = 'category_id';         //name of column 'category id'
    
    protected $_col_parent_id = 'parent_id';    //name of column 'parent category id'
    
    protected $_cache_key = 'newsfeed_cat';
	/**
	 * Class constructor
	 */	
	public function __construct()
	{	
		$this->_sTable = Phpfox::getT('ynnews_categories');
	}

	/** 
	 * Set display mode for category data gotten
	 * @param string $sDisplay - the input display mode
	 * @return string Service_Category
	 */
	public function display($sDisplay)
	{
		$this->_sDisplay = $sDisplay;
		
		return $this;
	}

	public function getChildIds($iId)
	{
		return rtrim($this->_getChildIds($iId), ',');
	}

	private function _getChildIds($iParentId, $bUseId = true)
	{
		$aCategories = $this->database()->select('pc.name, pc.category_id')
			->from($this->_sTable, 'pc')
			->where(($bUseId ? 'pc.parent_id = ' . (int) $iParentId . '' : 'pc.name_url = \'' . $this->database()->escape($iParentId) . '\''))
			->execute('getRows');
		$sCategories = '';
		foreach ($aCategories as $aCategory)
		{
			$sCategories .= $aCategory['category_id'] . ',' . $this->_getChildIds($aCategory['category_id']) . '';
		}
		
		return $sCategories;		
	}

	public function getMyCategories($iSkipId = 0)
	{
		//$sCacheId = $this->cache()->set('foxfeedspro_category_display_my_' . $this->_sDisplay);
		
		if ($this->_sDisplay == 'admincp')
		{
			//if (!($sOutput = $this->cache()->get($sCacheId)))
			{				
				$sOutput = $this->_getMyCategories(0, 1, phpfox::getUserId(),$iSkipId);
				
				//$this->cache()->save($sCacheId, $sOutput);
			}

			return $sOutput;
		}
		else 
		{
			//if (!($this->_sOutput = $this->cache()->get($sCacheId)))
			{				
				$this->_getMyCategories(0, 1, phpfox::getUSerId(),$iSkipId);
				
				//$this->cache()->save($sCacheId, $this->_sOutput);
			}
			
			return $this->_sOutput;
		}		
	}

	private function _getMyCategories($iParentId, $iActive = null, $iUserId = 0,$iSkipId = 0)
	{
		$iUserId = phpfox::getUserId();
		$aCategories = $this->database()->select('name, category_id, parent_id, ordering, name_url')
			->from($this->_sTable)
			->where('parent_id = ' . (int) $iParentId . ' AND is_active = ' . (int) $iActive . ' AND user_id = '.$iUserId.' AND category_id !='.$iSkipId)
			->order('ordering ASC')
			->execute('getRows');
		if (count($aCategories))
		{
			$aCache = array();
			
			if ($iParentId != 0)
			{
				$this->_iCnt++;	
			}
			
			if ($this->_sDisplay == 'option')
			{
				
			}
			elseif ($this->_sDisplay == 'admincp')
			{
				$sOutput = '<ul>';
			}
			else 
			{
				$this->_sOutput .= '<div class="js_mp_parent_holder" id="js_mp_holder_' . $iParentId . '" ' . ($iParentId > 0 ? ' style="display:none; padding:5px 0px 0px 0px;"' : '') . '>';
				$this->_sOutput .= '<select class="form-control" name="val[category][]" class="js_mp_category_list" id="js_mp_id_' . $iParentId . '">' . "\n";
				$this->_sOutput .= '<option value="">' . ($iParentId === 0 ? _p('foxfeedspro.select') : _p('foxfeedspro.select_a_sub_category')) . ':</option>' . "\n";
			}
			
			foreach ($aCategories as $iKey => $aCategory)
			{
				$aCache[] = $aCategory['category_id'];
				
				if ($this->_sDisplay == 'option')
				{
					$this->_sOutput .= '<option value="' . $aCategory['category_id'] . '" id="js_mp_category_item_' . $aCategory['category_id'] . '">' . ($this->_iCnt > 0 ? str_repeat('&nbsp;', ($this->_iCnt * 2)) . ' ' : '') . $aCategory['name'] . '</option>' . "\n";
					$this->_sOutput .= $this->_getMyCategories($aCategory['category_id'], $iActive, $iUserId,$iSkipId);
				}
				elseif ($this->_sDisplay == 'admincp')
				{
					$sOutput .= '<li><img src="' . Phpfox::getLib('template')->getStyle('image', 'misc/draggable.png') . '" alt="" /> <input type="hidden" name="order[' . $aCategory['category_id'] . ']" value="' . $aCategory['category_order'] . '" class="js_mp_order" /><a href="#?id=' . $aCategory['category_id'] . '" class="js_drop_down">' . $aCategory['name'] . '</a>' . $this->_getMyCategories($aCategory['category_id'], $iActive) . '</li>' . "\n";
				}
				else 
				{				
					$this->_sOutput .= '<option value="' . $aCategory['category_id'] . '" id="js_mp_category_item_' . $aCategory['category_id'] . '">' . $aCategory['name'] . '</option>' . "\n";
				}
			}
			
			if ($this->_sDisplay == 'option')
			{
				
			}
			elseif ($this->_sDisplay == 'admincp')
			{
				$sOutput .= '</ul>';

				return $sOutput;
			}
			else 
			{			
				$this->_sOutput .= '</select>' . "\n";
				$this->_sOutput .= '</div>';
				
				foreach ($aCache as $iCateoryId)
				{
					$this->_getMyCategories($iCateoryId, $iActive, $iUserId);
				}
			}
			
			$this->_iCnt = 0;
		}		
	}


	/**
	 * Get category for edit
	 * @param int $iId the id of the category need to be edited.
	 * @return array of category row Data
	 */
	public function getForEdit($iId)
	{
		$aRow = $this->database()->select('*')->from($this->_sTable)->where('category_id = '.(int)$iId)->execute('getRow');

		if (!isset($aRow['category_id']))
		{
			return false;
		}

		//Support legacy phrases
		if (substr($aRow['name'], 0, 7) == '{phrase' && substr($aRow['name'], -1) == '}') {
			$aRow['name'] = preg_replace('/\s+/', ' ', $aRow['name']);
			$aRow['name'] = str_replace([
											"{phrase var='",
											"{phrase var=\"",
											"'}",
											"\"}"
										], "", $aRow['title']);
		}//End support legacy
		$aLanguages = Phpfox::getService('language')->getAll();
		foreach ($aLanguages as $aLanguage){
			$sPhraseValue = (Core\Lib::phrase()->isPhrase($aRow['name'])) ? _p($aRow['name'], [], $aLanguage['language_id']) : $aRow['name'];
			$aRow['name_' . $aLanguage['language_id']] = $sPhraseValue;
		}
		return $aRow;
	}
	
	/**
	 * Get total item count from query
	 * @param array $aConds is input filter conditions
	 * @return number of item gotten
	 */
	public function getItemCount($aConds)
	{
		// Generate query object	
		$oQuery = $this -> database()
						-> select('count(*)')
						-> from($this->_sTable);
		
		// Filfer conditions
		if($aConds)
		{
			$oQuery-> where($aConds);
		}						
		return $oQuery->execute('getSlaveField');
	}
	
	/**
	 * Get category item by id
	 * @param int $iId is the id of the category need to get information
	 * @return array|bool of category data | false if no data gotten
	 */
	public function getCategory($iId)
	{
		
		$aCategory = $this->database()->select('*')
			->from($this->_sTable)
			->where('category_id = ' . (int) $iId)
			->execute('getSlaveRow');
			
		return (isset($aCategory['category_id']) ? $aCategory : false);
	}		
	
	/**
	 * Get category options 
	 */
	public function get($iSkipId = 0)
	{
		$sCacheId = $this->cache()->set('foxfeedspro_category_display_' . $this->_sDisplay);
		
		if ($this->_sDisplay == 'admincp')
		{
			if (!($sOutput = $this->cache()->get($sCacheId)))
			{				
				$sOutput = $this->_get(0, 1, $iSkipId);
				
				$this->cache()->save($sCacheId, $sOutput);
			}
			
			return $sOutput;
		}
		else 
		{
			if (!($this->_sOutput = $this->cache()->get($sCacheId)))
			{				
				$this->_get(0, 1, $iSkipId);
				
				$this->cache()->save($sCacheId, $this->_sOutput);
			}
			
			return $this->_sOutput;
		}		
	}
	
	/**
	 * @TODO: implement cache engine
	 * Get all categories that currently have on the site
	 * @return array list of categories
	 */
	public function getCategories()
	{		
		$aItems = $this -> database()
						-> select('*')
						-> from($this->_sTable)
						-> group('category_id')
						-> order('ordering ASC, name ASC')
						-> execute('getSlaveRows');			
		return $aItems;
	}
	
	/**
	 * Get category list of a feed id
	 * @param <int> $iFeedId is the id of the selected feed
	 * @return <array< $aCatList is the list of category id
	 */
	public function getFeedCategoryList($iFeedId = 0)
	{
		// Get category list array
		$aCategoryData =  $this -> database()
								-> select('category_id') 
								-> from(Phpfox::getT('ynnews_category_data')) 
								-> where("feed_id = {$iFeedId}")
							    -> execute('getRows');
		
		$aCatList = array();

		// Generate category id array
		if($aCategoryData)
		{
			foreach($aCategoryData as $aCat)
			{
				$aCatList[] = $aCat['category_id'];
			}
		}
		
		return $aCatList;
	}
	
	public function getRelatedFeedIdIntoString($iCatId)
	{
		$aCatData = $this -> database()
						  -> select('feed_id')
						  -> from(Phpfox::getT('ynnews_category_data'))
						  -> where("category_id = {$iCatId}")
						  -> group('feed_id')
						  -> execute('getRows');
				
		$aFeedIds = array(0);
		if($aCatData)
		{
			foreach($aCatData as $aData)
			{
				$aFeedIds[] = $aData['feed_id'];
			}
		}
		
		$sFeedIds = implode(',', $aFeedIds);
		
		return $sFeedIds;
	}
	/**
     * Get all items in database and build to array
     * @param   string $lang    : language_id (if designed in database), 
     * @return  array 
     */
    public function getItems($lang = null) {
        $aItems = array();
        
        $this->_getItems($aItems, 0, $lang);
        
        return $aItems;
    }
	 
	protected function _getItems(&$aItems, $pId, $lang = null) {
        $where = $this->_col_parent_id.'='.$pId;
        
        if($lang !== null) {
            $where .= ' AND user_id = 0 AND language_id=\''.$lang.'\'';
        }
        
        $aTemps = $this->database()->select('*')
            ->from($this->_sTable)
            ->where($where)
            ->order('ordering ASC')
            ->execute('getSlaveRows');
        
        if(count($aTemps)) {
            foreach($aTemps as $k=>$aTemp) {
                $aItems[$k] = $aTemp;
                $this->_getItems($aItems[$k]['items'], $aItems[$k][$this->_col_id], $lang);
            }
        }
    }
	
	/**
     * Build Html menu from array items
     * @param   array $css      : array(id, name, class) of ul
     * @param   string $lang    : language_id (if designed in database), 
     * @return  string
     */
    public function getMenu($iCurrent = 0, $sMode ='', $css = null, $lang = null) 
    {
        $aItems = $this->getItems($lang); //not cache items
        
        # build to Html
        $sHtml = '<div class="sub_section_menu">';
        $this->_renderItem($aItems, $iCurrent, $sMode, $sHtml, $css);
        $sHtml .= '</div>';
        
        return $sHtml;
    }
	
	protected function _renderItem($aItems, $iCurrent, $sMode, &$sHtml, $css = null) 
	{
        $id = (is_array($css) && isset($css['id'])) ? $css['id'] : '';
        $name = (is_array($css) && isset($css['name'])) ? $css['name'] : '';
        $class = (is_array($css) && isset($css['class'])) ? $css['class'] : '';
        
        $aData = $this->database()->select('*')->from($this->_sTable)->execute('getSlaveRows');
        
        $sHtml .= '<ul id="'.$id.'" name="'.$name.'" class="'.$class.'">';
        
        foreach($aItems as $aItem) {
            $name_url = Phpfox::getLib('parse.input')->cleanTitle(Phpfox::getLib('locale')->convert($aItem[$this->_col_name]));
            #url: change it for your module
            $aUrlParam = array();
            if(!$sMode)
			{
            	$aUrlParam = array($aItem[$this->_col_id], $name_url);
            }
			else
			{
				$aUrlParam = array($aItem[$this->_col_id], $name_url, 'view_'.$sMode);
			}
			
			$url = Phpfox::getLib('url')->makeUrl($this->_module.'.category', $aUrlParam);
			
            if($iCurrent!=0)
            {
                if($aItem[$this->_col_id]==$iCurrent)
                {
                    $sHtml .= '<li class="active">';
                }
                elseif($this->is_parent($aData, $aItem, $iCurrent) || $aItem[$this->_col_parent_id]==0 || $aItem[$this->_col_parent_id]==$iCurrent)
                {
                    $sHtml .= '<li>';
                }
                else
                {
                    continue;
                }
            }
            else
            {
                if($aItem[$this->_col_parent_id]==0)
                {
                    $sHtml .= '<li>';
                }
                else
                {
                    continue;
                }
            }
           
            $sHtml .= '<a style="border-bottom:1px #DFDFDF solid;" href="'.$url.'">'.Phpfox::getLib('locale')->convert($aItem[$this->_col_name]).'</a>';
            
            if(isset($aItem['items'])) {
                $this->_renderItem($aItem['items'], $iCurrent, $sMode, $sHtml, null);
            }
            
            $sHtml .= '</li>';
        }
        
        $sHtml .= '</ul>';
    }

	public function is_parent($aData, $aCheck, $iCurrent)
    {
        $aCurrent = array();
        
        foreach($aData as $one)
        {
            if($one[$this->_col_id]==$iCurrent)
            {
                $aCurrent = $one;
            }
        }
        
        while($aCurrent[$this->_col_parent_id] != 0)
        {
            if($aCheck[$this->_col_id]==$aCurrent[$this->_col_parent_id] || $aCheck[$this->_col_parent_id]==$aCurrent[$this->_col_parent_id])
            {
                return true;
            }
            else
            {
                foreach($aData as $one)
                {
                    if($one[$this->_col_id]==$aCurrent[$this->_col_parent_id])
                    {
                        $aCurrent = $one;
                        break;
                    }
                }
            }
        }
        return false;
    }
	
	/**
	 * Recursive method that used to generate the multi-level category list
	 * @param int $iParentId the parent category id
	 * @param int $iActive the category mode
	 * @param int $iSkipId the category that will be skipped
	 * @return string layout of category list 
	 */
	private function _get($iParentId, $iActive = null, $iSkipId)
	{
		$aCategories = $this -> database() 
							 -> select('*')
							 -> from($this->_sTable)
							 -> where('parent_id = ' . (int) $iParentId . '  AND is_active = ' . (int) $iActive . ' AND user_id = 0 AND category_id <> ' . (int) $iSkipId )
							 -> order('ordering ASC')
							 -> execute('getRows');
			
		if (count($aCategories))
		{
			$aCache = array();
			
			if ($iParentId != 0)
			{
				$this->_iCnt++;	
			}
			
			if ($this->_sDisplay == 'option')
			{
				
			}
			elseif ($this->_sDisplay == 'admincp')
			{
				$sOutput = '<ul>';
			}
			else 
			{
				$this->_sOutput .= '<div class="js_mp_parent_holder" id="js_mp_holder_' . $iParentId . '" ' . ($iParentId > 0 ? ' style="display:none; padding:5px 0px 0px 0px;"' : '') . '>';
				$this->_sOutput .= '<select class="form-control" name="val[category][]" class="js_mp_category_list" id="js_mp_id_' . $iParentId . '">' . "\n";
				$this->_sOutput .= '<option value="">' . ($iParentId === 0 ? _p('foxfeedspro.select') : _p('foxfeedspro.select_a_sub_category')) . ':</option>' . "\n";
			}
			
			foreach ($aCategories as $iKey => $aCategory)
			{
				$aCache[] = $aCategory['category_id'];
				
				if ($this->_sDisplay == 'option')
				{
					$this->_sOutput .= '<option value="' . $aCategory['category_id'] . '" id="js_mp_category_item_' . $aCategory['category_id'] . '">' . ($this->_iCnt > 0 ? str_repeat('&nbsp;', ($this->_iCnt * 2)) . ' ' : '') . Phpfox::getLib('locale')->convert(Phpfox::isPhrase($aCategory['name']) ? _p($aCategory['name']) : $aCategory['name']) . '</option>' . "\n";
					$this->_sOutput .= $this->_get($aCategory['category_id'], $iActive, $iSkipId);					
				}
				elseif ($this->_sDisplay == 'admincp')
				{
					$sOutput .= '<li><img src="' . Phpfox::getLib('template')->getStyle('image', 'misc/draggable.png') . '" alt="" /> <input type="hidden" name="order[' . $aCategory['category_id'] . ']" value="' . $aCategory['ordering'] . '" class="js_mp_order" /><a href="#?id=' . $aCategory['category_id'] . '" class="js_drop_down">' . Phpfox::getLib('locale')->convert(Phpfox::isPhrase($aCategory['name']) ? _p($aCategory['name']) : $aCategory['name']) . ' (' .$aCategory['used']. ')' . '</a>' . $this->_get($aCategory['category_id'], $iActive, $iSkipId) . '</li>' . "\n";
				}
				else 
				{				
					$this->_sOutput .= '<option value="' . $aCategory['category_id'] . '" id="js_mp_category_item_' . $aCategory['category_id'] . '">' . Phpfox::getLib('locale')->convert(Phpfox::isPhrase($aCategory['name']) ? _p($aCategory['name']) : $aCategory['name']) . '</option>' . "\n";
				}
			}
			
			if ($this->_sDisplay == 'option')
			{
				
			}
			elseif ($this->_sDisplay == 'admincp')
			{
				$sOutput .= '</ul>';
				
				return $sOutput;
			}
			else 
			{			
				$this->_sOutput .= '</select>' . "\n";
				$this->_sOutput .= '</div>';
				
				foreach ($aCache as $iCateoryId)
				{
					$this->_get($iCateoryId, $iActive, $iSkipId);
				}
			}
			
			$this->_iCnt = 0;
		}		
	}

	public function getForBrowse($iCategoryId = null)
	{		
		$sCacheId = $this->cache()->set('foxfeedspro_category_browse' . ($iCategoryId === null ? '' : '_' . $iCategoryId));
	 	if (!($aCategories = $this->cache()->get($sCacheId)))
		{					
			$aCategories = $this->database()->select('mc.category_id, mc.name')
				->from($this->_sTable, 'mc')
				->where('mc.parent_id = ' . ($iCategoryId === null ? '0' : (int) $iCategoryId) . ' AND mc.is_active = 1 AND user_id = 0 ')
				->order('mc.ordering ASC')
				->execute('getRows');
			
			foreach ($aCategories as $iKey => $aCategory)
			{
				$aCategories[$iKey]['url'] = Phpfox::permalink('foxfeedspro.category', $aCategory['category_id'], Phpfox::isPhrase($aCategory['name']) ? _p($aCategory['name']) : $aCategory['name']);
				
				//if ($sCategory === null)
				{
					$aCategories[$iKey]['sub'] = $this->database()->select('mc.category_id, mc.name')
						->from($this->_sTable, 'mc')
						->where('mc.parent_id = ' . $aCategory['category_id'] . ' AND mc.is_active = 1')
						->order('mc.ordering ASC')
						->execute('getRows');			
						
					foreach ($aCategories[$iKey]['sub'] as $iSubKey => $aSubCategory)
					{
						$aCategories[$iKey]['sub'][$iSubKey]['url'] = Phpfox::permalink('foxfeedspro.category', $aSubCategory['category_id'], Phpfox::isPhrase($aSubCategory['name']) ? _p($aSubCategory['name']) : $aSubCategory['name']);
					}
				}

            }
			
			$this->cache()->save($sCacheId, $aCategories);
		}
		
		return $aCategories;
	}
    public function getParentBreadcrumb($sCategory)
    {
		$sCategories = $this->getParentCategories($sCategory);

		$aCategories = $this->database()->select('*')
			->from($this->_sTable)
			->where('category_id IN(' . $sCategories . ')')
			->execute('getRows');

		$aBreadcrumb = $this->getCategoriesById(null, $aCategories);

        return $aBreadcrumb;
    }
    public function getParentCategories($sCategory)
    {
		$iCategory = $this->database()->select('category_id')
			->from($this->_sTable)
			->where('category_id = \'' . (int) $sCategory . '\'')
			->execute('getField');

		$sCategories = $this->_getParentIds($iCategory);

		$sCategories = rtrim($sCategories, ',');

        return $sCategories;
    }
    public function getCategoriesById($iId = null, &$aCategories = null)
    {
        $oUrl = Phpfox::getLib('url');

        if ($aCategories === null)
        {
            $aCategories = $this->database()->select('pc.parent_id, pc.category_id, pc.name')
                ->from(Phpfox::getT('ynnews_category_data'), 'pcd')
                ->join($this->_sTable, 'pc', 'pc.category_id = pcd.category_id')
                ->where('pcd.feed_id = ' . (int) $iId)
                ->order('pc.parent_id ASC, pc.ordering ASC')
                ->execute('getSlaveRows');
        }

        if (!count($aCategories))
        {
            return null;
        }

        $aBreadcrumb = array();
        if (count($aCategories) > 1)
        {
            foreach ($aCategories as $aCategory)
            {
                $aBreadcrumb[] = array(Phpfox::getLib('locale')->convert(Phpfox::getLib('parse.input')->clean(Phpfox::isPhrase($aCategory['name'])) ? Phpfox::getLib('parse.input')->clean(_p($aCategory['name'])) : $aCategory['name']), Phpfox::permalink('news.category', $aCategory['category_id'], Phpfox::isPhrase($aCategory['name']) ? _p($aCategory['name']) : $aCategory['name']));
            }
        }
        else
        {
            $aBreadcrumb[] = array(Phpfox::getLib('locale')->convert(Phpfox::getLib('parse.input')->clean(Phpfox::isPhrase($aCategories[0]['name'])) ? Phpfox::getLib('parse.input')->clean(_p($aCategories[0]['name'])) : $aCategories[0]['name'] ), Phpfox::permalink('news.category', $aCategories[0]['category_id'], $aCategories[0]['name']));
        }

        return $aBreadcrumb;
    }
    private function _getParentIds($iId)
    {
        $aCategories = $this->database()->select('pc.category_id, pc.parent_id')
            ->from($this->_sTable, 'pc')
            ->where('pc.category_id = ' . (int) $iId)
            ->execute('getRows');

        $sCategories = '';
        foreach ($aCategories as $aCategory)
        {
            $sCategories .= $aCategory['category_id'] . ',' . $this->_getParentIds($aCategory['parent_id']) . '';
        }

        return $sCategories;
    }
	public function getForAdmin($iParentId = 0, $bGetSub = 1)
	{
		$aRows = $this->database()->select('*')
			->from($this->_sTable)
			->where('parent_id = ' . (int) $iParentId)
			->order('ordering ASC')
			->execute('getSlaveRows');

		foreach ($aRows as $iKey => $aRow){
			if ($bGetSub) {
				$aRows[$iKey]['numberItems'] = $this->getAllItemBelongToCategory($aRow['category_id']);
				$aRows[$iKey]['categories'] = $this->getForAdmin($aRow['category_id']);
                $aRows[$iKey]['name'] = Phpfox::getLib('parse.input')->clean(Phpfox_Locale::instance()->convert($aRow['name']));
            }
		}

		return $aRows;
	}
	public function getAllItemBelongToCategory($iCategoryId)
	{
		return $this->database()->select('COUNT(ccd.feed_id)')
			->from(Phpfox::getT('ynnews_category_data'), 'ccd')
			->where('ccd.category_id = '.$iCategoryId)
			->execute('getSlaveField');

	}
}

?>