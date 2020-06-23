<?php

namespace Apps\P_AdvMarketplace\Service\Category;

defined('PHPFOX') or exit('NO DICE!');

use Phpfox;
use Phpfox_Service;
use Phpfox_Error;
use Phpfox_Plugin;
use Phpfox_Locale;

class Category extends Phpfox_Service
{
    private $_sOutput = '';

    private $_iCnt = 0;

    private $_sDisplay = 'select';

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('advancedmarketplace_category');
    }

    public function getParentCategoriesList()
    {
        $cacheId = $this->cache()->set('advmarketplace_parent_categories');
        if (($aRows = $this->cache()->get($cacheId)) === false) {
            $aRows = db()->select('category_id, name')
                ->from($this->_sTable)
                ->where('parent_id = 0')
                ->execute('getSlaveRows');
            $this->cache()->saveBoth($cacheId, $aRows);
        }
        return $aRows;
    }

    public function getForEdit($iId)
    {
        $aRow = $this->database()->select('*')->from($this->_sTable)->where('category_id = ' . (int)$iId)->execute('getRow');
        if (!isset($aRow['category_id'])) {
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
        $aLanguages = Phpfox::getService('language')->getAll();
        foreach ($aLanguages as $aLanguage) {
            $sPhraseValue = (\Core\Lib::phrase()->isPhrase($aRow['name'])) ? _p($aRow['name'], [],
                $aLanguage['language_id']) : $aRow['name'];
            $aRow['name_' . $aLanguage['language_id']] = $sPhraseValue;
        }

        return $aRow;
    }

    public function getForBrowse($iCategoryId = null)
    {
        (($sPlugin = Phpfox_Plugin::get('advancedmarketplace.service_category_getforbrowse')) ? eval($sPlugin) : false);

        $cacheObject = $this->cache();
        $cacheId = $cacheObject->set('advmarketplace_categories_browse');

        if(($aCategories = $cacheObject->get($cacheId)) === false) {
            $aCategories = $this->database()->select('mc.category_id, mc.name')
                ->from($this->_sTable, 'mc')
                ->where('mc.parent_id = ' . ($iCategoryId === null ? '0' : (int)$iCategoryId) . ' AND mc.is_active = 1')
                ->order('mc.ordering ASC')
                ->execute('getSlaveRows');
            if(!empty($aCategories)) {
                foreach ($aCategories as $iKey => $aCategory) {
                    $aCategories[$iKey]['url'] = Phpfox::permalink('advancedmarketplace.search.category',
                        $aCategory['category_id'],
                        (Phpfox::getLib('parse.input')->cleanTitle(Phpfox_Locale::instance()->convert($aCategory['name']))));
                    $aCategories[$iKey]['name'] = (Phpfox::getLib('parse.input')->clean(Phpfox_Locale::instance()->convert($aCategory['name'])));
                    $aCategories[$iKey]['sub'] = $this->database()->select('mc.category_id, mc.name')
                        ->from($this->_sTable, 'mc')
                        ->where('mc.parent_id = ' . $aCategory['category_id'] . ' AND mc.is_active = 1')
                        ->order('mc.ordering ASC')
                        ->execute('getSlaveRows');

                    foreach ($aCategories[$iKey]['sub'] as $iSubKey => $aSubCategory) {
                        $aCategories[$iKey]['sub'][$iSubKey]['url'] = Phpfox::permalink('advancedmarketplace.search.category',
                            $aSubCategory['category_id'],
                            (Phpfox::getLib('parse.input')->cleanTitle(Phpfox_Locale::instance()->convert($aSubCategory['name']))));
                        $aCategories[$iKey]['sub'][$iSubKey]['name'] = (Phpfox::getLib('parse.input')->clean(Phpfox_Locale::instance()->convert($aSubCategory['name'])));
                    }
                }
                $cacheObject->saveBoth($cacheId, $aCategories);
                $cacheObject->group('advmarketplace_category', $cacheId);
            }
        }

        return $aCategories;
    }

    public function getTopCategories($iLimit)
    {
        db()->select('COUNT(*) AS total_posts, ac.category_id')
            ->from(Phpfox::getT('advancedmarketplace_category'), 'ac')
            ->join(Phpfox::getT('advancedmarketplace_category_data'), 'acd', 'acd.category_id = ac.category_id')
            ->join(Phpfox::getT('advancedmarketplace'), 'a', 'a.listing_id = acd.listing_id')
            ->where('ac.is_active = 1 AND ac.parent_id = 0 AND a.view_id = 0 AND a.post_status = 1 AND a.privacy = 0 AND (a.has_expiry = 0 OR a.expiry_date > ' . PHPFOX_TIME . ')')
            ->order('total_posts DESC')
            ->limit($iLimit)
            ->group('ac.category_id')
            ->union()
            ->unionFrom('t');
        $aTopCategories = db()->select('t.total_posts, t.category_id, ac.name, ac.image_path, ac.server_id')
            ->join(Phpfox::getT('advancedmarketplace_category'), 'ac', 't.category_id = ac.category_id')
            ->execute('getSlaveRows');

        return $aTopCategories;
    }

    public function display($sDisplay)
    {
        $this->_sDisplay = $sDisplay;

        return $this;
    }

    public function get()
    {
        if ($this->_sDisplay == 'admincp') {
            $sOutput = $this->_get(0, 1);

            return $sOutput;
        } else {
            $iEditId = $this->request()->getInt('id') ? $this->request()->getInt('id') : null;
            $this->_get(0, 1, $iEditId);

            return $this->_sOutput;
        }
    }

    public function getParentBreadcrumb($sCategory)
    {
        $aBreadcrumb = array();
        $sCategories = $this->getParentCategories($sCategory);
        if ($sCategories) {
            $aCategories = $this->database()->select('*')
                ->from($this->_sTable)
                ->where('category_id IN(' . $sCategories . ')')
                ->execute('getRows');
            $aBreadcrumb = $this->getCategoriesById(null, $aCategories);
        }

        return $aBreadcrumb;
    }

    public function getCategoriesById($iId = null, &$aCategories = null)
    {
        if ($aCategories === null) {
            $aCategories = $this->database()->select('pc.parent_id, pc.category_id, pc.name')
                ->from(Phpfox::getT('advancedmarketplace_category_data'), 'pcd')
                ->join($this->_sTable, 'pc', 'pc.category_id = pcd.category_id')
                ->where('pcd.listing_id = ' . (int)$iId)
                ->order('pc.parent_id ASC, pc.ordering ASC')
                ->execute('getSlaveRows');
        }

        if (!count($aCategories)) {
            return null;
        }

        $aBreadcrumb = array();
        if (count($aCategories) > 1) {
            foreach ($aCategories as $aCategory) {
                $aBreadcrumb[] = array(
                    (Phpfox::getLib('parse.input')->clean(Phpfox_Locale::instance()->convert($aCategory['name']))),
                    Phpfox::permalink('advancedmarketplace.search.category', $aCategory['category_id'],
                        (Phpfox::getLib('parse.input')->clean(Phpfox_Locale::instance()->convert($aCategory['name']))))
                );
            }
        } else {
            $aBreadcrumb[] = array(
                (Phpfox::getLib('parse.input')->clean(Phpfox_Locale::instance()->convert($aCategories[0]['name']))),
                Phpfox::permalink('advancedmarketplace.search.category', $aCategories[0]['category_id'],
                    (Phpfox::getLib('parse.input')->clean(Phpfox_Locale::instance()->convert($aCategories[0]['name']))))
            );
        }

        return $aBreadcrumb;
    }

    public function getCategoryIds($iId)
    {
        $aCategories = $this->database()->select('category_id')
            ->from(Phpfox::getT('advancedmarketplace_category_data'))
            ->where('listing_id = ' . (int)$iId)
            ->execute('getSlaveRows');

        $aCache = array();
        foreach ($aCategories as $aCategory) {
            $aCache[] = $aCategory['category_id'];
        }

        return implode(',', $aCache);
    }

    public function getCategoryId($iId)
    {
        $aCategories = $this->database()->select('category_id')
            ->from(Phpfox::getT('advancedmarketplace_category_data'))
            ->where('listing_id = ' . (int)$iId)
            ->execute('getSlaveRows');

        return $aCategories[count($aCategories) - 1];
    }

    public function getAllCategories($sCategory)
    {
        $iCategory = $this->_getCorrectId($sCategory);
        $sCategories = $this->_getChildIds($iCategory);
        $sCategories = rtrim($iCategory . ',' . ltrim($sCategories, $iCategory . ','), ',');

        return $sCategories;
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
                $aRows[$iKey]['name'] = Phpfox::getLib('parse.input')->clean(Phpfox_Locale::instance()->convert($aRow['name']));
            }
        }

        return $aRows;
    }

    public function getAllItemBelongToCategory($iCategoryId)
    {
        return $this->database()->select('COUNT(dcd.listing_id)')
            ->from(Phpfox::getT('advancedmarketplace_category_data'), 'dcd')
            ->where('dcd.category_id = ' . $iCategoryId)
            ->execute('getSlaveField');
    }

    public function getChildIds($iId)
    {
        return rtrim($this->_getChildIds($iId), ',');
    }

    public function getParentIds($iId)
    {
        return rtrim($this->_getParentIds($iId), ',');
    }

    public function getParentCategoryId($iId)
    {
        $iCategory = $this->database()->select('parent_id')
            ->from($this->_sTable)
            ->where('category_id = \'' . (int)$iId . '\'')
            ->execute('getField');

        return $iCategory;
    }

    public function getParentCategories($sCategory)
    {
        $iCategory = $this->database()->select('category_id')
            ->from($this->_sTable)
            ->where('category_id = \'' . (int)$sCategory . '\'')
            ->execute('getField');
        $sCategories = $this->_getParentIds($iCategory);
        $sCategories = rtrim($sCategories, ',');

        return $sCategories;
    }

    /**
     * If a call is made to an unknown method attempt to connect
     * it to a specific plug-in with the same name thus allowing
     * plug-in developers the ability to extend classes.
     *
     * @param string $sMethod is the name of the method
     * @param array $aArguments is the array of arguments of being passed
     * @return mixed
     */
    public function __call($sMethod, $aArguments)
    {
        /**
         * Check if such a plug-in exists and if it does call it.
         */
        if ($sPlugin = Phpfox_Plugin::get('advancedmarketplace.service_category_category__call')) {
            return eval($sPlugin);
        }

        /**
         * No method or plug-in found we must throw a error.
         */
        Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
    }

    private function _getChildIds($iParentId, $bUseId = true)
    {
        $aCategories = $this->database()->select('pc.name, pc.category_id')
            ->from($this->_sTable, 'pc')
            ->where(($bUseId ? 'pc.parent_id = ' . (int)$iParentId . '' : 'pc.name_url = \'' . $this->database()->escape($iParentId) . '\''))
            ->execute('getRows');

        $sCategories = '';
        foreach ($aCategories as $aCategory) {
            $sCategories .= $aCategory['category_id'] . ',' . $this->_getChildIds($aCategory['category_id']) . '';
        }

        return $sCategories;
    }

    private function _getParentIds($iId)
    {
        $aCategories = $this->database()->select('pc.category_id, pc.parent_id')
            ->from($this->_sTable, 'pc')
            ->where('pc.category_id = ' . (int)$iId)
            ->execute('getRows');

        $sCategories = '';
        foreach ($aCategories as $aCategory) {
            $sCategories .= $aCategory['category_id'] . ',' . $this->_getParentIds($aCategory['parent_id']) . '';
        }

        return $sCategories;
    }

    private function _get($iParentId, $iActive = null, $iEditId = null)
    {
        $aCategories = $this->database()->select('*')
            ->from($this->_sTable)
            ->where('parent_id = ' . (int)$iParentId . ' AND is_active = ' . (int)$iActive . '')
            ->order('ordering ASC')
            ->execute('getRows');

        if (count($aCategories)) {
            $aCache = array();

            if ($iParentId != 0) {
                $this->_iCnt++;
            }

            if ($this->_sDisplay == 'option') {

            } elseif ($this->_sDisplay == 'admincp') {
                $sOutput = '<ul>';
            } else {
                $this->_sOutput .= '<div class="js_mp_parent_holder" id="js_mp_holder_' . $iParentId . '" ' . ($iParentId > 0 ? ' style="display:none; padding:5px 0px 0px 0px;"' : '') . '>';
                $this->_sOutput .= '<select name="val[category][]" class="js_mp_category_list form-control w-auto" id="js_mp_id_' . $iParentId . '">' . "\n";
                $this->_sOutput .= '<option value="">' . ($iParentId === 0 ? _p('advancedmarketplace.select') : _p('advancedmarketplace.select_a_sub_category')) . ':</option>' . "\n";
            }

            foreach ($aCategories as $iKey => $aCategory) {
                if ($aCategory['category_id'] == $iEditId) {
                    continue;
                }

                $aCache[] = $aCategory['category_id'];

                if ($this->_sDisplay == 'option') {
                    $this->_sOutput .= '<option value="' . $aCategory['category_id'] . '" id="js_mp_category_item_' . $aCategory['category_id'] . '">' . ($this->_iCnt > 0 ? str_repeat('-',
                                ($this->_iCnt)) . ' ' : '') . (Phpfox::getLib('parse.input')->clean(Phpfox_Locale::instance()->convert($aCategory['name']))) . '</option>' . "\n";
                    $this->_sOutput .= $this->_get($aCategory['category_id'], $iActive, $iEditId);
                } elseif ($this->_sDisplay == 'admincp') {
                    $sOutput .= '<li><img src="' . Phpfox::getLib('template')->getStyle('image',
                            'misc/draggable.png') . '" alt="" /> <input type="hidden" name="order[' . $aCategory['category_id'] . ']" value="' . $aCategory['ordering'] . '" class="js_mp_order" /><a href="#?id=' . $aCategory['category_id'] . '" class="js_drop_down">' . (Phpfox::getLib('parse.input')->clean(Phpfox_Locale::instance()->convert($aCategory['name']))) . '</a>' . $this->_get($aCategory['category_id'],
                            $iActive) . '</li>' . "\n";
                } else {
                    $this->_sOutput .= '<option value="' . $aCategory['category_id'] . '" id="js_mp_category_item_' . $aCategory['category_id'] . '">' . (Phpfox::getLib('parse.input')->clean(Phpfox_Locale::instance()->convert($aCategory['name']))) . '</option>' . "\n";
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
        if ($iCnt === 1) {
            // Cache the cache ID
            static $sCacheId = null;

            // Check if we have this data already cached
            $sCacheId = $this->cache()->set('advancedmarketplace_category_url' . ($bPassName ? '_name' : '') . '_' . $iParentId);
            if ($sParents = $this->cache()->get($sCacheId)) {
                return $sParents;
            }
        }

        // Get the menus based on the category ID
        $aParents = $this->database()->select('category_id, name, name_url, parent_id')
            ->from($this->_sTable)
            ->where('category_id = ' . (int)$iParentId)
            ->execute('getRows');

        // Loop thur all the sub menus
        $sParents = '';
        foreach ($aParents as $aParent) {
            $sParents .= $aParent['name_url'] . ($bPassName ? '|' . (Phpfox::getLib('parse.input')->cleanTitle(Phpfox_Locale::instance()->convert($aParent['name']))) . '|' . $aParent['category_id'] : '') . '/' . $this->_getParentsUrl($aParent['parent_id'],
                    $bPassName);
        }

        // Save the cached based on the static cache ID
        if (isset($sCacheId)) {
            $this->cache()->save($sCacheId, $sParents);
        }

        // Return the loop
        return $sParents;
    }

    private function _getCorrectId($sCategory)
    {
        if (preg_match('/\./i', $sCategory)) {
            $aParts = explode('.', $sCategory);
            $iCategoryId = 0;
            for ($i = 0; $i < count($aParts); $i++) {
                $iCategoryId = $this->database()->select('category_id')
                    ->from($this->_sTable)
                    ->where(($iCategoryId > 0 ? 'parent_id = ' . (int)$iCategoryId . ' AND ' : ' parent_id = 0 AND ') . 'name_url = \'' . $this->database()->escape($aParts[$i]) . '\'')
                    ->execute('getField');
            }
        } else {
            $iCategoryId = $this->database()->select('category_id')
                ->from($this->_sTable)
                ->where('parent_id = 0 AND name_url = \'' . $this->database()->escape($sCategory) . '\'')
                ->execute('getField');
        }

        return $iCategoryId;
    }

    public function getChildIdsOfCats($aCategories)
    {
        $iCategoryId = $aCategories[0];
        foreach ($aCategories as $iCatId) {
            $iChildId = $this->_getChildIds($iCatId['category_id']);
            if ($iChildId == '') {
                $iCategoryId = $iCatId;

                return $iCategoryId;
            }
        }

        return $iCategoryId;
    }

    // nhanlt
    public function getCategorieStructure($returnAllStructure = null)
    {
        $cats = Phpfox::getLib("database")
            ->select("*")
            ->from(Phpfox::getT("advancedmarketplace_category"))
            ->execute("getRows");

        $aCategories = array();
        $all = array();
        $dangling = array();

        // Initialize arrays
        foreach ($cats as $entry) {
            $entry['children'] = array();
            $id = $entry['category_id'];
            $entry['url'] = Phpfox::permalink('advancedmarketplace.search.category', $entry['category_id'],
                $entry['name']);

            // If this is a top-level node, add it to the output immediately
            if ($entry['parent_id'] == 0) {
                $all[$id] = $entry;
                $aCategories[] =& $all[$id];

                // If this isn't a top-level node, we have to process it later
            } else {
                $dangling[$id] = $entry;
            }
        }

        while (count($dangling) > 0) {
            foreach ($dangling as $entry) {
                $id = $entry['category_id'];
                $pid = $entry['parent_id'];

                // If the parent has already been added to the output, it's
                // safe to add this node too
                if (isset($all[$pid])) {
                    $all[$id] = $entry;
                    $all[$pid]['children'][] =& $all[$id];
                    $all[$id]['parent_id'] = $pid;
                    unset($dangling[$entry['category_id']]);
                }
            }
        }
        if ($returnAllStructure) {
            return array($all, $aCategories);
        } else {
            return $aCategories;
        }
    }

    public function getTree($iParentId, $prefix = '-')
    {
        $result = array();

        $aCategories = db()->select('name, category_id, parent_id')
            ->from($this->_sTable)
            ->where('is_active = 1 AND parent_id = ' . (int)$iParentId)
            ->order('ordering ASC, category_id DESC')
            ->execute('getSlaveRows');

        if (!count($aCategories)) {
            return array();
        }

        if ($iParentId != 0) {
            $this->_iCnt++;
        }

        foreach ($aCategories as $aCategory) {
            $aCategory['name'] = ($this->_iCnt > 0 ? str_repeat('&nbsp;', ($this->_iCnt)) . '-' : '') . ' '
                . Phpfox::getLib('locale')->convert(_p($aCategory['name']));
            $result[] = $aCategory;
            $result = array_merge($result, $this->getTree($aCategory['category_id']));
        }

        $this->_iCnt = 0;

        return $result;
    }
}
