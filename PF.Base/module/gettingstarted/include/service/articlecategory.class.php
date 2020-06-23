<?php

/**
 *
 *
 * @copyright        [YouNet_COPYRIGHT]
 * @author           YouNet Company
 * @package          Module_Gettingstarted
 * @version          2.01
 */
defined('PHPFOX') or exit('NO DICE!');

class Gettingstarted_service_articlecategory extends Phpfox_Service
{

    /************************************************************************************/
    /* ================================ version 3.02p5 ================================ */
    /************************************************************************************/

    private $_sOutput = '';

    private $_iCnt = 0;

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('gettingstarted_article_category');
    }

    public function updateOrder($aVals)
    {
        foreach ($aVals as $iId => $iOrder) {
            $this->database()->update($this->_sTable, array('ordering' => $iOrder), 'article_category_id = ' . (int)$iId);
        }

        return true;
    }

    public function addArticleCategory($aVals)
    {
        var_dump($aVals);
        if ($this->isExistName($aVals['article_category_name']) > 0) {
            return false;
        }
        $oFilter = phpfox::getLib('parse.input');
        $aInsert = array();
        $aInsert['parent_id'] = $aVals['parent_id'];
        $aInsert['article_category_name'] = $oFilter->clean($aVals['article_category_name']);
        $aInsert['name_url'] = $oFilter->cleanTitle($aVals['article_category_name']);
        $aInsert['language_id'] = $oFilter->clean($aVals['language_id']);
        $aInsert['time_stamp'] = PHPFOX_TIME;
        $this->database()->insert($this->_sTable, $aInsert);
        return true;
    }

    public function updateArticleCategory($aVals, $iId)
    {
        $oFilter = phpfox::getLib('parse.input');
        $aUpdate = array();
        $aUpdate['parent_id'] = $aVals['parent_id'];
        $aUpdate['article_category_name'] = $oFilter->clean($aVals['article_category_name']);
        $aUpdate['name_url'] = $oFilter->cleanTitle($aVals['article_category_name']);
        $aUpdate['language_id'] = $oFilter->clean($aVals['language_id']);
        $aUpdate['time_stamp'] = PHPFOX_TIME;
        $this->database()->update($this->_sTable, $aUpdate, 'article_category_id=' . (int)$iId);
        return true;
    }

    public function isExistName($sName)
    {
        $oFilter = phpfox::getLib('parse.input');
        $iCount = $this->database()->select('count(*)')
            ->from($this->_sTable)
            ->where('article_category_name="' . $oFilter->clean($sName) . '"')
            ->execute('getSlaveField');
        return $iCount;
    }

    public function getArticleCategoryforEdit($iId)
    {

        $aRows = Phpfox::getLib('database')->select('*')
            ->from($this->_sTable, 'ac')
            ->where('ac.article_category_id = ' . $iId)
            ->execute('getSlaveRow');
        return $aRows;
    }

    public function getCatForManage($language_id)
    {
        $sOutput = $this->_get(0, $language_id);
        return $sOutput;
    }

    private function _get($iParentId, $language_id)
    {
        $oFilter = phpfox::getLib('parse.input');
        $aCategories = $this->database()->select('*')
            ->from($this->_sTable)
            ->where('parent_id = ' . (int)$iParentId . ' AND language_id = \'' . $oFilter->clean($language_id) . '\'')
            ->order('ordering ASC')
            ->execute('getRows');

        if (count($aCategories)) {
            $aCache = array();
            if ($iParentId != 0) {
                $this->_iCnt++;
            }
            $sOutput = '<ul class="ui-sortable dont-unbind">';
            foreach ($aCategories as $iKey => $aCategory) {
                $aCache[] = $aCategory['article_category_id'];
                $sOutput .= '<li><img src="' . Phpfox::getLib('template')->getStyle('image', 'misc/draggable.png') . '" alt="" />
				<input type="hidden" name="order[' . $aCategory['article_category_id'] . ']" value="' . $aCategory['ordering'] . '" class="js_mp_order" />
				<a href="#?id=' . $aCategory['article_category_id'] . '" class="js_drop_down">' . Phpfox::getLib('locale')->convert($aCategory['article_category_name']) . '</a>' . $this->_get($aCategory['article_category_id'], $language_id) . '</li>' . "\n";
            }
            $sOutput .= '</ul>';
            return $sOutput;
            $this->_iCnt = 0;
        }
    }

    public function delete($iId)
    {
        $iExists = $this->database()->select('*')
            ->from($this->_sTable)
            ->where('article_category_id="' . $iId . '"')
            ->execute('getSlaveField');

        if (!$iExists) {
            return false;
        }

        $this->_delete($iId);
        return true;
    }

    private function _delete($iId)
    {
        $aCategories = $this->database()->select('*')
            ->from($this->_sTable)
            ->where('parent_id = ' . (int)$iId)
            ->execute('getRows');

        if (count($aCategories)) {
            foreach ($aCategories as $aCategory) {
                $this->_delete($aCategory['article_category_id']);
            }
        }

        Phpfox::getService('gettingstarted.article')->unCategory($iId);
        $this->database()->delete($this->_sTable, 'article_category_id=' . (int)$iId);
    }

    private function _getArticle($iId, &$aRows, $language_id, $search_key)
    {
        $aCategories = $this->database()->select('article_category_id')
            ->from($this->_sTable)
            ->where('parent_id = ' . (int)$iId)
            ->execute('getRows');

        if (count($aCategories)) {
            foreach ($aCategories as $aCategory) {
                $this->_getArticle($aCategory['article_category_id'], $aRows, $language_id, $search_key);
            }
        }

        $aTemps = $this->database()->select('a.*, c.article_category_name')
            ->from(Phpfox::getT('gettingstarted_article'), 'a')
            ->leftJoin(Phpfox::getT('gettingstarted_article_category'), 'c', 'c.article_category_id=a.article_category_id')
            ->where('a.language_id =\'' . $language_id . '\' AND a.article_category_id = ' . (int)$iId . ' AND title LIKE \'%' . $search_key . '%\'')
            ->execute('getSlaveRows');

        if (count($aTemps)) {
            $aRows = array_merge($aRows, $aTemps);
        }
    }

    public function getArticleById($iId)
    {
        if (Phpfox::isModule('friend')) {
            $this->database()->select('f.friend_id AS is_friend, ')->leftJoin(Phpfox::getT('friend'), 'f', "f.user_id = article.user_id AND f.friend_user_id = " . Phpfox::getUserId());
        }
        if (Phpfox::isModule('like')) {
            $this->database()->select('l.like_id AS is_liked, ')->leftJoin(Phpfox::getT('like'), 'l', 'l.type_id = \'gettingstarted\' AND l.item_id = article.article_id AND l.user_id = ' . Phpfox::getUserId());
        }
        $aLanguage = Phpfox::getLib('locale')->getLang();
        $language_id = $aLanguage['language_id'];

        $aRow = $this->database()->select('article.*')
            ->from(phpfox::getT('gettingstarted_article'), 'article')
            ->where("article.article_id=" . $iId)
            ->execute('getSlaveRow');

        if ($aRow && ($aRow['language_id'] != $language_id)) {
            Phpfox::getLib('url')->send('gettingstarted', null, null);
        }

        if ($aRow) {
            if ($aRow['article_category_id'] == -1) {
                $aRow['article_category_name'] = _p('gettingstarted.uncategorized');
                $aRow['name_url'] = strtolower(_p('gettingstarted.uncategorized'));
            } else {
                $aRow['article_category_name'] = $this->database()->select('article_category_name')
                    ->from($this->_sTable)
                    ->where('article_category_id=' . $aRow['article_category_id'])
                    ->execute('getSlaveField');
            }
        }

        return $aRow;
    }

    public function searchArticleByCategoryId($key, $iId, $iLimit, $iPage = null, $arrSearch)
    {
        $aRows = array();
        $aTemps = $this->searchdsArticleByCategoryId($key, $iId, $arrSearch);

        if ($iPage === null) {
            if ($iLimit > count($aTemps)) {
                $iLimit = count($aTemps);
            }

            for ($i = 0; $i < $iLimit; $i++) {
                $aRows[] = $aTemps[$i];
            }
        } else {
            $start = ($iPage - 1) * $iLimit;
            $end = ($start + $iLimit - 1 < count($aTemps) - 1) ? ($start + $iLimit - 1) : (count($aTemps) - 1);

            for ($i = $start; $i <= $end; $i++) {
                $aRows[] = $aTemps[$i];
            }
        }

        return $aRows;
    }

    public function searchdsArticleByCategoryId($key, $iId, $aSearchCondition = "")
    {
        $aLanguage = PHPFOX::getLib('locale')->getLang();
        $language_id = $aLanguage['language_id'];

        $aRows = array();
        $this->_searchdsArticleByCategoryId($key, $iId, $aRows, $language_id, $aSearchCondition);

        $aRows = $this->_subval_sort($aRows, 'time_stamp');

        foreach ($aRows as $k => $v) {
            $aRows[$k]['title'] = Phpfox::getService('gettingstarted.search')->highlight($key, $aRows[$k]['title']);
        }

        return $aRows;
    }

    private function _searchdsArticleByCategoryId($key, $iId, &$aRows, $language_id, $aSearchCondition = "")
    {
        $aCategories = $this->database()->select('article_category_id')
            ->from($this->_sTable)
            ->where('parent_id = ' . (int)$iId)
            ->execute('getRows');

        if (count($aCategories)) {
            foreach ($aCategories as $aCategory) {
                $this->_searchdsArticleByCategoryId($key, $aCategory['article_category_id'], $aRows, $language_id);
            }
        }
        $where = 'a.language_id =\'' . $language_id . '\' AND a.article_category_id = ' . (int)$iId;

        if (is_array($aSearchCondition)) {
            for ($i = 0; $i < count($aSearchCondition); $i++) {
                $item = $aSearchCondition[$i];
                if (strpos($item, 'AND') !== false) {
                    $where .= " " . $item;
                }
            }
        }


        if (!empty($key)) {
            if (is_string($key)) {
                if (strpos($key, 'AND') !== false) {
                    $where .= $key;
                } else
                    $where .= ' AND a.title LIKE \'%' . $key . '%\'';
            }
        }
        $where = str_replace('gettingstarted_article', 'a', $where);

        $aTemps = $this->database()->select('a.*, c.article_category_name')
            ->from(Phpfox::getT('gettingstarted_article'), 'a')
            ->leftJoin($this->_sTable, 'c', 'a.article_category_id=c.article_category_id')
            ->where($where)
            ->execute('getSlaveRows');

        if (count($aTemps)) {
            $aRows = array_merge($aRows, $aTemps);
        }
    }

    public function getArticleByCategoryId($iId, $iLimit, $iPage = null)
    {
        $aRows = array();
        $aTemps = $this->getdsArticleByCategoryId($iId);

        if ($iPage === null) {
            if ($iLimit > count($aTemps)) {
                $iLimit = count($aTemps);
            }

            for ($i = 0; $i < $iLimit; $i++) {
                $aRows[] = $aTemps[$i];
            }
        } else {
            $start = ($iPage - 1) * $iLimit;
            $end = ($start + $iLimit - 1 < count($aTemps) - 1) ? ($start + $iLimit - 1) : (count($aTemps) - 1);

            for ($i = $start; $i <= $end; $i++) {
                $aRows[] = $aTemps[$i];
            }
        }

        return $aRows;
    }

    public function getdsArticleByCategoryId($iId)
    {
        $aLanguage = PHPFOX::getLib('locale')->getLang();
        $language_id = $aLanguage['language_id'];

        $aRows = array();
        $this->_getdsArticleByCategoryId($iId, $aRows, $language_id);

        $aRows = $this->_subval_sort($aRows, 'time_stamp');

        return $aRows;
    }

    public function get_Article_total_By_CategoryId($iId)
    {
        $aLanguage = PHPFOX::getLib('locale')->getLang();
        $language_id = $aLanguage['language_id'];

        $aRows = array();
        $this->_getdsArticleByCategoryId($iId, $aRows, $language_id);

        $aRows = $this->_subval_sort($aRows, 'time_stamp');

        return $aRows;
    }


    private function _getdsArticleByCategoryId($iId, &$aRows, $language_id)
    {
        $aCategories = $this->database()->select('article_category_id')
            ->from($this->_sTable)
            ->where('parent_id = ' . (int)$iId)
            ->execute('getRows');

        if (count($aCategories)) {
            foreach ($aCategories as $aCategory) {
                $this->_getdsArticleByCategoryId($aCategory['article_category_id'], $aRows, $language_id);
            }
        }

        $aTemps = $this->database()->select('a.*, c.article_category_name, c.name_url')
            ->from(Phpfox::getT('gettingstarted_article'), 'a')
            ->leftJoin($this->_sTable, 'c', 'a.article_category_id=c.article_category_id')
            ->where('a.language_id =\'' . $language_id . '\' AND a.article_category_id = ' . (int)$iId)
            ->execute('getSlaveRows');

        if (count($aTemps)) {
            $aRows = array_merge($aRows, $aTemps);
        }
    }

    public function getCategoryForSearchArticle($language_id)
    {
        $aRows = array();
        $aRows[0] = _p('gettingstarted.any');

        $aItems = Phpfox::getService('gettingstarted.multicat')->getItems($language_id);
        $this->_getCategoryForSearchArticle($aRows, $aItems, 0, '');

        $aRows[-1] = _p('gettingstarted.uncategorized');
        return $aRows;
    }

    private function _getCategoryForSearchArticle(&$aRows, $aItems, $pid, $refix)
    {
        foreach ($aItems as $aItem) {
            if ($aItem['parent_id'] == $pid) {
                $aRows[$aItem['article_category_id'] . '-' . $aItem['language_id']] = $refix . $aItem['article_category_name'];
            }
            if (!empty($aItem['items'])) {
                $this->_getCategoryForSearchArticle($aRows, $aItem['items'], $aItem['article_category_id'], '&nbsp&nbsp&nbsp&nbsp' . $refix);
            }
        }
    }


    /************************************************************************************/
    /* =========================== versions earlier 3.02p5 ============================ */
    /************************************************************************************/

    public function get($iPage, $iLimit, $iCnt)
    {
        $sLanguage = phpfox::getLib('locale')->getLang();
        $sLanguage_id = $sLanguage['language_id'];
        $aRows = $this->database()->select('category.*')
            ->from(phpfox::getT('gettingstarted_article_category'), 'category')
            ->where("language_id='" . $sLanguage_id . "' AND parent_id=0")
            ->order('ordering ASC')
            ->limit($iPage, $iLimit, $iCnt)
            ->execute('getSlaveRows');

        $iLastPage = (int)(($iCnt) / $iLimit);
        $mod = $iCnt % $iLimit;
        if ($mod != 0) {
            $iLastPage = (int)(($iCnt) / $iLimit) + 1;
        }

        if ($iPage == $iLastPage) {

            $aRow = array();
            $aRow[count($aRows)]['article_category_id'] = -1;
            $aRow[count($aRows)]['article_category_name'] = _p('gettingstarted.uncategorized');
            $aRow[count($aRows)]['name_url'] = strtolower(_p('gettingstarted.uncategorized'));
            $aRows = array_merge($aRows, $aRow);
        }
        return $aRows;
    }

    public function getCategoriesForEdit($iPage, $iLimit, $iCnt)
    {
        $aRows = $this->database()->select('category.*,l.title')
            ->from(phpfox::getT('gettingstarted_article_category'), 'category')
            ->join(phpfox::getT('language'), 'l', 'l.language_id = category.language_id')
            ->limit($iPage, $iLimit, $iCnt)
            ->order('article_category_id DESC')
            ->execute('getSlaveRows');
        return $aRows;
    }

    public function getCategoriesForEditByLanguage($language_id, $iPage, $iLimit, $iCnt)
    {
        if ($language_id == -1) {
            $aRows = $this->database()->select('category.*,l.title')
                ->from(phpfox::getT('gettingstarted_article_category'), 'category')
                ->join(phpfox::getT('language'), 'l', 'l.language_id = category.language_id')
                ->limit($iPage, $iLimit, $iCnt)
                ->order('article_category_id DESC,time_stamp DESC')
                ->execute('getSlaveRows');
        } else {
            $aRows = $this->database()->select('category.*,l.title')
                ->from(phpfox::getT('gettingstarted_article_category'), 'category')
                ->join(phpfox::getT('language'), 'l', 'l.language_id = category.language_id')
                ->where("category.language_id='" . $language_id . "'")
                ->limit($iPage, $iLimit, $iCnt)
                ->order('article_category_id DESC')
                ->execute('getSlaveRows');
        }
        return $aRows;
    }

    public function getCount()
    {
        $iCount = (int)$this->database()->select('count(*)')
            ->from(phpfox::getT('gettingstarted_article_category'), 'category')
            ->execute('getSlaveField');
        return $iCount;
    }

    //get total article categories for Pager on manage category page
    public function getCountByLanguage($language_id)
    {
        if ($language_id == -1) {
            $iCount = (int)$this->database()->select('count(*)')
                ->from(phpfox::getT('gettingstarted_article_category'), 'category')
                ->execute('getSlaveField');
        } else {
            $iCount = (int)$this->database()->select('count(*)')
                ->from(phpfox::getT('gettingstarted_article_category'), 'category')
                ->where("language_id='" . $language_id . "'")
                ->execute('getSlaveField');
        }

        return $iCount;
    }

    public function getArticleCategoryById($Id)
    {
        $aRow = $this->database()->select('*')
            ->from(phpfox::getT('gettingstarted_article_category'), 'category')
            ->where('category.article_category_id=' . $Id)
            ->execute('getSlaveRow');
        if ($Id == -1) {
            $aRow['article_category_name'] = _p('gettingstarted.uncategorized');
            $aRow['name_url'] = strtolower(_p('gettingstarted.uncategorized'));
        }
        return $aRow;
    }

    public function getArticleCategory()
    {
        $aLanguage = PHPFOX::getLib('locale')->getLang();
        $language_id = $aLanguage['language_id'];
        $aRows = $this->database()->select('category.*')
            ->from(phpfox::getT('gettingstarted_article_category'), 'category')
            ->where("category.language_id ='" . $language_id . "'")
            ->order('category.ordering ASC')
            ->execute('getSlaveRows');
        $aRow = array();
        $aRow[count($aRows)]['article_category_id'] = -1;
        $aRow[count($aRows)]['article_category_name'] = _p('gettingstarted.uncategorized');
        $aRow[count($aRows)]['name_url'] = strtolower(_p('gettingstarted.uncategorized'));
        $aRow[count($aRows)]['language_id'] = $aLanguage['language_id'];
        $aRows = array_merge($aRows, $aRow);

        return $aRows;
    }

    public function getArticleCategoryForManage()
    {

        $aRows = $this->database()->select('category.*')
            ->from(phpfox::getT('gettingstarted_article_category'), 'category')
            ->execute('getSlaveRows');


        return $aRows;
    }

    public function getArticleCategoryForManageByLanguage($sLanguage_id)
    {

        $aRows = $this->database()->select('category.*')
            ->from(phpfox::getT('gettingstarted_article_category'), 'category')
            ->where("category.language_id='" . $sLanguage_id . "'")
            ->execute('getSlaveRows');


        return $aRows;
    }

    public function UpdateArticleCategoryById($aVals)
    {
        $oFilter = phpfox::getLib('parse.input');
        $aUpdates = array();
        $aUpdates['article_category_name'] = $oFilter->clean($aVals['title']);
        $this->database()->update(phpfox::getT('gettingstarted_article_category'), $aUpdates, 'article_category_id=' . $aVals['article_category_id']);
    }

    public function deleteArticleCategoryById($Id)
    {
        $this->database()->delete(phpfox::getT('gettingstarted_article_category'), 'article_category_id=' . $Id);
    }

    public function deleteArticleById($Id)
    {
        $arr = explode('->', $Id);
        $this->database()->delete(phpfox::getT('gettingstarted_article'), 'article_id=' . $arr[0] . " AND language_id='" . $arr[1] . "'");
    }

    public function deleteMultiple($aIds)
    {
        foreach ($aIds as $iId) {
            $this->updateCategoryArticle($iId);
            $this->deleteArticleCategoryById($iId);
        }
        return true;
    }

    public function updateCategoryArticle($iCategoryId)
    {
        $aUpdate['article_category_id'] = -1;
        Phpfox::getLib('database')->update(phpfox::getT('gettingstarted_article'), $aUpdate, 'article_category_id = ' . (int)$iCategoryId);
    }

    public function deleteMultipleArticle($aIds)
    {
        foreach ($aIds as $iId) {
            $this->deleteArticleById($iId);
        }
        return true;
    }

    public function addarticle($aVals, $bIsAddId = true)
    {
        $aInsert = array();
        if (!isset($aVals['article_category_id'])) {
            Phpfox_Error::set(_p('gettingstarted.fill_in_a_description_for_your_knowledgebase_article'));
            Phpfox::getLib('url')->send("current", null, _p('gettingstarted.error_please_provide_a_category_dot'));
        }

        if ($bIsAddId) {
            $aInsert['article_id'] = $aVals['article_id'];
        }

        $oFilter = phpfox::getLib('parse.input');
        $aInsert['title'] = $oFilter->clean($aVals['title']);
        $aInsert['description_parsed'] = $oFilter->prepare($aVals['description']);
        $aInsert['description'] = $oFilter->clean($aVals['description']);
        $aInsert['user_id'] = Phpfox::getUserId();
        $aInsert['time_stamp'] = PHPFOX_TIME;
        $aInsert['article_category_id'] = ($aVals['article_category_id'] != 0) ? $aVals['article_category_id'] : -1;
        $aInsert['language_id'] = $aVals['language_id'];
        $aInsert['is_featured'] = $aVals['is_featured'];
        $aInsert['total_rating'] = 0;
        $iId = $this->database()->insert(phpfox::getT('gettingstarted_article'), $aInsert);
        return $iId;
    }

    public function getCountArticle($title_search, $category_search)
    {
        $aLanguage = PHPFOX::getLib('locale')->getLang();
        $language_id = $aLanguage['language_id'];
        $aTemps = array();
        $this->_getArticle($category_search, $aTemps, $language_id, $title_search);
        $iCount = count($aTemps);
        return $iCount;
    }

    public function getArticle($title_search, $category_search, $iPage, $iLimit, $iCnt)
    {
        $aLanguage = PHPFOX::getLib('locale')->getLang();
        $language_id = $aLanguage['language_id'];
        $aTemps = array();

        $this->_getArticle($category_search, $aTemps, $language_id, $title_search);

        $aRows = array();

        if ($iLimit != '') {
            $start = ($iPage - 1) * $iLimit;
            if ($iCnt < ($start + $iLimit)) {
                $end = $iCnt - 1;
            } else {
                $end = $start + $iLimit - 1;
            }

            for ($i = $start; $i <= $end; $i++) {
                $aRows[] = $aTemps[$i];
            }
        } else {
            $aRows = $aTemps;
        }

        $aCat = $this->database()->select('article_category_name')
            ->from($this->_sTable)
            ->where('article_category_id=' . $category_search)
            ->execute('getRow');

        for ($i = 0; $i < count($aRows); $i++) {
            $aRows[$i]['title'] = PHPFOX::getService('gettingstarted.search')->highlight($title_search, $aRows[$i]['title']);
            $aRows[$i]['root_category_name'] = $aCat['article_category_name'];
            $aRows[$i]['root_category_id'] = $category_search;
        }

        return $aRows;
    }

    //get all articles to list in admincp.
    public function getArticleForManage($iFeatured_id, $language_id, $title_search, $category_search, $sSortBy, $iPage, $iLimit, $iCnt)
    {

        $category_language = 0;

        if ($category_search != 0 && $category_search != -1) {
            $arr_temp = explode('-', $category_search);
            $category_search = $arr_temp[0];
            $category_language = $arr_temp[1];
        }


        $aRows = $this->database()->select('article.*, category.article_category_name,l.title as language_title')
            ->from(phpfox::getT('gettingstarted_article'), 'article')
            ->join(PHPFOX::getT('language'), 'l', 'article.language_id = l.language_id')
            ->leftjoin(phpfox::getT('gettingstarted_article_category'), 'category', 'category.article_category_id=article.article_category_id AND article.language_id = category.language_id')
            ->where("article.title like '%" . $title_search . "%' and ((article.article_category_id=" . $category_search . " or '" . $category_search . "'='0') AND (article.language_id='" . $category_language . "' OR '" . $category_language . "' = '0')) AND (article.is_featured=" . $iFeatured_id . " or '" . $iFeatured_id . "'='-1') AND (article.language_id='" . $language_id . "')")
            ->order('article.article_category_id DESC, article.time_stamp DESC')
            ->limit($iPage, $iLimit, $iCnt)
            ->execute('getSlaveRows');
        return $aRows;
    }

    public function getExtra(&$aArticles)
    {
        if (count($aArticles) > 0) {
            foreach ($aArticles as $iKey => $value) {
                if ($aArticles[$iKey]['article_category_id'] == -1) {
                    $aArticles[$iKey]['article_category_name'] = _p('gettingstarted.uncategorized');
                    $aArticles[$iKey]['name_url'] = strtolower(_p('gettingstarted.uncategorized'));
                }
                $post_time = Phpfox::getTime(Phpfox::getParam('gettingstarted.display_time_stamp'), $value['time_stamp']);
                $aArticles[$iKey]['post_time'] = $post_time;
            }
        }
    }

    public function getExtraSearch(&$aArticles)
    {
        if (count($aArticles) > 0) {
            foreach ($aArticles as $iKey => $value) {
                if ($aArticles[$iKey]['root_category_id'] == -1) {
                    $aArticles[$iKey]['root_category_name'] = _p('gettingstarted.uncategorized');
                    $aArticles[$iKey]['name_url'] = strtolower(_p('gettingstarted.uncategorized'));
                }
                $post_time = Phpfox::getTime(Phpfox::getParam('gettingstarted.display_time_stamp'), $value['time_stamp']);
                $aArticles[$iKey]['info'] = _p('gettingstarted.posted_on_post_time', array('post_time' => $post_time));
            }
        }
    }

    public function getCountArticleForManage($iFeatured_value, $language_id, $title_search, $category_search, $sSortBy)
    {

        $category_language = 0;

        if ($category_search != 0 && $category_search != -1) {
            $arr_temp = explode('-', $category_search);
            $category_search = $arr_temp[0];
            $category_language = $arr_temp[1];
        }


        $iCount = (int)$this->database()->select('count(*)')
            ->from(phpfox::getT('gettingstarted_article'), 'article')
            ->leftjoin(phpfox::getT('gettingstarted_article_category'), 'category', 'category.article_category_id=article.article_category_id AND article.language_id = category.language_id')
            ->where("article.title like '%" . $title_search . "%' and ((article.article_category_id=" . $category_search . " or '" . $category_search . "'='0') AND (article.language_id='" . $category_language . "' OR '" . $category_language . "' = '0')) AND (article.is_featured=" . $iFeatured_value . " or '" . $iFeatured_value . "'='-1') AND (article.language_id='" . $language_id . "')")
            ->order('article.article_category_id')
            ->execute('getSlaveField');
        return $iCount;
    }

    public function getArticleLastest($iLimit)
    {
        $aLanguage = PHPFOX::getLib('locale')->getLang();
        $language_id = $aLanguage['language_id'];
        $aRows = $this->database()->select('*')
            ->from(phpfox::getT('gettingstarted_article'), 'article')
            ->where("language_id='" . $language_id . "'")
            ->order('article.time_stamp desc')
            ->limit($iLimit)
            ->execute('getSlaveRows');
        return $aRows;
    }

    public function getArticlePaginationId($iId, $iPage, $iLimit, $iCnt)
    {
        $aRows = $this->database()->select('*')
            ->from(phpfox::getT('gettingstarted_article'), 'article')
            ->where('article.article_category_id=' . $iId)
            ->order('article.time_stamp desc')
            ->limit($iPage, $iLimit, $iCnt)
            ->execute('getSlaveRows');
        return $aRows;
    }

    public function getCountArticlePaginationId($iId)
    {
        $iCount = (int)$this->database()->select('count(*)')
            ->from(phpfox::getT('gettingstarted_article'), 'article')
            ->where('article.article_category_id=' . $iId)
            ->execute('getSlaveField');
        return $iCount;
    }

    #function to sort a multi-dimensional array
    private function _subval_sort($a, $subkey)
    {
        $b = array();
        $c = array();

        foreach ($a as $k => $v) {
            $b[$k] = $v[$subkey];
        }
        arsort($b);
        foreach ($b as $key => $val) {
            $c[] = $a[$key];
        }
        return $c;
    }

    public function updateArticle($aVals)
    {

        $oFilter = phpfox::getLib('parse.input');

        $aUpdates = array();
        $aUpdates['title'] = $oFilter->clean($aVals['title']);
        $aUpdates['description'] = $oFilter->clean($aVals['description']);
        $aUpdates['description_parsed'] = $oFilter->prepare($aVals['description']);
        $aUpdates['article_category_id'] = ($aVals['article_category_id'] != 0) ? $oFilter->clean($aVals['article_category_id']) : -1;
        $aUpdates['language_id'] = $aVals['language_id'];
        $aUpdates['is_featured'] = $aVals['is_featured'];
        $this->database()->update(phpfox::getT('gettingstarted_article'), $aUpdates, 'article_id=' . $aVals['article_id'] . " AND language_id='" . $aVals['language_id'] . "'");
    }

    public function isExistRating($user_id, $item_id)
    {
        $iCount = (int)$this->database()->select('count(*)')
            ->from(phpfox::getT('gettingstarted_rating'))
            ->where('user_id=' . $user_id . ' and item_id=' . $item_id)
            ->execute('getSlaveField');
        return $iCount;
    }

    //By HT
    //For category---------------------------------------------
    public function addCategoryByLanguage($arrInsert, $bIsAddId = false)
    {
        if (!$bIsAddId) {
            if (isset($arrInsert['article_category_id'])) {
                unset($arrInsert['article_category_id']);
            }
        }
        return $this->database()->insert(phpfox::getT('gettingstarted_article_category'), $arrInsert);
    }

    //get information of maximum category
    public function getMaxCategoryInfo()
    {
        $aId = $this->database()->select("MAX(article_category_id) as id")
            ->from(PhpFox::getT("gettingstarted_article_category"))
            ->execute('getSlaveRow');


        if (isset($aId) && $aId['id'] != null) {
            $aRows = $this->database()->select("*")
                ->from(PhpFox::getT("gettingstarted_article_category"))
                ->where("article_category_id =" . $aId['id'])
                ->execute("getSlaveRow");
        } else {
            $aRows['article_category_id'] = 0;
        }
        return $aRows;
    }

    //add new category by language
    public function addMoreCategoryLanguage($aVals)
    {
        $oFilter = phpfox::getLib('parse.input');
        $aInsert = array();
        $aInsert['article_category_id'] = $aVals['article_category_id'];
        $aInsert['article_category_name'] = $oFilter->clean($aVals['article_category_name']);
        $aInsert['time_stamp'] = PHPFOX_TIME;
        $aInsert['language_id'] = $oFilter->clean($aVals['language_id']);
        $this->database()->insert(phpfox::getT('gettingstarted_article_category'), $aInsert);
        return true;
    }

    //Check existed or not category name. Use when insert or update category
    public function isExistCategoryName($language_id, $category_name)
    {
        $aRow = $this->database()->select("article_category_id")
            ->from(PHPFOX::getT('gettingstarted_article_category'))
            ->where("language_id='" . $language_id . "' AND article_category_name='" . $category_name . "'")
            ->execute('getSlaveRow');
        if (count($aRow) > 0) {
            return true;
        } else {
            return false;
        }
    }

    //Get all article categories for list
    public function getArtCategory()
    {
        $aRows = $this->database()->select('category.*')
            ->from(phpfox::getT('gettingstarted_article_category'), 'category')
            ->order('time_stamp DESC')
            ->execute('getSlaveRows');
        return $aRows;
    }

    //Get all article categories for list by language
    public function getArticleCategoryByLanguage($language_id)
    {

        $aRows = $this->database()->select('category.*')
            ->from(phpfox::getT('gettingstarted_article_category'), 'category')
            ->where("language_id = '" . $language_id . "'")
            ->execute('getSlaveRows');

        return $aRows;
    }

    public function updateCategoryByLanguage($arrUpdate, $iId, $language_id)
    {
        return $this->database()->update(PHPFOX::getT('gettingstarted_article_category'), $arrUpdate, 'article_category_id =' . $iId . " AND language_id='" . $language_id . "'");
    }

    //Check category is existed or not by category_id and language
    public function isExistCategory($category_id, $language_id)
    {
        $aRow = $this->database()->select('article_category_id')
            ->from(PHPFOX::getT('gettingstarted_article_category'))
            ->where('article_category_id =' . $category_id . " AND language_id ='" . $language_id . "'")
            ->execute('getSlaveRow');
        if (count($aRow) > 0) {
            return true;
        } else
            return false;
    }

    public function deleteArticleCategoryByLanguage($iId, $language_id)
    {
        $this->database()->delete(phpfox::getT('gettingstarted_article_category'), 'article_category_id=' . $iId . " AND language_id='" . $language_id . "'");
    }

    public function updateArticleCategoryForEdit($iId, $sLanguage_id, $aArray)
    {
        $this->database()->update(PHPFOX::getT('gettingstarted_article'), $aArray, 'article_category_id=' . $iId . " AND language_id='" . $sLanguage_id . "'");
    }

    //For article-------------------------------
    //get maximum article. User to know article id when insert next article
    public function getMaxArticleId()
    {
        $aRow = $this->database()->select('Max(article_id) as article_id')
            ->from(PHPFOX::getT('gettingstarted_article'))
            ->execute('getSlaveRow');
        if (count($aRow) > 0) {
            return $aRow['article_id'];
        } else {
            return 0;
        }
    }

    //Get All articles for list  
    public function getAllArticle($iPage, $iLimit, $iCnt)
    {

        $aRows = $this->database()->select('article.*, category.article_category_name,l.title as language_title')
            ->from(phpfox::getT('gettingstarted_article'), 'article')
            ->join(PHPFOX::getT('language'), 'l', 'article.language_id = l.language_id')
            ->leftjoin(phpfox::getT('gettingstarted_article_category'), 'category', 'category.article_category_id=article.article_category_id AND category.language_id = article.language_id')
            ->order('article.article_category_id DESC')
            ->limit($iPage, $iLimit, $iCnt)
            ->execute('getSlaveRows');
        return $aRows;
    }

    //Get an article for edit by language
    public function getArticleForEdit($art_id, $language_id)
    {
        $aRow = $this->database()->select('article.*, category.article_category_name')
            ->from(phpfox::getT('gettingstarted_article'), 'article')
            ->leftjoin(phpfox::getT('gettingstarted_article_category'), 'category', 'category.article_category_id=article.article_category_id')
            ->where('article.article_id=' . $art_id . " AND article.language_id='" . $language_id . "'")
            ->execute('getSlaveRow');
        return $aRow;
    }

    //Check article is existed or not by Article_id and Language
    public function isExistArticleByLanguage($art_id, $language_id)
    {
        $aRow = $this->database()->select('article_id')
            ->from(phpfox::getT('gettingstarted_article'))
            ->where('article_id=' . $art_id . " AND language_id='" . $language_id . "'")
            ->execute('getSlaveRow');
        if (count($aRow) > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function updateArticleByLanguage($arr)
    {
        $this->database()->update(PHPFOX::getT('gettingstarted_article'), $arr, 'article_id=' . $arr['article_id'] . " AND language_id='" . $arr['language_id'] . "'");
    }

    //update is_featured for update or insert article in orther language
    public function updateIsFeatured($iId, $bFeatured)
    {
        $this->database()->query("UPDATE " . PHPFOX::getT('gettingstarted_article') . " SET is_featured= '" . $bFeatured . "' WHERE article_id=" . $iId);
    }
}

?>
