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
class Resume_Service_Category_Category extends Phpfox_Service
{
	private $_sOutput = '';
	
	private $_iCnt = 0;
	
	private $_sDisplay = 'select'; 
	
	protected $_css  = array(
        'id'=>'resume_category_list', 
        'class'=>'resume_category_list action', 
        'style'=>''
    );
    
    protected $_sTable;
    
    protected $_module = 'resume';
    
    protected $_col_name = 'name';
	
	protected $_col_name_url = 'name_url';
    
    protected $_col_id = 'category_id';
    
    protected $_col_parent_id = 'parent_id';
    
    protected $_cache_key = 'resume_cat';
	
	/**
	 * Class constructor
	 */	
	public function __construct()
	{	
		$this->_sTable = Phpfox::getT('resume_category');
	}
	
	/**
     * get data in structure
     * @return array 
     */
    public function loadData()
    {
        # get from database
        $temp_aItems = $this->database()
            ->select('c.*')
            ->from($this->_sTable, 'c')
            ->order('ordering ASC')
            ->execute('getSlaveRows');

        // final list of items.
        $aItems = array();

        // map parent_item_id => parent_id
        foreach ($temp_aItems as $aItem)
        {
            $aItems[$aItem[$this->_col_id]] = $aItem;
        }

        // manual unset items for save memory.
        unset($temp_aItems);

        for ($i = 0; $i < 10; $i++)
        {
            $map_p2c = array();
            
            // map parent_item_id => parent_id
            foreach ($aItems as $aItem)
            {
                $map_p2c[$aItem[$this->_col_parent_id]] = 1;
            }

            $found = false;
            
            foreach ($aItems as $key => $aItem)
            {
                $id = $aItem[$this->_col_id];
                $pid = $aItem[$this->_col_parent_id];
                
                // process p1.
                if (0 == $pid)
                {
                    continue;
                }

                if (!isset($map_p2c[$id]) or $map_p2c[$pid] == 2)
                {
                    $found = true;
                    if (isset($aItems[$pid]) && is_array($aItems[$pid]))
                    {
                        $aItems[$pid]['items'][] = $aItem;
                    }
                    unset($aItems[$key]);
                }
                else
                {
                    $map_p2c[$pid] == 2;
                }
            }
            if (!$found)
            {
                break;
            }
        }

        # return result
        return $aItems;
    }


    /**
     * render items
     * @return string 
     */
    protected function renderItem($aData, $iCurrentId, $sViewMode, $level)
    {
        $aItems = array();

		if ($level == 0)
		{
			$aItems = isset($aData['aItems']) ? $aData['aItems'] : array();
		}
		else
		{
			$aItems = $aData;
		}
        
        $css = isset($aData['css']) ? $aData['css'] : null;		
        
        if (empty($aItems))
        {
            return '';
        }

        $xhtml = array();

        foreach ($aItems as $aItem)
        {
            if (!$aItem['is_active']) {
                continue;
            }
            try
            {
                $sub = '';

                if (isset($aItem['items']) && $aItem['items'])
                {
                    $sub = $this->renderItem($aItem['items'], $iCurrentId, $sViewMode, $level + 1);
                }

                $xhtml[] = $this->renderSub($aItem, $iCurrentId, $sViewMode, $sub);

            }
            catch (exception $e) {}
        }
	
        $xhtml = sprintf('<ul class="%s" id="%s" style="%s">%s</ul>', $css['class'], $css['id'], $css['style'], implode(PHP_EOL, $xhtml));
		
        return $xhtml;
    }


    /**
     * render sub items
     * @return html
     */
    protected function renderSub($aItem, $iCurrentId, $sViewMode, $sub)
    {
    	$pattern = '<li><a href="%s">%s</a>%s</li>';
    	
    	if($iCurrentId == $aItem[$this->_col_id])
		{
			 $pattern = '<li class="active"><a href="%s">%s</a>%s</li>';	
		}
       
        $url = Phpfox::getLib('url')->makeUrl($this->_module . '.category', array($aItem[$this->_col_id], $aItem[$this->_col_name_url]));
		$url = "";
		if($sViewMode)
		{
			$url = Phpfox::getLib('url')->permalink(array('resume.category', 'view' => $sViewMode ), $aItem[$this->_col_id], $aItem[$this->_col_name_url]);
		}
		else
		{
			$url = Phpfox::getLib('url')->permalink(array('resume.category'), $aItem[$this->_col_id], $aItem[$this->_col_name_url]);	
		}
		
        return sprintf($pattern, $url, Phpfox::getLib('locale')->convert(_p($aItem[$this->_col_name])), $sub);
    }
    

    /**
     * build to Html
     * @return html 
     */
    public function toHtml($iCurrentId = 0,$sViewMode = "", $css = null)
    {
        # get data from cache
        $sCacheId = $this->cache()->set($this->_cache_key);
        $aItems = $this->cache()->get($sCacheId);
        
        if (!$aItems)
        {
            $aItems = $this->loadData();
            
            # update cache
            $this->cache()->save($sCacheId, $aItems);
        }

        # include css
        if($css)
        {
            $this->_css = array_merge($this->_css, $css);
        }
        
        $aData = array(
            'css' => $this->_css,
            'aItems' => $aItems
        );
        
        return $this->renderItem($aData, $iCurrentId, $sViewMode, 0);
    }

	/** 
	 * Set display mode for category data gotten
	 * @param string $sDisplay - the input display mode
	 * @return class Service_Category
	 */
	public function display($sDisplay)
	{
		$this->_sDisplay = $sDisplay;
		
		return $this;
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
	 *  Get category item by id
	 * @param int $iId is the id of the category need to get information
	 * @return mix array of category data | false if no data gotten
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
	public function get()
	{
		$sCacheId = $this->cache()->set('resume_category_display_' . $this->_sDisplay);
		
		if ($this->_sDisplay == 'admincp')
		{
			if (!($sOutput = $this->cache()->get($sCacheId)))
			{				
				$sOutput = $this->_get(0, 1);
				
				$this->cache()->save($sCacheId, $sOutput);
			}
			
			return $sOutput;
		}
		else 
		{
			if (!($this->_sOutput = $this->cache()->get($sCacheId)))
			{				
				$this->_get(0, 1);
				
				$this->cache()->save($sCacheId, $this->_sOutput);
			}
			
			return $this->_sOutput;
		}		
	}
	
	public function getCategories()
	{		
		$aItems = $this->database()->select('c.category_id, c.name, c.name')
			->from(Phpfox::getT('resume_category'), 'c')
            ->where('c.is_active = 1')
			->group('c.category_id')
			->order('c.ordering ASC')
			->execute('getSlaveRows');			
		return $aItems;
	}
	
	public function getCategoriesData($resume_id)
	{		
		$aItems = $this->database()->select('category_id')
			->from(Phpfox::getT('resume_category_data'))
			->where('resume_id = '.$resume_id)
			->execute('getSlaveRows');			
		return $aItems;
	}
	
	public function getCatNameList($resume_id)
	{
		$aCats = $this->database()->select('rc.category_id, rc.name, rc.name_url')
					->from(Phpfox::getT('resume_category_data'),'rcd')
					->innerJoin(Phpfox::getT('resume_category'),'rc','rcd.resume_id = '.$resume_id . ' AND rc.category_id = rcd.category_id')
					->execute('getRows');
		if($aCats)
        {
            foreach ($aCats as $key => $aCat)
            {
                if(Core\Lib::phrase()->isPhrase($aCat['name']))
                {
                    $aCats[$key]['name'] = _p($aCat['name']);
                }
            }
        }
		return $aCats;		
	}
	
	public function getCatNameListFromSelectedResumes($aResumeIdList)
	{
		$sResumeIdList = implode(',', $aResumeIdList);
		
		$aCats = $this -> database() -> select('rcd.*, rc.name, rc.name_url')
					   -> from(Phpfox::getT('resume_category_data'),'rcd')
					   -> innerJoin(Phpfox::getT('resume_category'),'rc','rcd.resume_id in ('. $sResumeIdList . ') AND rc.category_id = rcd.category_id')
					   -> order('rcd.resume_id DESC')
					   -> execute('getRows');
		return $aCats;			  
	}
	/*
	 * Checking if a category has related data or not
	 */
	public function hasData($iCatId)
	{
		$iNumOfData = $this -> database()
							-> select('COUNT(*)')
							-> from(Phpfox::getT('resume_category_data'))
							-> where('category_id = ' . $iCatId)
							-> execute('getSlaveField');
							
		if($iNumOfData == 0)
		{
			return false;
		}
		
		return true;
	}

	private function _get($iParentId, $iActive = null)
	{
		$aCategories = $this->database()->select('*')
			->from($this->_sTable)
			->where('parent_id = ' . (int) $iParentId . ' AND is_active = ' . (int) $iActive . '')
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
				$this->_sOutput .= '<select name="val[category][]" class="js_mp_category_list form-control" id="js_mp_id_' . $iParentId . '">' . "\n";
				$this->_sOutput .= '<option value="">' . ($iParentId === 0 ? _p('resume.select') : _p('resume.select_a_sub_category')) . ':</option>' . "\n";
			}
			
			foreach ($aCategories as $iKey => $aCategory)
			{
				$aCache[] = $aCategory['category_id'];
				
				if ($this->_sDisplay == 'option')
				{
					$this->_sOutput .= '<option value="' . $aCategory['category_id'] . '" id="js_mp_category_item_' . $aCategory['category_id'] . '">' . ($this->_iCnt > 0 ? str_repeat('&nbsp;', ($this->_iCnt * 2)) . ' ' : '') . Phpfox::getLib('locale')->convert(_p($aCategory['name'])) . '</option>' . "\n";
					$this->_sOutput .= $this->_get($aCategory['category_id'], $iActive);					
				}
				elseif ($this->_sDisplay == 'admincp')
				{
					$sOutput .= '<li><img src="' . Phpfox::getLib('template')->getStyle('image', 'misc/draggable.png') . '" alt="" /> <input type="hidden" name="order[' . $aCategory['category_id'] . ']" value="' . $aCategory['ordering'] . '" class="js_mp_order" /><a href="#?id=' . $aCategory['category_id'] . '" class="js_drop_down">' . Phpfox::getLib('locale')->convert(_p($aCategory['name'])) . ' (' .$aCategory['used']. ')' . '</a>' . $this->_get($aCategory['category_id'], $iActive) . '</li>' . "\n";
				}
				else 
				{				
					$this->_sOutput .= '<option value="' . $aCategory['category_id'] . '" id="js_mp_category_item_' . $aCategory['category_id'] . '">' . Phpfox::getLib('locale')->convert($aCategory['name']) . '</option>' . "\n";
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
					$this->_get($iCateoryId, $iActive);
				}
			}
			
			$this->_iCnt = 0;
		}		
	}
    public function getAllItemBelongToCategory($iCategoryId)
    {
        return $this->database()->select('COUNT(ccd.resume_id)')
            ->from(Phpfox::getT('resume_category_data'), 'ccd')
            ->where('ccd.category_id = '.$iCategoryId)
            ->execute('getSlaveField');

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
            }
        }

        return $aRows;
    }
}

?>