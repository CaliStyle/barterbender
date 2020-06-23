<?php

namespace Apps\YNC_Blogs\Service;

use Language_Service_Language;
use Core;
use Phpfox;
use Phpfox_Service;

class Category extends Phpfox_Service
{
    private $_sDisplay = 'select';
    private $_iCnt = 0;
    private $_sOutput = '';

    public function __construct()
    {
        $this->_sTable = Phpfox::getT('ynblog_category');
    }

    /**
     * @param $sDisplay
     * @return $this
     */
    public function display($sDisplay)
    {
        $this->_sDisplay = $sDisplay;
        return $this;
    }

    public function getRelatedCategories($categoryId)
    {
        $categories = db()->select('category_id')
                    ->from($this->_sTable)
                    ->where('category_id IN (SELECT yc.category_id FROM `' . Phpfox::getT('ynblog_category') .'` as yc, (SELECT @category_list := '. $categoryId .') init WHERE (FIND_IN_SET(yc.parent_id, @category_list) AND @category_list := CONCAT(@category_list, "," , yc.category_id)) OR yc.category_id = '. $categoryId .') ORDER BY 1')
                    ->execute('getSlaveRows');

        return !empty($categories) ? implode(',', array_column($categories, 'category_id')) : '';
    }

    /**
     * @param null $iSelected
     * @return mixed|string
     */
    public function get($iSelected = null)
    {
        $sCacheId = $this->cache()->set('ynblog_category_display_' . $this->_sDisplay . '_' . Phpfox::getLib('locale')->getLangId());

        if ($this->_sDisplay == 'admincp') {
            if (!($sOutput = $this->cache()->get($sCacheId))) {
                $sOutput = $this->_get(0, 1);

                $this->cache()->save($sCacheId, $sOutput);
            }

            return $sOutput;
        } else {
            if ($this->_sDisplay == 'search') {
                $this->_get(0, 1, $iSelected);
            } elseif ($this->_sDisplay == 'searchblock') {
                $this->_getBlock(0, 1, $iSelected);
            } elseif (!($this->_sOutput = $this->cache()->get($sCacheId))) {
                $this->_get(0, 1);

                $this->cache()->save($sCacheId, $this->_sOutput);
            }
            return $this->_sOutput;
        }
    }

    /**
     * @param $iParentId
     * @param null $iActive
     * @param null $iSelected
     * @return string
     */
    private function _getBlock($iParentId, $iActive = null, $iSelected = null)
    {

        $aCategories = $this->database()->select('*')->from($this->_sTable)->where('parent_id = 0' . (int)$iParentId . ' AND is_active = ' . (int)$iActive . '')->order('ordering ASC')->execute('getRows');

        if (count($aCategories)) {
            $aCache = array();

            if ($iParentId != 0) {
                $this->_iCnt++;
            }

            if ($this->_sDisplay == 'option') {

            } elseif ($this->_sDisplay == 'admincp') {
                $sOutput = '<ul>';
            } elseif ($this->_sDisplay == 'searchblock') {
                $display = (isset($iSelected) && ($this->isChild($iSelected, $iParentId) || $iSelected == $iParentId)) ? '' : 'display:none; ';
                $this->_sOutput .= '<div class="js_mp_parent_holder" id="js_mp_holder_' . $iParentId . '" ' . ($iParentId > 0 ? ' style="' . $display . 'padding:5px 0px 0px 0px;"' : '') . '>';
                $this->_sOutput .= '<select name="search[category][' . $iParentId . ']" class="js_mp_category_list form-control" id="js_mp_id_' . $iParentId . '">' . "\n";
                $this->_sOutput .= '<option value="">' . ($iParentId === 0 ? _p('select') : _p('select_a_sub_category')) . ':</option>' . "\n";
            } else {
                $this->_sOutput .= '<div class="js_mp_parent_holder" id="js_mp_holder_' . $iParentId . '" ' . ($iParentId > 0 ? ' style="display:none; padding:5px 0px 0px 0px;"' : '') . '>';
                $this->_sOutput .= '<select name="val[category][' . $iParentId . ']" class="js_mp_category_list form-control" id="js_mp_id_' . $iParentId . '">' . "\n";
                $this->_sOutput .= '<option value="">' . ($iParentId === 0 ? _p('select') : _p('select_a_sub_category')) . ':</option>' . "\n";
            }

            foreach ($aCategories as $iKey => $aCategory) {
                $aCache[] = $aCategory['category_id'];

                if ($this->_sDisplay == 'option') {
                    $this->_sOutput .= '<option value="' . $aCategory['category_id'] . '" id="js_mp_category_item_' . $aCategory['category_id'] . '">' . ($this->_iCnt > 0 ? str_repeat('&nbsp;', ($this->_iCnt * 2)) . ' ' : '') . _p($aCategory['name']) . '</option>' . "\n";
                    //$this->_sOutput .= $this->_get($aCategory['category_id'], $iActive);
                } elseif ($this->_sDisplay == 'admincp') {
                    $sOutput .= '<li><img src="' . Phpfox::getLib('template')->getStyle('image', 'misc/draggable.png') . '" alt="" /> <input type="hidden" name="order[' . $aCategory['category_id'] . ']" value="' . $aCategory['ordering'] . '" class="js_mp_order" /><a href="#?id=' . $aCategory['category_id'] . '" class="js_drop_down">' . _p($aCategory['name']) . '</a>' . $this->_get($aCategory['category_id'], $iActive, $iSelected) . '</li>' . "\n";
                } elseif ($this->_sDisplay == 'searchblock') {
                    $selected = (isset($iSelected) && ($iSelected == $aCategory['category_id'] || $this->isChild($iSelected, $aCategory['category_id']))) ? ' selected="selected"' : '';
                    $this->_sOutput .= '<option value="' . $aCategory['category_id'] . '" id="js_mp_category_item_' . $aCategory['category_id'] . '"' . $selected . ' >' . _p($aCategory['name']) . '</option>' . "\n";
                } else {
                    $this->_sOutput .= '<option value="' . $aCategory['category_id'] . '" id="js_mp_category_item_' . $aCategory['category_id'] . '">' . _p($aCategory['name']) . '</option>' . "\n";
                }
            }

            if ($this->_sDisplay == 'option') {

            } elseif ($this->_sDisplay == 'admincp') {
                $sOutput .= '</ul>';

                return $sOutput;
            } else {
                $this->_sOutput .= '</select>' . "\n";
                $this->_sOutput .= '</div>';

                foreach ($aCache as $iCateoryId) {
                    $this->_getBlock($iCateoryId, $iActive, $iSelected);
                }
            }

            $this->_iCnt = 0;
        }
    }

    /**
     * @param $iParentId
     * @param null $iActive
     * @param null $iSelected
     * @return string
     */
    private function _get($iParentId, $iActive = null, $iSelected = null)
    {
        $aCategories = $this->database()->select('*')->from($this->_sTable)->where('parent_id = ' . (int)$iParentId . ' AND is_active = ' . (int)$iActive . '')->order('ordering ASC')->execute('getRows');

        if (count($aCategories)) {
            $aCache = array();

            if ($iParentId != 0) {
                $this->_iCnt++;
            }

            if ($this->_sDisplay == 'option') {

            } elseif ($this->_sDisplay == 'admincp') {
                $sOutput = '<ul>';
            } elseif ($this->_sDisplay == 'search') {
                $display = (isset($iSelected) && ($this->isChild($iSelected, $iParentId) || $iSelected == $iParentId)) ? '' : 'display:none; ';
                $this->_sOutput .= '<div class="js_mp_parent_holder" id="js_mp_holder_' . $iParentId . '" ' . ($iParentId > 0 ? ' style="' . $display . 'padding:5px 0px 0px 0px;"' : '') . '>';
                $this->_sOutput .= '<select name="search[category][' . $iParentId . ']" class="js_mp_category_list form-control" id="js_mp_id_' . $iParentId . '">' . "\n";
                $this->_sOutput .= '<option value="">' . ($iParentId === 0 ? _p('select') : _p('select_a_sub_category')) . ':</option>' . "\n";
            } else {
                $this->_sOutput .= '<div class="js_mp_parent_holder" id="js_mp_holder_' . $iParentId . '" ' . ($iParentId > 0 ? ' style="display:none; padding:5px 0px 0px 0px;"' : '') . '>';
                $this->_sOutput .= '<select name="val[category][' . $iParentId . ']" class="js_mp_category_list form-control" id="js_mp_id_' . $iParentId . '">' . "\n";
                $this->_sOutput .= '<option value="">' . ($iParentId === 0 ? _p('select') : _p('select_a_sub_category')) . ':</option>' . "\n";
            }

            foreach ($aCategories as $iKey => $aCategory) {
                $aCache[] = $aCategory['category_id'];

                if ($this->_sDisplay == 'option') {
                    $this->_sOutput .= '<option value="' . $aCategory['category_id'] . '" id="js_mp_category_item_' . $aCategory['category_id'] . '">' . ($this->_iCnt > 0 ? str_repeat('&nbsp;', ($this->_iCnt * 2)) . ' ' : '') . _p($aCategory['name']) . '</option>' . "\n";

                    // Max 3 level in category.
                    if ($this->_iCnt < 1) {
                        $this->_sOutput .= $this->_get($aCategory['category_id'], $iActive);
                    }
                } elseif ($this->_sDisplay == 'admincp') {
                    $sIcon = '';
                    if (!empty($aCategory['image_path'])) {
                        $sIcon = Phpfox::getLib('image.helper')->display(array(
                                'server_id' => $aCategory['server_id'],
                                'path' => 'core.url_pic',
                                'file' => $aCategory['image_path'],
                                'suffix' => '_16'
                            )
                        );
                    }
                    $sOutput .= '<li><img src="' . Phpfox::getLib('template')->getStyle('image', 'misc/draggable.png') . '" alt="" /> ' . $sIcon . ' <input type="hidden" name="order[' . $aCategory['category_id'] . ']" value="' . $aCategory['ordering'] . '" class="js_mp_order" /><a href="#?id=' . $aCategory['category_id'] . '" class="js_drop_down">' . ((Phpfox::isPhrase($aCategory['name'])) ? _p($aCategory['name']) : _p($aCategory['name'])) . '</a>' . $this->_get($aCategory['category_id'], $iActive, $iSelected) . '</li>' . "\n";
                } elseif ($this->_sDisplay == 'search') {
                    $selected = (isset($iSelected) && ($iSelected == $aCategory['category_id'] || $this->isChild($iSelected, $aCategory['category_id']))) ? ' selected="selected"' : '';
                    $this->_sOutput .= '<option value="' . $aCategory['category_id'] . '" id="js_mp_category_item_' . $aCategory['category_id'] . '"' . $selected . ' >' . _p($aCategory['name']) . '</option>' . "\n";
                } else {
                    $this->_sOutput .= '<option value="' . $aCategory['category_id'] . '" id="js_mp_category_item_' . $aCategory['category_id'] . '">' . _p($aCategory['name']) . '</option>' . "\n";
                }
            }

            if ($this->_sDisplay == 'option') {

            } elseif ($this->_sDisplay == 'admincp') {
                $sOutput .= '</ul>';

                return $sOutput;
            } else {
                $this->_sOutput .= '</select>' . "\n";
                $this->_sOutput .= '</div>';

                foreach ($aCache as $iCateoryId) {
                    $this->_get($iCateoryId, $iActive, $iSelected);
                }
            }

            $this->_iCnt = 0;
        }
    }

    /**
     * @param $iId
     * @param $iParentId
     * @return bool
     */
    public function isChild($iId, $iParentId)
    {
        $aRow = $this->database()->select('*')->from($this->_sTable)->where('category_id = ' . (int)$iId . ' AND parent_id = ' . (int)$iParentId)->execute('getSlaveRow');

        if (!empty($aRow)) {
            return true;
        }

        return false;
    }

    /**
     * @param $iParentId
     * @return string
     */
    public function getChildIds($iParentId)
    {
        $sCategories = $this->_getChildIds($iParentId);
        $sCategories = trim($sCategories, ',');

        return $sCategories;
    }

    /**
     * @param $iParentId
     * @return string
     */
    private function _getChildIds($iParentId)
    {
        $aCategories = $this->database()->select('bc.category_id')
            ->from($this->_sTable, 'bc')
            ->where('bc.parent_id = ' . (int)$iParentId)
            ->execute('getRows');

        $sCategories = '';
        foreach ($aCategories as $aCategory) {
            $sCategories .= $aCategory['category_id'] . ',' . $this->_getChildIds($aCategory['category_id']);
        }

        return $sCategories;
    }

    /**
     * @param int $iParentId
     * @param int $bGetSub
     * @param int $bCareActive
     * @return array|int|string
     */
    public function getForAdmin($iParentId = 0, $bGetSub = 1, $bCareActive = 0, $currentId = 0)
    {
        $aRows = $this->database()->select('*')
            ->from($this->_sTable)
            ->where('parent_id = ' . (int)$iParentId . ' AND category_id <> ' . $currentId . ($bCareActive ? ' AND is_active = 1' : ''))
            ->order('ordering ASC')
            ->execute('getSlaveRows');

        if ($bGetSub) {
            foreach ($aRows as $iKey => $aRow) {
                $aRows[$iKey]['categories'] = $this->getForAdmin($aRow['category_id'], $bGetSub, $bCareActive, $currentId);
                $aRows[$iKey]['sub'] = $aRows[$iKey]['categories'];
            }
        }

        foreach ($aRows as $iKey => $aCategory) {
            $aRows[$iKey]['name'] = _p($aCategory['name']);
            $aRows[$iKey]['url'] = Phpfox::permalink('ynblog.category', $aCategory['category_id'],
                _p($aCategory['name']));
        }

        return $aRows;
    }

    /**
     * @param int $iParentId
     * @param int $bGetSub
     * @param int $bCareActive
     * @return array|int|string
     */
    public function getForUsers($iParentId = 0, $bGetSub = 1, $bCareActive = 0)
    {
        return $this->getForAdmin($iParentId, $bGetSub, $bCareActive);
    }

    /**
     * @param $iId
     * @return array|bool|int|string
     */
    public function getForEdit($iId)
    {
        $aRow = $this->database()->select('*')
            ->from($this->_sTable)
            ->where('category_id = ' . (int)$iId)
            ->execute('getSlaveRow');

        if (!isset($aRow['category_id'])) {
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
        foreach ($aLanguages as $aLanguage) {
            $sPhraseValue = (Core\Lib::phrase()->isPhrase($aRow['name'])) ? _p($aRow['name'], [], $aLanguage['language_id']) : $aRow['name'];
            $aRow['name_' . $aLanguage['language_id']] = $sPhraseValue;
        }

        return $aRow;
    }

    /**
     * @param $iCategoryId
     * @return array|bool|int|mixed|string
     */
    public function getCategory($iCategoryId)
    {
        $sCacheId = $this->cache()->set('ynblog_category_' . $iCategoryId);
        if (!$aCategory = $this->cache()->get($sCacheId)) {
            $aCategory = $this->database()->select('*')
                ->from($this->_sTable)
                ->where('category_id = ' . (int)$iCategoryId)
                ->execute('getSlaveRow');
            $this->cache()->save($sCacheId, $aCategory);
        }
        return (isset($aCategory['category_id']) ? $aCategory : false);
    }

    /**
     * @param $iBlogId
     * @param string $sOrder
     * @return array|int|string
     */
    public function getCategoryByBlogId($iBlogId, $sOrder = 'parent_id ASC')
    {
        return $this->database()->select('ac.*')
            ->from(Phpfox::getT('ynblog_category_data'), 'acd')
            ->join($this->_sTable, 'ac', 'ac.category_id = acd.category_id')
            ->where('blog_id = ' . (int)$iBlogId)
            ->order($sOrder)
            ->execute('getSlaveRows');
    }

    public function getParentCategoryId($iCategoryId)
    {
        $iParentId = $this->database()->select('ac.parent_id')
            ->from($this->_sTable, 'ac')
            ->where('ac.category_id = ' . (int)$iCategoryId)
            ->execute('getSlaveField');

        return $iParentId;
    }

    /**
     * @param $iBlogId
     * @return array|int|string
     */
    public function getStringCategoryByBlogId($iBlogId)
    {
        return $this->database()->select('GROUP_CONCAT(category_id)')
            ->from(Phpfox::getT('ynblog_category_data'))
            ->where('blog_id = ' . $iBlogId)
            ->execute('getSlaveField');
    }

    /**
     * @param $sId
     * @return array
     */
    public function getCategoriesById($sId)
    {
        if (!$sId) {
            return [];
        }

        $aItems = $this->database()->select('d.blog_id, d.category_id, c.name AS category_name')
            ->from(Phpfox::getT('ynblog_category_data'), 'd')
            ->join(Phpfox::getT('ynblog_category'), 'c', 'd.category_id = c.category_id')
            ->where("c.is_active = 1 AND d.blog_id IN(" . $sId . ")")
            ->execute('getSlaveRows');

        $aCategories = [];
        foreach ($aItems as $aItem) {
            $aCategories[$aItem['blog_id']][] = $aItem;
        }

        return $aCategories;
    }

    /**
     * @param $iCategoryId
     * @param bool $bGetListId
     * @return array|int|string
     */
    public function getAllItemBelongToCategory($iCategoryId, $bGetListId = false)
    {
        if (!$bGetListId)
            return $this->database()->select('COUNT(abcd.blog_id)')
                ->from(Phpfox::getT('ynblog_category_data'), 'abcd')
                ->where('abcd.category_id = ' . $iCategoryId)
                ->execute('getSlaveField');
        else
            return $this->database()->select('GROUP_CONCAT(abcd.blog_id)')
                ->from(Phpfox::getT('ynblog_category_data'), 'abcd')
                ->where('abcd.category_id = ' . $iCategoryId)
                ->execute('getSlaveField');
    }

    /**
     * @param $iCategory
     * @param int $bGetALlField
     * @return mixed|string
     */
    public function getBreadcrumCategory($iCategory, $bGetALlField = 0)
    {
        $aCategory = $this->getCategory($iCategory);

        if (empty($aCategory['parent_id'])) {
            return $aCategory['category_id'];
        } else {
            return ($this->getBreadcrumCategory($aCategory['parent_id'], $bGetALlField) . ',' . $aCategory['category_id']);
        }
    }
}
