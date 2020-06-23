<?php

/**
 * [PHPFOX_HEADER]
 */
namespace Apps\YouNet_UltimateVideos\Service\Category;

use Phpfox;
use Phpfox_Service;
use Phpfox_Pages_Category;
use Phpfox_Plugin;
use Phpfox_Error;

defined('PHPFOX') or exit('NO DICE!');

/**
 *
 *
 * @copyright      YouNet Company
 * @author         HaiNM
 * @package        Module_UltimateVideo
 * @version        4.01
 */
class Category extends Phpfox_Service
{

    private $_sDisplay = 'select';
    private $_iCnt = 0;
    protected $_sOutput = '';

    private $_categoryIdDescendants = [];

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('ynultimatevideo_category');
    }

    public function getForEdit($iId)
    {
        $aRow = $this->database()->select('*')->from($this->_sTable)->where('category_id = ' . (int)$iId)->execute('getRow');

        if (!isset($aRow['category_id'])) {
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
        foreach ($aLanguages as $aLanguage) {
            $sPhraseValue = (\Core\Lib::phrase()->isPhrase($aRow['title'])) ? _p($aRow['title'], [], $aLanguage['language_id']) : $aRow['title'];
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
        $sCacheId = $this->cache()->set('ynultimatevideo_category_display_' . $this->_sDisplay . '_' . Phpfox::getLib('locale')->getLangId());

        if ($this->_sDisplay == 'admincp') {
            $sOutput = $this->_get(0, 1);
            return $sOutput;
        } else {
            if ($this->_sDisplay == 'search') {
                $this->_get(0, 1, $iSelected);
            } elseif (!($this->_sOutput = $this->cache()->get($sCacheId))) {
                $this->_get(0, 1);

                //$this->cache()->save($sCacheId, $this->_sOutput);
            }

            return $this->_sOutput;
        }
    }

    private function _get($iParentId, $iActive = null, $iSelected = null)
    {
        $aCategories = $this->database()->select('*')->from($this->_sTable)->where('parent_id =' . (int)$iParentId . ' AND is_active = ' . (int)$iActive . '')->order('ordering ASC')->execute('getRows');

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
                $this->_sOutput .= '<select name="search[category][' . $iParentId . ']" class="js_mp_category_list" id="js_mp_id_' . $iParentId . '">' . "\n";
                $this->_sOutput .= '<option value="">' . ($iParentId === 0 ? _p('Select') : _p('select_a_sub_category')) . ':</option>' . "\n";
            } else {
                $this->_sOutput .= '<div class="js_mp_parent_holder" id="js_mp_holder_' . $iParentId . '" ' . ($iParentId > 0 ? ' style="display:none; padding:5px 0px 0px 0px;"' : '') . '>';
                $this->_sOutput .= '<select name="val[category][' . $iParentId . ']" class="js_mp_category_list form-control" id="js_mp_id_' . $iParentId . '">' . "\n";
                $this->_sOutput .= '<option value="">' . ($iParentId === 0 ? _p('Select') : _p('select_a_sub_category')) . ':</option>' . "\n";
            }

            foreach ($aCategories as $iKey => $aCategory) {
                $aCache[] = $aCategory['category_id'];

                if ($this->_sDisplay == 'option') {
                    $this->_sOutput .= '<option value="' . $aCategory['category_id'] . '" id="js_mp_category_item_' . $aCategory['category_id'] . '">' . ($this->_iCnt > 0 ? str_repeat('&nbsp;', ($this->_iCnt * 2)) . ' ' : '') . Phpfox::getLib('locale')->convert(\Core\Lib::phrase()->isPhrase($aCategory['title']) ? _p($aCategory['title']) : $aCategory['title']) . '</option>' . "\n";
                    //$this->_sOutput .= $this->_get($aCategory['category_id'], $iActive);
                } elseif ($this->_sDisplay == 'admincp') {
                    $sOutput .= '<li><img src="' . Phpfox::getLib('template')->getStyle('image', 'misc/draggable.png') . '" alt="" /> <input type="hidden" name="order[' . $aCategory['category_id'] . ']" value="' . $aCategory['ordering'] . '" class="js_mp_order" /><a href="#?id=' . $aCategory['category_id'] . '" class="js_drop_down">' . Phpfox::getLib('locale')->convert($aCategory['title']) . '</a>' . $this->_get($aCategory['category_id'], $iActive, $iSelected) . '</li>' . "\n";
                } elseif ($this->_sDisplay == 'search') {
                    $selected = (isset($iSelected) && ($iSelected == $aCategory['category_id'] || $this->isChild($iSelected, $aCategory['category_id']))) ? ' selected="selected"' : '';
                    $this->_sOutput .= '<option value="' . $aCategory['category_id'] . '" id="js_mp_category_item_' . $aCategory['category_id'] . '"' . $selected . ' >' . Phpfox::getLib('locale')->convert(\Core\Lib::phrase()->isPhrase($aCategory['title']) ? _p($aCategory['title']) : $aCategory['title']) . '</option>' . "\n";
                } else {
                    $this->_sOutput .= '<option value="' . $aCategory['category_id'] . '" id="js_mp_category_item_' . $aCategory['category_id'] . '">' . Phpfox::getLib('locale')->convert(\Core\Lib::phrase()->isPhrase($aCategory['title']) ? _p($aCategory['title']) : $aCategory['title']) . '</option>' . "\n";
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

        return null;
    }

    /**
     * get categories by Id or list of Ids seperated by comma
     * @by minhta
     * @param string $iVideoId purpose
     * @return
     */
    public function getCategoriesByVideoId($iVideoId)
    {
        $aCategories = $this->database()->select('c.parent_id, c.category_id, c.title')
            ->from(Phpfox::getT('ynultimatevideo_videos'), 'v')
            ->join($this->_sTable, 'c', 'c.category_id = v.category_id')
            ->where('v.video_id = ' . (int)$iVideoId)
            ->execute('getSlaveRows');

        if (!count($aCategories)) {
            return null;
        }

        $aBreadcrumb = array();
        if (count($aCategories) > 1) {
            foreach ($aCategories as $aCategory) {
                $aBreadcrumb[] = array(Phpfox::getLib('locale')->convert(\Core\Lib::phrase()->isPhrase($aCategory['title']) ? _p($aCategory['title']) : $aCategory['title']), Phpfox::permalink('ultimatevideo.category', $aCategory['category_id'], $aCategory['title']));
            }
        } else {
            $aBreadcrumb[] = array(Phpfox::getLib('locale')->convert(\Core\Lib::phrase()->isPhrase($aCategories[0]['title']) ? _p($aCategories[0]['title']) : $aCategories[0]['title']), Phpfox::permalink('ultimatevideo.category', $aCategories[0]['category_id'], $aCategories[0]['title']));
        }

        return $aBreadcrumb;
    }

    public function getCategoriesByPlaylistId($iPlaylistId)
    {
        $aCategories = $this->database()->select('c.parent_id, c.category_id, c.title')
            ->from(Phpfox::getT('ynultimatevideo_playlists'), 'p')
            ->join($this->_sTable, 'c', 'c.category_id = p.category_id')
            ->where('p.playlist_id = ' . (int)$iPlaylistId)
            ->execute('getSlaveRows');

        if (!count($aCategories)) {
            return null;
        }

        $aBreadcrumb = array();
        if (count($aCategories) > 1) {
            foreach ($aCategories as $aCategory) {
                $aBreadcrumb[] = array(Phpfox::getLib('locale')->convert(\Core\Lib::phrase()->isPhrase($aCategory['title']) ? _p($aCategory['title']) : $aCategory['title']), Phpfox::permalink('ultimatevideo.playlist.category', $aCategory['category_id'], $aCategory['title']));
            }
        } else {
            $aBreadcrumb[] = array(Phpfox::getLib('locale')->convert(\Core\Lib::phrase()->isPhrase($aCategories[0]['title']) ? _p($aCategories[0]['title']) : $aCategories[0]['title']), Phpfox::permalink('ultimatevideo.playlist.category', $aCategories[0]['category_id'], $aCategories[0]['title']));
        }

        return $aBreadcrumb;
    }

    public function getCategoriesBreadcrumbByVideoId($iVideoId)
    {
        $aCategories = $this->getCategoriesByVideoId($iVideoId);
        $sCategories = '';
        if (isset($aCategories)) {
            foreach ($aCategories as $iKey => $aCategory) {
                $sCategories .= ' <a href="' . $aCategory[1] . '">' . $aCategory[0] . '</a>';
            }
        }

        return $sCategories;
    }

    public function getCategoriesBreadcrumbByPlaylistId($iPlaylistId)
    {
        $aCategories = $this->getCategoriesByPlaylistId($iPlaylistId);
        $sCategories = '';
        if (isset($aCategories)) {
            foreach ($aCategories as $iKey => $aCategory) {
                $sCategories .= ' <a href="' . $aCategory[1] . '">' . $aCategory[0] . '</a>';
            }
        }

        return $sCategories;
    }

    /**
     * @TODO: LIST OF CATEGORY TO SELECT
     * <pre>
     * PhpFox::getService('ynultimatevideo.category')->getCategories($aConds  = array() , $sSort = string);
     * </pre>
     * @by datlv
     * @param string, array $aConds condition for query
     * @param string $sSort condition for sort in query
     * @return array list of all categories
     */
    public function getCategories($aConds = 'c.parent_id = 0', $sSort = 'c.title ASC', $iLimit = 0)
    {
        $this->database()
            ->select('c.category_id, c.title')
            ->from(Phpfox::getT('ynultimatevideo_category'), 'c')
            ->where($aConds)
            ->group('c.category_id')
            ->order($sSort);


        if ($iLimit) {
            $this->database()->limit($iLimit);

        }

        $aItems = $this->database()->execute('getSlaveRows');
        return $aItems;
    }

    public function getForBrowse($iCategoryId = null)
    {
        $aCategories = $this->database()->select('mc.category_id, mc.title, mc.title as name')->from($this->_sTable, 'mc')->where('mc.parent_id = ' . (int) $iCategoryId . ' AND mc.is_active = 1')->order('mc.ordering ASC')->execute('getRows');

        foreach ($aCategories as $iKey => $aCategory) {
            $aCategories[$iKey]['url'] = Phpfox::permalink('ultimatevideo.category', $aCategory['category_id'], $aCategory['title']);

            $aCategories[$iKey]['sub'] = $this->database()->select('mc.category_id, mc.title, mc.title as name')->from($this->_sTable, 'mc')->where('mc.parent_id = ' . $aCategory['category_id'] . ' AND mc.is_active = 1')->order('mc.ordering ASC')->execute('getRows');

            foreach ($aCategories[$iKey]['sub'] as $iSubKey => $aSubCategory) {
                $aCategories[$iKey]['sub'][$iSubKey]['url'] = Phpfox::permalink('ultimatevideo.category', $aSubCategory['category_id'], $aSubCategory['title']);
            }
        }

        return $aCategories;
    }

    public function getForBrowsePlaylist($iCategoryId = null)
    {
        $link = 'ultimatevideo.playlist.category';

        $aCategories = $this->database()->select('mc.category_id, mc.title, mc.title as name')->from($this->_sTable, 'mc')->where('mc.parent_id = ' . (int) $iCategoryId . ' AND mc.is_active = 1')->order('mc.ordering ASC')->execute('getRows');

        foreach ($aCategories as $iKey => $aCategory) {
            $aCategories[$iKey]['url'] = Phpfox::permalink($link, $aCategory['category_id'], $aCategory['title']);

            //if ($sCategory === null)
            {
                $aCategories[$iKey]['sub'] = $this->database()->select('mc.category_id, mc.title, mc.title as name')->from($this->_sTable, 'mc')->where('mc.parent_id = ' . $aCategory['category_id'] . ' AND mc.is_active = 1')->order('mc.ordering ASC')->execute('getRows');

                foreach ($aCategories[$iKey]['sub'] as $iSubKey => $aSubCategory) {
                    $aCategories[$iKey]['sub'][$iSubKey]['url'] = Phpfox::permalink($link, $aSubCategory['category_id'], $aSubCategory['title']);
                }
            }
        }

        return $aCategories;
    }

    public function isChild($iId, $iParentId)
    {
        $aRow = $this->database()->select('*')->from($this->_sTable)->where('category_id = ' . (int)$iId . ' AND parent_id = ' . (int)$iParentId)->execute('getSlaveRow');

        if (!empty($aRow)) {
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
        $aCategories = $this->database()->select('c.category_id')
            ->from($this->_sTable, 'c')
            ->where('c.parent_id = ' . (int)$iParentId)
            ->execute('getRows');

        $sCategories = '';
        foreach ($aCategories as $aCategory) {
            $sCategories .= $aCategory['category_id'] . ',' . $this->_getChildIds($aCategory['category_id']);
        }

        return $sCategories;
    }

    public function getParentCategory()
    {
        $aCategories = $this->database()->select('*')
            ->from($this->_sTable, 'yc')
            ->where('yc.parent_id = 0')
            ->execute('getSlaveRows');
        return $aCategories;
    }

    public function getParentCategoryId($iCategoryId)
    {
        $iParentId = $this->database()->select('yc.parent_id')
            ->from($this->_sTable, 'yc')
            ->where('yc.category_id = ' . (int)$iCategoryId)
            ->execute('getSlaveField');
        return $iParentId;
    }

    public function getCategoryIdDescendants($id)
    {
        if (isset($this->_categoryIdDescendants[$id]))
            return $this->_categoryIdDescendants[$id];

        $this->_categoryIdDescendants[$id] = get_from_cache(['ultimatevideo.getCategoryIdDescendants', $id], function () use ($id) {
            $aCategoryIds = [intval($id)];
            $total = count($aCategoryIds);
            for ($i = 0; $i < 4; ++$i) {
                $check = implode(',', $aCategoryIds);
                $aCategoryIds = array_map(function ($tmp) {
                    return $tmp['category_id'];
                }, $this->database()
                    ->select('c.category_id')
                    ->from($this->_sTable, 'c')
                    ->where('c.parent_id IN( ' . $check . ') OR c.category_id IN (' . $check . ')')
                    ->execute('getRows'));

                if ($total < count($aCategoryIds)) {
                    $total = count($aCategoryIds);
                } else {
                    break;
                }
            }
            return implode(',', $aCategoryIds);
        }, 1);

        return $this->_categoryIdDescendants[$id];
    }

    public function getCategoryAncestors($iCategoryId, $iLimit = 3)
    {
        $result = [];
        do {
            if ($iCategoryId) {
                $row = $this->database()
                    ->select('ca.*')
                    ->from($this->_sTable, 'ca')
                    ->where('ca.category_id=' . intval($iCategoryId))
                    ->execute('getSlaveRow');

                if ($row) {
                    array_unshift($result, $row);
                    $iCategoryId = intval($row['parent_id']);
                } else {
                    $iCategoryId = 0;
                }
            }
        } while ($iCategoryId > 0 && --$iLimit > 0);

        return $result;

    }

    /**
     * @param $iCategoryId
     * @return mixed
     */
    public function getCategoryById($iCategoryId)
    {
        return $this->database()
            ->select('ca.*')
            ->from($this->_sTable, 'ca')
            ->where('ca.category_id=' . intval($iCategoryId))
            ->execute('getSlaveRow');
    }

    public function getCustomGroup($iCateoryId)
    {
        return $this->database()
            ->select('ycg.*')
            ->from(Phpfox::getT('ynultimatevideo_category_customgroup_data'), 'yccd')
            ->join(Phpfox::getT('ynultimatevideo_custom_group'), 'ycg', 'yccd.group_id = ycg.group_id')
            ->where('category_id = ' . (int)$iCateoryId)
            ->execute('getRows');
    }

    public function getForAdmin($iParentId = 0, $bGetSub = 1)
    {
        $aRows = $this->database()->select('*')
            ->from($this->_sTable)
            ->where('parent_id = ' . (int)$iParentId)
            ->order('ordering ASC')
            ->execute('getSlaveRows');

        foreach ($aRows as $iKey => $aRow) {
            if ($bGetSub) {
                $aRows[$iKey]['numberItems'] = $this->getAllItemBelongToCategory($aRow['category_id']);
                $aRows[$iKey]['categories'] = $this->getForAdmin($aRow['category_id']);
            }
        }

        return $aRows;
    }

    public function getAllItemBelongToCategory($iCategoryId)
    {
        return $this->database()->select('COUNT(ccd.video_id)')
            ->from(Phpfox::getT('ynultimatevideo_videos'), 'ccd')
            ->where('ccd.category_id = ' . $iCategoryId)
            ->execute('getSlaveField');

    }

    public function getRandomCategory()
    {
        return $this->database()->select('category_id')
            ->from($this->_sTable)
            ->order('RAND()')
            ->limit(1)
            ->execute('getField');
    }

    /**
     * @param $sMethod
     * @param $aArguments
     * @return mixed|null
     */
    public function __call($sMethod, $aArguments)
    {
        /**
         * Check if such a plug-in exists and if it does call it.
         */
        if ($sPlugin = Phpfox_Plugin::get('ynultimatevideo.service_category_category__call')) {
            return eval($sPlugin);
        }

        /**
         * No method or plug-in found we must throw a error.
         */
        Phpfox_Error::trigger('Call to undefined method ' . __class__ . '::' . $sMethod . '()', E_USER_ERROR);

        return null;
    }

    public function getForUsers($iParentId = 0, $bGetSub = 1, $bCareActive = 1, $iCacheTime = 5, $selected = array())
    {
        return $this->getCategoriesForAdmin($iParentId, $bGetSub, $bCareActive, 0, 0, $iCacheTime, $selected);
    }

    public function getCategoriesForAdmin(
        $iParentId = 0,
        $bGetSub = 1,
        $bCareActive = 0,
        $notInclude = 0,
        $isFirst = 1,
        $iCacheTime = 5,
        $selected = array()
    )
    {
        if ($isFirst) {
            $hash = md5($iParentId === null ? '' : '_' . $iParentId) . (empty($bGetSub) ? '' : '_' . $bGetSub) . (empty($bCareActive) ? '' : '_' . $bCareActive) . (empty($notInclude) ? '' : '_' . $notInclude);
            $sCacheId = $this->cache()->set('ultimatevideo_category_' . $hash . '_' . Phpfox::getLanguageId());
            Phpfox::getLib('cache')->group('ultimatevideo', $sCacheId);
        }
        if (!isset($sCacheId) || !($aRows = $this->cache()->get($sCacheId, $iCacheTime))) {
            $aRows = db()->select('*')
                ->from($this->_sTable)
                ->where('parent_id = ' . (int)$iParentId . ($bCareActive ? ' AND is_active = 1' : '') . ' AND category_id <> ' . $notInclude)
                ->order('ordering ASC')
                ->execute('getSlaveRows');

            if ($bGetSub) {
                foreach ($aRows as $iKey => $aRow) {
                    $aRows[$iKey]['sub'] = $this->getCategoriesForAdmin($aRow['category_id'], 1, $bCareActive, $notInclude, 0, 0, $selected);
                }
            }
            if ($isFirst && isset($sCacheId)) {
                $this->cache()->save($sCacheId, $aRows);
            }
        }

        if (is_array($aRows)) {
            foreach ($aRows as $iKey => $aCategory) {
                $aRows[$iKey]['name'] = _p($aCategory['title']);
                $aRows[$iKey]['url'] = Phpfox::permalink('ultimatevideo.category', $aCategory['category_id'],
                    _p($aCategory['title']));
                $aRows[$iKey]['used'] = $aCategory['used'];
                if (in_array($aCategory['category_id'], $selected)) {
                    $aRows[$iKey]['active'] = 1;
                }
            }
        }
        return $aRows;
    }
}
