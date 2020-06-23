<?php

defined('PHPFOX') or exit('NO DICE!');

class Ecommerce_Service_Category_Category extends Phpfox_Service {

    private $_sDisplay = 'select';
    private $_iCnt = 0;
    private $_sOutput = '';

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('ecommerce_category');
    }

    public function getCustomGroup($iCateoryId)
    {
        return $this->database()
                        ->select('ycg.*')
                        ->from(Phpfox::getT('ecommerce_category_customgroup_data'), 'yccd')
                        ->join(Phpfox::getT('ecommerce_custom_group'), 'ycg', 'yccd.group_id = ycg.group_id')
                        ->where('category_id = ' . (int) $iCateoryId)
                        ->execute('getRows');
    }

    public function getForEdit($iId)
    {
        $aRow = $this->database()->select('*')->from($this->_sTable)->where('category_id = ' . (int) $iId)->execute('getRow');

        if (!isset($aRow['category_id']))
        {
            return false;
        }

        //Support legacy phrases
        if (substr($aRow['title'], 0, 7) == '{phrase' && substr($aRow['title'], -1) == '}') {
            $aRow['title'] = preg_replace('/\s+/', ' ', $aRow['title']);
            $aRow['title'] = str_replace([
                "{phrase var='",
                "{phrase var=\"",
                "'}",
                "\"}"
            ], "", $aRow['title']);
        }//End support legacy
        $aLanguages = Language_Service_Language::instance()->getAll();
        foreach ($aLanguages as $aLanguage){
            $sPhraseValue = (Core\Lib::phrase()->isPhrase($aRow['title'])) ? _p($aRow['title'], [], $aLanguage['language_id']) : $aRow['title'];
            $aRow['name_' . $aLanguage['language_id']] = $sPhraseValue;
        }

        return $aRow;
    }

    public function display($sDisplay)
    {
        $this->_sDisplay = $sDisplay;
        return $this;
    }

    public function get($iSelected = null)
    {
        $sCacheId = $this->cache()->set('ecommerce_category_display_' . $this->_sDisplay . '_' . Phpfox::getLib('locale')->getLangId());
		
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
            if ($this->_sDisplay == 'search')
            {
                $this->_get(0, 1, $iSelected);
            }
            elseif ($this->_sDisplay == 'searchblock')
            {
                $this->_getBlock(0, 1, $iSelected);
            }
            elseif (!($this->_sOutput = $this->cache()->get($sCacheId)))
            {
                $this->_get(0, 1);

                $this->cache()->save($sCacheId, $this->_sOutput);
            }
            return $this->_sOutput;
        }
    }

    private function _getBlock($iParentId, $iActive = null, $iSelected = null)
    {

        $aCategories = $this->database()->select('*')->from($this->_sTable)->where('parent_id = 0'.(int)$iParentId.' AND is_active = '.(int)$iActive.'')->order('ordering ASC')->execute('getRows');

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
            elseif ($this->_sDisplay == 'searchblock')
            {
                $display = (isset($iSelected) && ($this->isChild($iSelected, $iParentId) || $iSelected == $iParentId)) ? '' : 'display:none; ';
                $this->_sOutput .= '<div class="js_mp_parent_holder" id="js_mp_holder_'.$iParentId.'" '.($iParentId > 0 ? ' style="'.$display.'padding:5px 0px 0px 0px;"' : '').'>';
                $this->_sOutput .= '<select name="search[category]['.$iParentId.']" class="js_mp_category_list form-control" id="js_mp_id_'.$iParentId.'">'."\n";
                $this->_sOutput .= '<option value="">'.($iParentId === 0 ? _p('select') : _p('select_a_sub_category')).':</option>'."\n";
            }
            else
            {
                $this->_sOutput .= '<div class="js_mp_parent_holder" id="js_mp_holder_'.$iParentId.'" '.($iParentId > 0 ? ' style="display:none; padding:5px 0px 0px 0px;"' : '').'>';
                $this->_sOutput .= '<select name="val[category]['.$iParentId.']" class="js_mp_category_list form-control" id="js_mp_id_'.$iParentId.'">'."\n";
                $this->_sOutput .= '<option value="">'.($iParentId === 0 ? _p('select') : _p('select_a_sub_category')).':</option>'."\n";
            }

            foreach ($aCategories as $iKey => $aCategory)
            {
                $aCache[] = $aCategory['category_id'];

                if ($this->_sDisplay == 'option')
                {
                    $this->_sOutput .= '<option value="'.$aCategory['category_id'].'" id="js_mp_category_item_'.$aCategory['category_id'].'">'.($this->_iCnt > 0 ? str_repeat('&nbsp;', ($this->_iCnt * 2)).' ' : '').((Phpfox::isPhrase($aCategory['title'])) ? _p($aCategory['title']) : Phpfox::getLib('locale')->convert($aCategory['title'])) .'</option>'."\n";
                    //$this->_sOutput .= $this->_get($aCategory['category_id'], $iActive);
                }
                elseif ($this->_sDisplay == 'admincp')
                {
                    $sOutput .= '<li><img src="'.Phpfox::getLib('template')->getStyle('image', 'misc/draggable.png').'" alt="" /> <input type="hidden" name="order['.$aCategory['category_id'].']" value="'.$aCategory['ordering'].'" class="js_mp_order" /><a href="#?id='.$aCategory['category_id'].'" class="js_drop_down">'.((Phpfox::isPhrase($aCategory['title'])) ? _p($aCategory['title']) : Phpfox::getLib('locale')->convert($aCategory['title'])) .'</a>'.$this->_get($aCategory['category_id'], $iActive, $iSelected).'</li>'."\n";
                }
                elseif ($this->_sDisplay == 'searchblock')
                {
                    $selected = (isset($iSelected) && ($iSelected == $aCategory['category_id'] || $this->isChild($iSelected, $aCategory['category_id']))) ? ' selected="selected"' : '';
                    $this->_sOutput .= '<option value="'.$aCategory['category_id'].'" id="js_mp_category_item_'.$aCategory['category_id'].'"'.$selected.' >'.((Phpfox::isPhrase($aCategory['title'])) ? _p($aCategory['title']) : Phpfox::getLib('locale')->convert($aCategory['title'])) .'</option>'."\n";
                }
                else
                {
                    $this->_sOutput .= '<option value="'.$aCategory['category_id'].'" id="js_mp_category_item_'.$aCategory['category_id'].'">'.((Phpfox::isPhrase($aCategory['title'])) ? _p($aCategory['title']) : Phpfox::getLib('locale')->convert($aCategory['title'])) .'</option>'."\n";
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
                $this->_sOutput .= '</select>'."\n";
                $this->_sOutput .= '</div>';

                foreach ($aCache as $iCateoryId)
                {
                    $this->_getBlock($iCateoryId, $iActive, $iSelected);
                }
            }

            $this->_iCnt = 0;
        }
    }
    
    private function _get($iParentId, $iActive = null, $iSelected = null)
    {
        $aCategories = $this->database()->select('*')->from($this->_sTable)->where('parent_id = ' . (int) $iParentId . ' AND is_active = ' . (int) $iActive . '')->order('ordering ASC')->execute('getRows');

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
            elseif ($this->_sDisplay == 'search')
            {
                $display = (isset($iSelected) && ($this->isChild($iSelected, $iParentId) || $iSelected == $iParentId)) ? '' : 'display:none; ';
                $this->_sOutput .= '<div class="js_mp_parent_holder" id="js_mp_holder_' . $iParentId . '" ' . ($iParentId > 0 ? ' style="' . $display . 'padding:5px 0px 0px 0px;"' : '') . '>';
                $this->_sOutput .= '<select name="search[category][' . $iParentId . ']" class="js_mp_category_list form-control" id="js_mp_id_' . $iParentId . '">' . "\n";
                $this->_sOutput .= '<option value="">' . ($iParentId === 0 ? _p('select') : _p('select_a_sub_category')) . ':</option>' . "\n";
            }
            else
            {
                $this->_sOutput .= '<div class="js_mp_parent_holder" id="js_mp_holder_' . $iParentId . '" ' . ($iParentId > 0 ? ' style="display:none; padding:5px 0px 0px 0px;"' : '') . '>';
                $this->_sOutput .= '<select name="val[category][' . $iParentId . '][]" class="js_mp_category_list form-control" id="js_mp_id_' . $iParentId . '">' . "\n";
                $this->_sOutput .= '<option value="">' . ($iParentId === 0 ? _p('select') : _p('select_a_sub_category')) . ':</option>' . "\n";
            }

            foreach ($aCategories as $iKey => $aCategory)
            {
                $aCache[] = $aCategory['category_id'];

                if ($this->_sDisplay == 'option')
                {
                    $this->_sOutput .= '<option value="' . $aCategory['category_id'] . '" id="js_mp_category_item_' . $aCategory['category_id'] . '">' . ($this->_iCnt > 0 ? str_repeat('&nbsp;', ($this->_iCnt * 2)) . ' ' : '') . ((Phpfox::isPhrase($aCategory['title'])) ? _p($aCategory['title']) : Phpfox::getLib('locale')->convert($aCategory['title']))  . '</option>' . "\n";

                    // Max 3 level in category.
                    if ($this->_iCnt < 1)
                    {
                        $this->_sOutput .= $this->_get($aCategory['category_id'], $iActive);
                    }
                }
                elseif ($this->_sDisplay == 'admincp')
                {
                    $sIcon = '';
                    if (!empty($aCategory['image_path']))
                    {
                        $sIcon = Phpfox::getLib('image.helper')->display(array(
                            'server_id' => $aCategory['server_id'],
                            'path' => 'core.url_pic',
                            'file' => $aCategory['image_path'],
                            'suffix' => '_16'
                                )
                        );
                    }
                    $sOutput .= '<li><img src="' . Phpfox::getLib('template')->getStyle('image', 'misc/draggable.png') . '" alt="" /> ' . $sIcon . ' <input type="hidden" name="order[' . $aCategory['category_id'] . ']" value="' . $aCategory['ordering'] . '" class="js_mp_order" /><a href="#?id=' . $aCategory['category_id'] . '" class="js_drop_down">' . ((Phpfox::isPhrase($aCategory['title'])) ? _p($aCategory['title']) : ((Phpfox::isPhrase($aCategory['title'])) ? _p($aCategory['title']) : Phpfox::getLib('locale')->convert($aCategory['title'])) ) . '</a>' . $this->_get($aCategory['category_id'], $iActive, $iSelected) . '</li>' . "\n";
                }
                elseif ($this->_sDisplay == 'search')
                {
                    $selected = (isset($iSelected) && ($iSelected == $aCategory['category_id'] || $this->isChild($iSelected, $aCategory['category_id']))) ? ' selected="selected"' : '';
                    $this->_sOutput .= '<option value="' . $aCategory['category_id'] . '" id="js_mp_category_item_' . $aCategory['category_id'] . '"' . $selected . ' >' . ((Phpfox::isPhrase($aCategory['title'])) ? _p($aCategory['title']) : Phpfox::getLib('locale')->convert($aCategory['title']))  . '</option>' . "\n";
                }
                else
                {
                    $this->_sOutput .= '<option value="' . $aCategory['category_id'] . '" id="js_mp_category_item_' . $aCategory['category_id'] . '">' . ((Phpfox::isPhrase($aCategory['title'])) ? _p($aCategory['title']) : Phpfox::getLib('locale')->convert($aCategory['title']))  . '</option>' . "\n";
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
                    $this->_get($iCateoryId, $iActive, $iSelected);
                }
            }

            $this->_iCnt = 0;
        }
    }
    
    public function isChild($iId, $iParentId)
    {
        $aRow = $this->database()->select('*')->from($this->_sTable)->where('category_id = ' . (int) $iId . ' AND parent_id = ' . (int) $iParentId)->execute('getSlaveRow');

        if (!empty($aRow))
        {
            return true;
        }

        return false;
    }

    public function getChildIds($iParentId)
    {
        $sCategories = $this->_getChildIds($iParentId);
        $sCategories = trim($sCategories, ',');
        
        return $sCategories;
    }
    
    private function _getChildIds($iParentId)
    {
        $aCategories = $this->database()->select('ec.category_id')
            ->from($this->_sTable, 'ec')
            ->where('ec.parent_id = ' . (int) $iParentId)
            ->execute('getRows');
        
        $sCategories = '';
        foreach ($aCategories as $aCategory)
        {
            $sCategories .= $aCategory['category_id'] . ',' . $this->_getChildIds($aCategory['category_id']);
        }
        
        return $sCategories;        
    } 
    
    public function getParentCategory()
    {
        $aCategories = $this->database()->select('*')
                ->from($this->_sTable, 'ec')
                ->where('ec.parent_id = 0')
				->order('ec.ordering ASC')
                ->execute('getSlaveRows');
        return $aCategories;
    }

    public function getAllCategories()
    {
        return $this->getCategories(0, 1);
    }
    
    public function getCategories($iParentId, $iActive)
    {
        $aCategories = $this->database()
                ->select('*')
                ->from($this->_sTable)
                ->where('parent_id = ' . (int) $iParentId . ' AND is_active = ' . (int) $iActive . '')
                ->order('ordering ASC')
                ->execute('getRows');
        
        if ($aCategories)
        {
            foreach ($aCategories as $iKey => $aCategory)
            {
            	$aCategories[$iKey]['title'] = Phpfox::getLib('locale')->convert($aCategories[$iKey]['title']);
                $aCategories[$iKey]['sub_category'] = $this->getCategories($aCategory['category_id'], $iActive);
				
				$aCategories[$iKey]['url_photo'] = Phpfox::getLib('image.helper')->display(array(
						'server_id' => $aCategories[$iKey]['server_id'],
						'file' => $aCategories[$iKey]['image_path'],
						'path' => 'core.url_pic',
						'suffix' => '_16',
						'return_url' => true			
					)
				);
				if($aCategories[$iKey]['url_photo'] == '<span class="no_image_item i_size__16"><span></span></span>')
				{
					$aCategories[$iKey]['url_photo'] = '';
				}
                $class_category_item = str_replace(' ', '_', strtolower($aCategories[$iKey]['title']));
                $aCategories[$iKey]['class_category_item'] = $class_category_item;
            }
        }
        
        return $aCategories;
    }
    
    public function getCategoryIds($iId, $sType = 'auction')
    {
        $aCategories = $this->database()
                ->select('category_id')
                ->from(Phpfox::getT('ecommerce_category_data'))
                ->where('product_id = ' . (int) $iId . ' AND product_type ="'.$sType.'"')
                ->execute('getSlaveRows');
            
        $aCache = array();
        foreach ($aCategories as $aCategory)
        {
            $aCache[] = $aCategory['category_id'];
        }
        
        return implode(',', $aCache);
    }
    
    public function getForBrowseByAuctionId($iAuctionId, $iCategoryId = null)
    {
        $aCategories = $this->database()
                ->select('ecd.category_id, ec.title')
                ->from(Phpfox::getT('ecommerce_category_data'), 'ecd')
                ->join(Phpfox::getT('ecommerce_category'), 'ec', 'ec.category_id = ecd.category_id')
                ->where('ecd.product_id = ' . (int) $iAuctionId . ' AND ec.parent_id = 0')
                ->order('ecd.is_main ASC')
                ->execute('getRows');

        foreach ($aCategories as $iKey => $aCategory)
        {
            $aCategories[$iKey]['title'] = Phpfox::getLib('locale')->convert($aCategories[$iKey]['title']);
            $aCategories[$iKey]['url'] = Phpfox::permalink('auction.category', $aCategory['category_id'], $aCategory['title']);
            $aCategories[$iKey]['sub'] = $this->database()
                    ->select('ec.category_id, ec.title ,ec.used')
                    ->from(Phpfox::getT('ecommerce_category'), 'ec')
                    ->join(Phpfox::getT('ecommerce_category_data'), 'ecd', 'ec.category_id = ecd.category_id')
                    ->where('ec.parent_id = ' . $aCategory['category_id'] . ' AND ecd.product_id = ' . (int) $iAuctionId)
                    ->order('ec.ordering ASC')
                    ->execute('getRows');

            foreach ($aCategories[$iKey]['sub'] as $iSubKey => $aSubCategory)
            {
                $aCategories[$iKey]['sub'][$iSubKey]['title'] = Phpfox::getLib('locale')->convert($aCategories[$iKey]['sub'][$iSubKey]['title']);
                $aCategories[$iKey]['sub'][$iSubKey]['url'] = Phpfox::permalink('auction.category', $aSubCategory['category_id'], $aSubCategory['title']);
            }
        }

        return $aCategories;
    }


    public function getParentId($iCategoryId)
    {
        $aCategories = $this->database()->select('ec.parent_id')
            ->from(Phpfox::getT('ecommerce_category'), 'ec')
            ->where('ec.category_id = ' . (int) $iCategoryId)
            ->execute('getRow');

        $sCategories = '';

        if($aCategories['parent_id'] == 0){
            return $iCategoryId;
        }
        else{
            return $aCategories['parent_id'];
        }
    }

    public function getAllItemBelongToCategory($iCategoryId)
    {
        return $this->database()->select('COUNT(ecd.product_id)')
                                ->from(Phpfox::getT('ecommerce_category_data'), 'ecd')
                                ->where('ecd.category_id = '.$iCategoryId)
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
