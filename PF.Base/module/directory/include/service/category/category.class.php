<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

class Directory_Service_Category_Category extends Phpfox_Service
{

    private $_sDisplay = 'select';
    private $_iCnt = 0;
    private $_sOutput = '';

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('directory_category');
    }

    public function getParentCategoryByBusinessId($business_id){
        $aCategories = $this->database()->select('*')
            ->from(Phpfox::getT('directory_category_data'), 'dcd')
            ->join($this->_sTable, 'dct', 'dct.parent_id = 0 AND dct.category_id = dcd.category_id')
            ->where('dcd.business_id = ' . (int) $business_id)
            ->execute('getSlaveRows');

        return $aCategories;
    }

    public function getChildCategoryByParentAndBusiness($parent_id, $business_id){
        $aCategories = $this->database()->select('*')
            ->from(Phpfox::getT('directory_category_data'), 'dcd')
            ->join($this->_sTable, 'dct', 'dct.parent_id = ' . (int) $parent_id . ' AND dct.category_id = dcd.category_id')
            ->where('dcd.business_id = ' . (int) $business_id)
            ->execute('getSlaveRows');

        return $aCategories;
    }

    public function getCategoryIds($iId)
    {
        $aCategories = $this->database()->select('category_id')
            ->from(Phpfox::getT('directory_category_data'))
            ->where('business_id = ' . (int) $iId)
            ->execute('getSlaveRows');
            
        $aCache = array();
        foreach ($aCategories as $aCategory)
        {
            $aCache[] = $aCategory['category_id'];
        }
        
        return implode(',', $aCache);
    }

    public function getCustomGroup($iCateoryId){
        return $this->database()
                    ->select('ycg.*')
                    ->from(Phpfox::getT('directory_category_customgroup_data'),'yccd')
                    ->join(Phpfox::getT('directory_custom_group'),'ycg','yccd.group_id = ycg.group_id')
                    ->where('category_id = '.(int)$iCateoryId)
                    ->execute('getRows');
    }
    public function getForEdit($iId)
    {
        $aRow = $this->database()->select('*, title as name')->from($this->_sTable)->where('category_id = '.(int)$iId)->execute('getRow');

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
        $aLanguages = Phpfox::getService('language')->getAll();
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

    public function getParentCategory(){
        $aCategories = $this->database()->select('*')
            ->from($this->_sTable, 'yc')
            ->where('yc.parent_id = 0')
            ->execute('getSlaveRows');
        return $aCategories;
    }

    public function get($iSelected = null)
    {
        $sCacheId = $this->cache()->set('directory_category_display_'.$this->_sDisplay.'_'.Phpfox::getLib('locale')->getLangId());
        $this->cache()->group('directory_category_display_'.$this->_sDisplay.'_'.Phpfox::getLib('locale')->getLangId(),$sCacheId);
        if ($this->_sDisplay == 'admincp')
        {
            return $this->_get(0, 1);
        }
        else
        {
            if ($this->_sDisplay == 'search')
            {
                $this->_get(0, 1, $iSelected);
            }
            elseif($this->_sDisplay == 'searchblock')
            {
                $this->_getBlock(0, 1, $iSelected);
            }
            elseif($this->_sDisplay == 'admin_option')
            {
                $this->_get(0, 1, $iSelected);
            }
            elseif (!($this->_sOutput = $this->cache()->get($sCacheId)))
            {
                $this->_get(0, 1);
                $this->cache()->save($sCacheId, $this->_sOutput);
            }
            elseif ($this->_sDisplay == 'select'){
                $this->cache()->removeGroup('directory_category_display_'.$this->_sDisplay.'_'.Phpfox::getLib('locale')->getLangId());
                return $this->_sOutput;

            }else {
                $this->cache()->removeGroup('directory_category_display_'.$this->_sDisplay.'_'.Phpfox::getLib('locale')->getLangId());
                return $this->_sOutput;
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
                $this->_sOutput .= '<select name="search[category]['.$iParentId.']" class="category js_mp_category_list form-control" id="js_mp_id_'.$iParentId.'">'."\n";
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
                    $this->_sOutput .= '<option value="'.$aCategory['category_id'].'" id="js_mp_category_item_'.$aCategory['category_id'].'">'.($this->_iCnt > 0 ? str_repeat('&nbsp;', ($this->_iCnt * 2)).' ' : '').((Core\Lib::phrase()->isPhrase($aCategory['title'])) ? _p($aCategory['title']) : Phpfox::getLib('locale')->convert($aCategory['title'])).'</option>'."\n";
                    //$this->_sOutput .= $this->_get($aCategory['category_id'], $iActive);
                }
                elseif ($this->_sDisplay == 'admincp')
                {
                    $sOutput .= '<li><img src="'.Phpfox::getLib('template')->getStyle('image', 'misc/draggable.png').'" alt="" /> <input type="hidden" name="order['.$aCategory['category_id'].']" value="'.$aCategory['ordering'].'" class="js_mp_order" /><a href="#?id='.$aCategory['category_id'].'" class="js_drop_down">'.((Core\Lib::phrase()->isPhrase($aCategory['title'])) ? _p($aCategory['title']) : Phpfox::getLib('locale')->convert($aCategory['title'])).'</a>'.$this->_get($aCategory['category_id'], $iActive, $iSelected).'</li>'."\n";
                }
                elseif ($this->_sDisplay == 'searchblock')
                {
                    $selected = (isset($iSelected) && ($iSelected == $aCategory['category_id'] || $this->isChild($iSelected, $aCategory['category_id']))) ? ' selected="selected"' : '';
                    $this->_sOutput .= '<option value="'.$aCategory['category_id'].'" id="js_mp_category_item_'.$aCategory['category_id'].'"'.$selected.' >'.((Core\Lib::phrase()->isPhrase($aCategory['title'])) ? _p($aCategory['title']) : Phpfox::getLib('locale')->convert($aCategory['title'])).'</option>'."\n";
                }
                else
                {
                    $this->_sOutput .= '<option value="'.$aCategory['category_id'].'" id="js_mp_category_item_'.$aCategory['category_id'].'">'.((Core\Lib::phrase()->isPhrase($aCategory['title'])) ? _p($aCategory['title']) : Phpfox::getLib('locale')->convert($aCategory['title'])).'</option>'."\n";
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
        $aCategories = $this->database()->select('*')->from($this->_sTable)->where('parent_id = '.(int)$iParentId.' AND is_active = '.(int)$iActive.'')->order('ordering ASC')->execute('getRows');

        if (count($aCategories))
        {
            $aCache = array();

            if ($iParentId != 0)
            {
                $this->_iCnt++;
            }

            if ($this->_sDisplay == 'option' || $this->_sDisplay == 'admin_option')
            {

            }
            elseif ($this->_sDisplay == 'admincp')
            {
                $sOutput = '<ul>';
            }
            elseif ($this->_sDisplay == 'search')
            {
                $display = (isset($iSelected) && ($this->isChild($iSelected, $iParentId) || $iSelected == $iParentId)) ? '' : 'display:none; ';
                $this->_sOutput .= '<div class="js_mp_parent_holder" id="js_mp_holder_'.$iParentId.'" '.($iParentId > 0 ? ' style="'.$display.'padding:5px 0px 0px 0px;"' : '').'>';
                $this->_sOutput .= '<select name="search[category]['.$iParentId.']" class="js_mp_category_list form-control" id="js_mp_id_'.$iParentId.'">'."\n";
                $this->_sOutput .= '<option value="">'.($iParentId === 0 ? _p('select') : _p('select_a_sub_category')).':</option>'."\n";
            }
            else
            {
                $this->_sOutput .= '<div class="js_mp_parent_holder" id="js_mp_holder_'.$iParentId.'" '.($iParentId > 0 ? ' style="display:none; padding:5px 0px 0px 0px;"' : '').'>';
                $this->_sOutput .= '<select name="val[category]['.$iParentId.'][]" class="js_mp_category_list form-control" id="js_mp_id_'.$iParentId.'">'."\n";
                $this->_sOutput .= '<option value="">'.($iParentId === 0 ? _p('select') : _p('select_a_sub_category')).':</option>'."\n";
            }

            foreach ($aCategories as $iKey => $aCategory)
            {
                $aCache[] = $aCategory['category_id'];

                if ($this->_sDisplay == 'option')
                {
                    $this->_sOutput .= '<option value="'.$aCategory['category_id'].'" id="js_mp_category_item_'.$aCategory['category_id'].'">'.($this->_iCnt > 0 ? str_repeat('&nbsp;', ($this->_iCnt * 2)).' ' : '').((Core\Lib::phrase()->isPhrase($aCategory['title'])) ? _p($aCategory['title']) : Phpfox::getLib('locale')->convert($aCategory['title'])).'</option>'."\n";

                    // Max 3 level in category.
                    if ($this->_iCnt < 1)
                    {
                        $this->_sOutput .= $this->_get($aCategory['category_id'], $iActive);
                    }
                }
                if ($this->_sDisplay == 'admin_option')
                {
                    if($iSelected == $aCategory['category_id'])
                        continue;
                    $this->_sOutput .= '<option value="'.$aCategory['category_id'].'" id="js_mp_category_item_'.$aCategory['category_id'].'">'.($this->_iCnt > 0 ? str_repeat('&nbsp;', ($this->_iCnt * 2)).' ' : '').((Core\Lib::phrase()->isPhrase($aCategory['title'])) ? _p($aCategory['title']) : Phpfox::getLib('locale')->convert($aCategory['title'])).'</option>'."\n";
                }
                elseif ($this->_sDisplay == 'admincp')
                {
                    $sOutput .= '<li><img src="'.Phpfox::getLib('template')->getStyle('image', 'misc/draggable.png').'" alt="" /> <input type="hidden" name="order['.$aCategory['category_id'].']" value="'.$aCategory['ordering'].'" class="js_mp_order" /><a href="#?id='.$aCategory['category_id'].'" class="js_drop_down">'.((Core\Lib::phrase()->isPhrase($aCategory['title'])) ? _p($aCategory['title']) : Phpfox::getLib('locale')->convert($aCategory['title'])).'</a>'.$this->_get($aCategory['category_id'], $iActive, $iSelected).'</li>'."\n";
                }
                elseif ($this->_sDisplay == 'search')
                {
                    $selected = (isset($iSelected) && ($iSelected == $aCategory['category_id'] || $this->isChild($iSelected, $aCategory['category_id']))) ? ' selected="selected"' : '';
                    $this->_sOutput .= '<option value="'.$aCategory['category_id'].'" id="js_mp_category_item_'.$aCategory['category_id'].'"'.$selected.' >'.((Core\Lib::phrase()->isPhrase($aCategory['title'])) ? _p($aCategory['title']) : Phpfox::getLib('locale')->convert($aCategory['title'])).'</option>'."\n";
                }
                else
                {
                    $this->_sOutput .= '<option value="'.$aCategory['category_id'].'" id="js_mp_category_item_'.$aCategory['category_id'].'">'.((Core\Lib::phrase()->isPhrase($aCategory['title'])) ? _p($aCategory['title']) : Phpfox::getLib('locale')->convert($aCategory['title'])).'</option>'."\n";
                }
            }

            if ($this->_sDisplay == 'option' || $this->_sDisplay == 'admin_option')
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
                if ($iParentId == 0 ){
                    $this->_sOutput .= '<div class="radio ync-radio-custom"><label id="yndirectory_maincategory"><input style="margin-right: 5px" class="yndirectory-categorylist-maincategory" type="radio" name="val[maincategory]" value="0" /><i class="ico ico-circle-o mr-1"></i>' . _p('directory.main_category') . '</label></div>';
                    $this->_sOutput .= '<div class="extra_info mt-h1"><a id="yndirectory_add" href="#" onclick="yndirectory.appendPredefined(this,\'category\'); return false;">' . Phpfox::getLib('phpfox.image.helper')->display(array('theme' => 'misc/add.png', 'class' => 'v_middle')) . '</a>';
                    $this->_sOutput .= '<a id="yndirectory_delete" style="display: none;" href="#" onclick="yndirectory.removePredefined(this,\'category\'); return false;">' . '<img src="'.phpfox::getParam('core.path').'module/directory/static/image/delete.png" class="v_middle"/>' . '</a></div>';

                }
                $this->_sOutput .= '</div>';

                foreach ($aCache as $iCateoryId)
                {
                    $this->_get($iCateoryId, $iActive, $iSelected);
                }
            }

            $this->_iCnt = 0;
        }
    }

    public function getForBrowse($iCategoryId = null, $sIsRatingArea = null)
    {
        $sCacheId = $this->cache()->set('directory_category_browse_' . ($iCategoryId === null ? '' : '_' . $iCategoryId) );
        $this->cache()->group('directory_category',$sCacheId);

        if (!($aCategories = $this->cache()->get($sCacheId)))
        {                   
            $aCategories = $this->database()->select('dc.category_id, dc.title, dc.title as name, dc.used')
                ->from($this->_sTable, 'dc')
                ->where('dc.is_active = 1 AND dc.parent_id = ' . ($iCategoryId === null ? '0' : (int) $iCategoryId) . '')
                ->order('dc.ordering ASC')
                ->execute('getRows');
            
            foreach ($aCategories as $iKey => $aCategory)
            {
            
                    $aCategories[$iKey]['url'] = Phpfox::permalink('directory.category', $aCategory['category_id'], $aCategory['title']);
      
                
                //if ($sCategory === null)
                {
                    $aCategories[$iKey]['sub'] = $this->database()->select('dc.category_id, dc.title, dc.title as name ,dc.used')
                        ->from($this->_sTable, 'dc')
                        ->where('dc.is_active = 1 AND dc.parent_id = ' . $aCategory['category_id'] . '')
                        ->order('dc.ordering ASC')
                        ->execute('getRows');           
                        
                    foreach ($aCategories[$iKey]['sub'] as $iSubKey => $aSubCategory)
                    {
                            $aCategories[$iKey]['sub'][$iSubKey]['url'] = Phpfox::permalink('directory.category', $aSubCategory['category_id'], $aSubCategory['title']);
         
                    }
                }
            }
            
            $this->cache()->save($sCacheId, $aCategories);
        }

        return $aCategories;    
    }

    public function getCategoriesById($iId = null, &$aCategories = null)
    {
        if ($aCategories === null) {
            $aCategories = $this->database()->select('dc.category_id, dc.title, dc.title as name, dc.used')
                ->from($this->_sTable, 'dc')
                ->join(Phpfox::getT('directory_category_data'), 'dcd', 'dcd.category_id = dc.category_id')
                ->where('dc.is_active = 1 AND dcd.business_id = ' . ($iId === null ? '0' : (int)$iId) . '')
                ->order('dc.ordering ASC')
                ->execute('getRows');

        }

        if (!count($aCategories)) {
            return null;
        }

        $aBreadcrumb = array();
        if (count($aCategories) > 1) {
            foreach ($aCategories as $aCategory) {
                $aBreadcrumb[] = array(
                    (Core\Lib::phrase()->isPhrase($aCategory['title']) ? _p($aCategory['title']) : Phpfox_Locale::instance()->convert($aCategory['title'])),
                    Phpfox::permalink('directory.category', $aCategory['category_id'],
                        (Core\Lib::phrase()->isPhrase($aCategory['title']) ? _p($aCategory['title']) : Phpfox_Locale::instance()->convert($aCategory['title'])))
                );
            }
        } else {
            $aBreadcrumb[] = array(
                (Core\Lib::phrase()->isPhrase($aCategories[0]['title']) ? _p($aCategories[0]['title']) : Phpfox_Locale::instance()->convert($aCategories[0]['title'])),
                Phpfox::permalink('directory.category', $aCategories[0]['category_id'],
                    (Core\Lib::phrase()->isPhrase($aCategories[0]['title']) ? _p($aCategories[0]['title']) : Phpfox_Locale::instance()->convert($aCategories[0]['title'])))
            );
        }

        return $aBreadcrumb;
    }

    public function getForBrowseByBusinessId($iBussinessId,$iCategoryId = null)
    {
        $aRows = $this->database()->select('dcd.category_id, dcd.is_main, dc.title, dc.parent_id, dc.ordering, dc.used')
            ->from(Phpfox::getT('directory_category_data'), 'dcd')
            ->join(Phpfox::getT('directory_category'),'dc','dc.category_id = dcd.category_id')
            ->where('dc.is_active = 1 AND dcd.business_id =  '.(int) $iBussinessId)
            ->order('dcd.data_id ASC')
            ->execute('getRows');

        $aCategories = array();

        foreach ($aRows as $k => $v)
        {
            if ($v['parent_id'] == 0)
            {
                $aCategories[] = $v;
            }
        }

        foreach ($aCategories as $k => $v)
        {
            $aCategories[$k]['sub'] = $this->_getChildInArray($aRows, $v['category_id']);
        }

        $this->_processRows($aCategories);

        uasort($aCategories, function($a, $b)
        {
            if ($a['is_main'] > $b['is_main'])
            {
                return -1;
            }
            elseif ($a['is_main'] < $b['is_main'])
            {
                return 1;
            }

            return ($a['ordering'] < $b['ordering']) ? -1 : 1;
        });

        return array_values($aCategories);
    }

    /**
     * @param $iBusinessId
     * @return array|int|string
     */
    public function getMainCategoryByBusinessId($iBusinessId)
    {
        $sCacheId = $this->cache()->set('directory_business_main_category');
        $aCache = $this->cache()->get($sCacheId);

        if(isset($aCache[$iBusinessId])) {
            return $aCache[$iBusinessId];
        }
        $aRow = $this->database()->select('dcd.category_id, dc.title')
            ->from(Phpfox::getT('directory_category_data'), 'dcd')
            ->join(Phpfox::getT('directory_category'),'dc','dc.category_id = dcd.category_id')
            ->where('dcd.is_main = 1 AND dc.is_active = 1 AND dcd.business_id =  '.(int) $iBusinessId)
            ->order('dcd.data_id ASC')
            ->execute('getSlaveRow');
        if(!is_array($aCache))
            $aCache = array();
        $aCache[$iBusinessId] = $aRow;
        $this->cache()->save($sCacheId, $aCache);
        return $aRow;
    }

    /**
     * Get and delete selected child
     */
    private function _getChildInArray(&$aRows, $iParentId)
    {
        foreach ($aRows as $k => $v)
        {
            if ($v['parent_id'] == 0)
            {
                unset($aRows[$k]);
            }

            if ($v['parent_id'] == $iParentId)
            {
                unset($aRows[$k]);
                return array($v);
            }
        }

        return array();
    }

    /**
     * Process categories info
     */
    private function _processRows(&$aRows)
    {
        foreach ($aRows as $key => $value)
        {
            $this->_processRow($aRows[$key]);

            if (!empty($aRows[$key]['sub']))
            {
                foreach ($aRows[$key]['sub'] as $keySub => $valueSub)
                {
                    $this->_processRow($aRows[$key]['sub'][$keySub]);
                }
            }
        }
    }

    /**
     * Process category info
     */
    private function _processRow(&$aRow)
    {
        $aRow['title'] = Phpfox::getLib('locale')->convert($aRow['title']);
        $aRow['url'] = Phpfox::permalink('directory.category', $aRow['category_id'], $aRow['title']);
    }

    public function getChildIds($iParentId)
    {
        $sCategories = $this->_getChildIds($iParentId);
        $sCategories = trim($sCategories, ',');
        
        return $sCategories;
    }
    
    private function _getChildIds($iParentId)
    {
        $aCategories = $this->database()->select('dc.category_id')
            ->from($this->_sTable, 'dc')
            ->where('dc.parent_id = ' . (int) $iParentId)
            ->execute('getRows');
        
        $sCategories = '';
        foreach ($aCategories as $aCategory)
        {
            $sCategories .= $aCategory['category_id'] . ',' . $this->_getChildIds($aCategory['category_id']);
        }
        
        return $sCategories;        
    }       

    public function getParentId($iCategoryId)
    {
        $aCategories = $this->database()->select('dc.parent_id')
            ->from($this->_sTable, 'dc')
            ->where('dc.category_id = ' . (int) $iCategoryId)
            ->execute('getRow');
        
        $sCategories = '';

        if($aCategories['parent_id'] == 0){
            return $iCategoryId;
        }
        else{
            return $aCategories['parent_id'];
        }
    } 

    public function isChild($iId, $iParentId)
    {
        $aRow = $this->database()->select('*')
            ->from($this->_sTable, 'dc')
            ->where('dc.category_id = '.(int)$iId.' AND dc.parent_id = '.(int)$iParentId)
            ->execute('getSlaveRow');
        
        if (!empty($aRow))
        {
            return true;
        }
        
        return false;
    }

    public function checkMainCategory($iBussinessId){
       
       $aInfos =  $this->database()->select('dcd.is_main,dcd.category_id')
                ->from(Phpfox::getT('directory_category_data'),'dcd')
                ->where('dcd.business_id = ' . (int) $iBussinessId)
                ->execute('getRows');

        foreach ($aInfos as $key => $aInfo) {
            
            if($aInfo['is_main'] == 1){
                return $aInfo['category_id'];
            }

        }

        return 0; 
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

    public function getAllItemBelongToCategory($iCategoryId)
    {
        return $this->database()->select('COUNT(dcd.business_id)')
            ->from(Phpfox::getT('directory_category_data'), 'dcd')
            ->where('dcd.category_id = '.$iCategoryId)
            ->execute('getSlaveField');

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
         * No method or plug-in found we must throw a error.
         */
        Phpfox_Error::trigger('Call to undefined method '.__class__.'::'.$sMethod.'()', E_USER_ERROR);
    }

}

?>
