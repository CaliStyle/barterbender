<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

class Videochannel_Service_Category_Category extends Phpfox_Service
{

    private $_sOutput = '';
    private $_iCnt = 0;
    private $_sDisplay = 'select';

    /**
     *
     * @var Videochannel_Service_Channel_Process 
     */
    public $oSerVideoChannelCategoryProcess;

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('channel_category');

        $this->oSerVideoChannelCategoryProcess = Phpfox::getService('videochannel.channel.process');
    }

    public function getForEdit($iId)
    {
        $aRow = $this->database()->select('*')
                        ->from($this->_sTable)
                        ->where('category_id = ' . (int) $iId)
                        ->execute('getRow');

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
            ], "", $aRow['name']);
        }//End support legacy
        $aLanguages = Phpfox::getService('language')->getAll();
        foreach ($aLanguages as $aLanguage){
            $sPhraseValue = (Core\Lib::phrase()->isPhrase($aRow['name'])) ? _p($aRow['name'], [], $aLanguage['language_id']) : $aRow['name'];
            $aRow['name_' . $aLanguage['language_id']] = $sPhraseValue;
        }

        return $aRow;
    }

    public function getForBrowse($sWhere = null, $iCategoryId = null)
    {
        $sCacheId = $this->cache()->set('videochannel_category_browse' . ($iCategoryId === null ? '' : '_' . $iCategoryId));
        if (!($aCategories = $this->cache()->get($sCacheId)))
        {
            $aCategories = $this->database()->select('mc.category_id, mc.name')
                    ->from($this->_sTable, 'mc')
                    ->where('mc.parent_id = 0' . ($iCategoryId === null ? '0' : (int) $iCategoryId) . ' AND mc.is_active = 1')
                    ->order('mc.ordering ASC')
                    ->execute('getRows');

            foreach ($aCategories as $iKey => $aCategory)
            {
                $aCategories[$iKey]['url'] = Phpfox::permalink('videochannel.category', $aCategory['category_id'], $aCategory['name']);

                {
                    $aCategories[$iKey]['sub'] = $this->database()->select('mc.category_id, mc.name')
                            ->from($this->_sTable, 'mc')
                            ->where('mc.parent_id = ' . $aCategory['category_id'] . ' AND mc.is_active = 1')
                            ->order('mc.ordering ASC')
                            ->execute('getRows');

                    foreach ($aCategories[$iKey]['sub'] as $iSubKey => $aSubCategory)
                    {
                        $aCategories[$iKey]['sub'][$iSubKey]['url'] = Phpfox::permalink('videochannel.category', $aSubCategory['category_id'], $aSubCategory['name']);
                    }
                }
            }

            $this->cache()->save($sCacheId, $aCategories);
        }

        return $aCategories;
    }

    public function display($sDisplay)
    {
        $this->_sDisplay = $sDisplay;

        return $this;
    }

    public function getCategories($iParentId, $iActive = null)
    {
        return $this->database()->select('*')
                        ->from(Phpfox::getT('channel_category'))
                        ->where('parent_id = ' . (int) $iParentId . ' AND is_active = ' . (int) $iActive . '')
                        ->order('ordering ASC')
                        ->execute('getRows');
    }
    
    /**
     * Get all categories in level. Because we have 2 selectboxes so it's difficult to display the default value.
     * @param integer $iDefaultCategoryId Select the default category.
     * @param integer $iActive Select the active categories.
     * @return array
     */
    public function getCategoriesInLevel($iDefaultCategoryId = 0, $iActive = 1)
    {
        $aParentCategories = $this->getCategories(0, $iActive);
        
        if (count($aParentCategories) > 0)
        {
            foreach ($aParentCategories as $iKey => $aCategory)
            {
                // Select the default category in parent.
                if ($aCategory['category_id'] == $iDefaultCategoryId)
                {
                    $aParentCategories[$iKey]['bIsDefault'] = true;
                }
                else
                {
                    $aParentCategories[$iKey]['bIsDefault'] = false;
                }
                
                // Get the sub categories.
                $aChildCategories = array();
                
                $aChildCategories = $this->getCategories($aCategory['category_id'], $iActive);
                
                foreach($aChildCategories as $iSubKey => $aSubCategory)
                {
                    // Check the default value for sub categories.
                    if ($aSubCategory['category_id'] == $iDefaultCategoryId)
                    {
                        // Set the child to default value.
                        $aChildCategories[$iSubKey]['bIsDefault'] = true;
                        
                        // Set the parent to default value. Be careful.
                        $aParentCategories[$iKey]['bIsDefault'] = true;
                    }
                    else
                    {
                        $aChildCategories[$iSubKey]['bIsDefault'] = false;
                    }
                }
                
                $aParentCategories[$iKey]['children'] = $aChildCategories;
                
            }
        }
        
        return $aParentCategories;
    }
    
    public function getCategoryElementInHtml($aCategories = array(), $iParentId = 0, $bDisplay = false)
    {
        $sDisplay = ($iParentId == 0) ? '' : ($bDisplay ? '' : ' style="display: none;" ');
        $sSelectClassId = ' js_mp_id_' . $iParentId;
        $sSelectClassParent = ' js_mp' . ($iParentId == 0 ? '_parent' : '') . '_category_list';
        
        $sHtml = '';
        
        $sHtml .= '<div class="js_mp_parent_holder" id="js_mp_holder_' . $iParentId . '">';
        $sHtml .= '<select name="val[category][]" class="form-control ' . $sSelectClassParent . $sSelectClassId . '" ' . $sDisplay . ' >' . "\n";
        $sHtml .= '<option value="">' . ($iParentId === 0 ? _p('videochannel.select') : _p('videochannel.select_a_sub_category')) . ':</option>' . "\n";

        foreach ($aCategories as $iKey => $aCategory)
        {
            $sSelected = $aCategory['bIsDefault'] ? 'selected="selected"' : '';
            
            $sHtml .= '<option value="' . $aCategory['category_id'] . '" class="js_mp_category_item_' . $aCategory['category_id'] . '" ' . $sSelected . ' >' . (Core\Lib::phrase()->isPhrase($aCategory['name']) ? _p($aCategory['name']) : Phpfox::getLib('locale')->convert($aCategory['name'])) . '</option>' . "\n";
        }

        $sHtml .= '</select>' . "\n";
        $sHtml .= '</div>';

        return $sHtml;
    }
    
    
    public function getCategoriesInHTML($iDefaultCategoryId = 0, $iActive = 1)
    {
        $aCategories = $this->getCategoriesInLevel($iDefaultCategoryId, $iActive);
        
        $sHtml = '';
        
        // Get HTML for parent.
        $sHtml = $this->getCategoryElementInHtml($aCategories, 0, true);
        
        // Get HTML for children.
        foreach($aCategories as $aCategory)
        {
            if (count($aCategory['children']) > 0)
            {
                $bDisplay = false;
                
                if ($aCategory['bIsDefault'])
                {
                    $bDisplay = true;
                }
                
                $sHtml .= $this->getCategoryElementInHtml($aCategory['children'], $aCategory['category_id'], $bDisplay);
            }
        }
        
        return $sHtml;
    }
    
    /**
     * This function has an error when using on Ajax Mode. So we will change it "Not use cache".
     * @return string HTML of categories in form.
     */
    public function get($iEditId = 0)
    {
        if ($this->_sDisplay == 'admincp')
        {
            $sOutput = $this->_get(0, 1, $iEditId);

            return $sOutput;
        }
        else
        {
            $this->_get(0, 1, $iEditId);
            return $this->_sOutput;
        }
    }

    public function getParentBreadcrumb($sCategory)
    {
        $sCacheId = $this->cache()->set('videochannel_parent_breadcrumb_' . md5($sCategory));
        if (!($aBreadcrumb = $this->cache()->get($sCacheId)))
        {
            $sCategories = $this->getParentCategories($sCategory);

            $aCategories = $this->database()->select('*')
                    ->from($this->_sTable)
                    ->where('category_id IN(' . $sCategories . ')')
                    ->execute('getRows');

            $aBreadcrumb = $this->getCategoriesById(null, $aCategories);

            $this->cache()->save($sCacheId, $aBreadcrumb);
        }

        return $aBreadcrumb;
    }

    public function getCategoriesById($iId = null, $aCategories = null, $sModuleId = '', $iItemId = 0, $aCallback = false)
    {
        $oUrl = Phpfox::getLib('url');

        if ($aCategories === null)
        {
            $aCategories = $this->database()->select('pc.parent_id, pc.category_id, pc.name')
                    ->from(Phpfox::getT('channel_category_data'), 'pcd')
                    ->join($this->_sTable, 'pc', 'pc.category_id = pcd.category_id')
                    ->where('pc.is_active = 1 AND pcd.video_id = ' . (int) $iId)
                    ->order('pc.parent_id ASC, pc.ordering ASC')
                    ->execute('getSlaveRows');
        }

        if (count($aCategories) == 0)
        {
            return null;
        }

        // For parent module.
        $sExtend = '';

        if ($sModuleId != '' && $iItemId != 0)
        {
            $sExtend = $sModuleId . '.' . $iItemId . '.';
        }

        // Get the breadcrumb.
        $aBreadcrumb = array();

        if (count($aCategories) > 1)
        {
            foreach ($aCategories as $aCategory)
            {
                $aBreadcrumb[] = array(
                    (Core\Lib::phrase()->isPhrase($aCategory['name']) ? _p($aCategory['name']) : Phpfox::getLib('locale')->convert($aCategory['name'])),
                    Phpfox::permalink($sExtend . 'videochannel.category', $aCategory['category_id'], $aCategory['name']),
                    $aCategory['category_id']
                );
            }
        }
        else
        {
            $aBreadcrumb[] = array(
                _p($aCategories[0]['name']),
                Phpfox::permalink($sExtend . 'videochannel.category', $aCategories[0]['category_id'], $aCategories[0]['name']),
                $aCategories[0]['category_id']
            );
        }

        return $aBreadcrumb;
    }

    /**
     * Get all categories by video id. Include parent and child categories.
     * @param int $iVideoId
     * @return array
     */
    public function getCategoriesByVideoId($iVideoId = 0)
    {
        $this->database()->select('cd.category_id, c.parent_id')
                ->from(Phpfox::getT('channel_category_data'), 'cd')
                ->leftJoin(Phpfox::getT('channel_category'), 'c', 'c.category_id = cd.category_id')
                ->where('c.is_active = 1 AND video_id = ' . (int) $iVideoId);

        return $this->database()->execute('getSlaveRows');
    }

    public function getCategoryIds($iId)
    {
        $aCategories = $this->database()->select('category_id')
                ->from(Phpfox::getT('channel_category_data'))
                ->where('video_id = ' . (int) $iId)
                ->execute('getSlaveRows');

        $aCache = array();
        foreach ($aCategories as $aCategory)
        {
            $aCache[] = $aCategory['category_id'];
        }

        return implode(',', $aCache);
    }

    public function getAllCategories($sCategory)
    {
        $sCacheId = $this->cache()->set('videochannel_category_childern_' . $sCategory);

        if (!($sCategories = $this->cache()->get($sCacheId)))
        {
            $iCategory = $this->database()->select('category_id')
                    ->from($this->_sTable)
                    ->where('category_id = \'' . (int) $sCategory . '\'')
                    ->execute('getField');

            $sCategories = $this->_getChildIds($sCategory, false);
            $sCategories = rtrim($iCategory . ',' . ltrim($sCategories, $iCategory . ','), ',');

            $this->cache()->save($sCacheId, $sCategories);
        }

        return $sCategories;
    }

    public function getChildIds($iId)
    {
        return rtrim($this->_getChildIds($iId), ',');
    }

    public function getParentCategories($sCategory)
    {
        $sCacheId = $this->cache()->set('videochannel_category_parent_' . $sCategory);

        if (!($sCategories = $this->cache()->get($sCacheId)))
        {
            $iCategory = $this->database()->select('category_id')
                    ->from($this->_sTable)
                    ->where('category_id = \'' . (int) $sCategory . '\'')
                    ->execute('getField');

            $sCategories = $this->_getParentIds($iCategory);

            $sCategories = rtrim($sCategories, ',');

            $this->cache()->save($sCacheId, $sCategories);
        }

        return $sCategories;
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
        if ($sPlugin = Phpfox_Plugin::get('videochannel.service_category_category__call'))
        {
            return eval($sPlugin);
        }

        /**
         * No method or plug-in found we must throw a error.
         */
        Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
    }

    private function _getChildIds($iParentId, $bUseId = true)
    {
        $bUseId = true;

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

    private function _get($iParentId = 0, $iActive = null, $iEditId = 0)
    {
        $aCategories = $this->database()->select('*')
                ->from($this->_sTable)
                ->where('parent_id = ' . (int) $iParentId . ' AND is_active = ' . (int) $iActive . ' AND category_id <> '. (int) $iEditId)
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
                $this->_sOutput .= '<select name="val[category][' . $iParentId . ']" class="js_mp_category_list form-control" id="js_mp_id_' . $iParentId . '" >' . "\n";
                $this->_sOutput .= '<option value="">' . ($iParentId === 0 ? _p('videochannel.select') : _p('videochannel.select_a_sub_category')) . ':</option>' . "\n";
            }

            foreach ($aCategories as $iKey => $aCategory)
            {
                $aCache[] = $aCategory['category_id'];

                if ($this->_sDisplay == 'option')
                {
                    $this->_sOutput .= '<option value="' . $aCategory['category_id'] . '" class="js_mp_category_item_' . $aCategory['category_id'] . '">' . ($this->_iCnt > 0 ? str_repeat('&nbsp;', ($this->_iCnt * 2)) . ' ' : '') . (Core\Lib::phrase()->isPhrase($aCategory['name']) ? _p($aCategory['name']) : Phpfox::getLib('locale')->convert($aCategory['name'])) . '</option>' . "\n";
                    //$this->_sOutput .= $this->_get($aCategory['category_id'], $iActive);					
                }
                elseif ($this->_sDisplay == 'admincp')
                {
                    $sOutput .= '<li><img src="' . Phpfox::getLib('template')->getStyle('image', 'misc/draggable.png') . '" alt="" /> <input type="hidden" name="order[' . $aCategory['category_id'] . ']" value="' . $aCategory['ordering'] . '" class="js_mp_order" /><a href="#?id=' . $aCategory['category_id'] . '" class="js_drop_down">' . (Core\Lib::phrase()->isPhrase($aCategory['name']) ? _p($aCategory['name']) : Phpfox::getLib('locale')->convert($aCategory['name'])) . '</a>' . $this->_get($aCategory['category_id'], $iActive) . '</li>' . "\n";
                }
                else
                {
                    $this->_sOutput .= '<option value="' . $aCategory['category_id'] . '" class="js_mp_category_item_' . $aCategory['category_id'] . '">' . (Core\Lib::phrase()->isPhrase($aCategory['name']) ? _p($aCategory['name']) : Phpfox::getLib('locale')->convert($aCategory['name'])) . '</option>' . "\n";
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

    private function _getParentsUrl($iParentId, $bPassName = false)
    {
        // Cache the round we are going to increment
        static $iCnt = 0;

        // Add to the cached round
        $iCnt++;

        // Check if this is the first round
        if ($iCnt === 1)
        {
            // Cache the cache ID
            static $sCacheId = null;

            // Check if we have this data already cached
            $sCacheId = $this->cache()->set('videochannel_category_url' . ($bPassName ? '_name' : '') . '_' . $iParentId);
            if ($sParents = $this->cache()->get($sCacheId))
            {
                return $sParents;
            }
        }

        // Get the menus based on the category ID
        $aParents = $this->database()->select('category_id, name, name_url, parent_id')
                ->from($this->_sTable)
                ->where('category_id = ' . (int) $iParentId)
                ->execute('getRows');

        // Loop thur all the sub menus
        $sParents = '';
        foreach ($aParents as $aParent)
        {
            $sParents .= $aParent['name_url'] . ($bPassName ? '|' . $aParent['name'] . '|' . $aParent['category_id'] : '') . '/' . $this->_getParentsUrl($aParent['parent_id'], $bPassName);
        }

        // Save the cached based on the static cache ID
        if (isset($sCacheId))
        {
            $this->cache()->save($sCacheId, $sParents);
        }

        // Return the loop
        return $sParents;
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
        return $this->database()->select('COUNT(ccd.channel_id)')
            ->from(Phpfox::getT('channel_category_data'), 'ccd')
            ->where('ccd.category_id = '.$iCategoryId)
            ->execute('getSlaveField');

    }

    public function getSelectBox($iParentId = 0, $iActive = null)
    {
        $aCategories = $this->database()->select('*')
            ->from($this->_sTable)
            ->where('parent_id = ' . (int) $iParentId . ' AND is_active = ' . (int) $iActive )
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
                $this->_sOutput .= '<select name="val[category][' . $iParentId . ']" class="js_mp_category_list form-control" id="js_mp_id_' . $iParentId . '" >' . "\n";
                $this->_sOutput .= '<option value="">' . ($iParentId === 0 ? _p('videochannel.select') : _p('videochannel.select_a_sub_category')) . ':</option>' . "\n";
            }

            foreach ($aCategories as $iKey => $aCategory)
            {
                $aCache[] = $aCategory['category_id'];

                if ($this->_sDisplay == 'option')
                {
                    $this->_sOutput .= '<option value="' . $aCategory['category_id'] . '" class="js_mp_category_item_' . $aCategory['category_id'] . '">' . ($this->_iCnt > 0 ? str_repeat('&nbsp;', ($this->_iCnt * 2)) . ' ' : '') . (Core\Lib::phrase()->isPhrase($aCategory['name']) ? _p($aCategory['name']) : Phpfox::getLib('locale')->convert($aCategory['name'])) . '</option>' . "\n";
                    //$this->_sOutput .= $this->_get($aCategory['category_id'], $iActive);
                }
                elseif ($this->_sDisplay == 'admincp')
                {
                    $sOutput .= '<li><img src="' . Phpfox::getLib('template')->getStyle('image', 'misc/draggable.png') . '" alt="" /> <input type="hidden" name="order[' . $aCategory['category_id'] . ']" value="' . $aCategory['ordering'] . '" class="js_mp_order" /><a href="#?id=' . $aCategory['category_id'] . '" class="js_drop_down">' . (Core\Lib::phrase()->isPhrase($aCategory['name']) ? _p($aCategory['name']) : Phpfox::getLib('locale')->convert($aCategory['name'])) . '</a>' . $this->_get($aCategory['category_id'], $iActive) . '</li>' . "\n";
                }
                else
                {
                    $this->_sOutput .= '<option value="' . $aCategory['category_id'] . '" class="js_mp_category_item_' . $aCategory['category_id'] . '">' . (Core\Lib::phrase()->isPhrase($aCategory['name']) ? _p($aCategory['name']) : Phpfox::getLib('locale')->convert($aCategory['name'])) . '</option>' . "\n";
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
        return $this->_sOutput;
    }

}

?>