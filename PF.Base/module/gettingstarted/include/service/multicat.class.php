<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

/**
 *
 *
 * @copyright   [YOUNET_COPYRIGHT]
 * @author      YouNet Company
 * @package     YouNet_Gettingstarted
 * @version     3.02p1
 *
 * @tutorial    Change your module name, column name follow in database
 *              Change your url pattern in function _renderItem
 *              Remove comment tag in function getMenu to use cache
 */
class Gettingstarted_Service_Multicat extends Phpfox_Service
{

    protected $_sTable;

    protected $_module = 'gettingstarted';

    protected $_col_name = 'article_category_name';

    protected $_col_id = 'article_category_id';

    protected $_col_parent_id = 'parent_id';

    protected $_cache_key = 'gettingstarted_cat';


    /**
     * constructor
     * @return void
     */
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('gettingstarted_article_category');
    }


    /**
     * Get all items in database and build to array
     * @param   string $lang : language_id (if designed in database),
     * @return  array
     */
    public function getItems($lang = null)
    {
        $aItems = array();

        $this->_getItems($aItems, 0, $lang);

        return $aItems;
    }

    protected function _getItems(&$aItems, $pId, $lang = null)
    {
        $where = $this->_col_parent_id . '=' . $pId;

        if ($lang !== null) {
            $where .= ' AND language_id=\'' . $lang . '\'';
        }

        $aTemps = $this->database()->select('*')
            ->from($this->_sTable)
            ->where($where)
            ->order('ordering ASC')
            ->execute('getSlaveRows');


        $oOutput = Phpfox::getLib('parse.output');
        $oLocale = Phpfox::getLib('locale');
        foreach ($aTemps as &$aCat) {
            $aCat['article_category_name'] = $oOutput->clean($oLocale->convert($aCat['article_category_name']));
        }
        if (count($aTemps)) {
            foreach ($aTemps as $k => $aTemp) {
                $aItems[$k] = $aTemp;
                $this->_getItems($aItems[$k]['items'], $aItems[$k][$this->_col_id], $lang);
            }
        }
    }


    /**
     * Build Html menu from array items
     * @param   array $css : array(id, name, class) of ul
     * @param   string $lang : language_id (if designed in database),
     * @return  html
     */
    public function getMenu($iCurrent = 0, $css = null, $lang = null)
    {
        $aItems = $this->getItems($lang); //not cache items

        #gettingstarted only
        $aItems[] = array(
            'article_category_id' => -1,
            'article_category_name' => _p('gettingstarted.uncategorized'),
            'name_url' => 'uncategorized',
            'language_id' => $lang,
            'parent_id' => '-1',
        );

        # build to Html
        $sHtml = '<div class="sub_section_menu">';
        $first = true;
        $this->_renderItem_custom($aItems, $iCurrent, $sHtml, $css, $first, $iCurrent);

        $sHtml .= '</div>';

        return $sHtml;
    }


    public function getForBrowse($iCategoryId = null)
    {
        $aLanguage = PHPFOX::getLib('locale')->getLang();
        $language_id = $aLanguage['language_id'];
        $aCategories = $this->database()->select('dc.article_category_id as category_id, dc.article_category_name as name, dc.name_url')
            ->from($this->_sTable, 'dc')
            ->where('dc.parent_id = ' . ($iCategoryId === null ? '0' : (int)$iCategoryId) . ' AND language_id = \''. $language_id . '\'')
            ->order('dc.ordering ASC')
            ->execute('getRows');
        foreach ($aCategories as $iKey => $aCategory) {
            $aCategories[$iKey]['url'] = Phpfox::permalink('gettingstarted.categories', $aCategory['category_id'], $aCategory['name_url']);
            $aCategories[$iKey]['sub'] = $this->database()->select('dc.article_category_id as category_id, dc.article_category_name as name, dc.name_url')
                ->from($this->_sTable, 'dc')
                ->where('dc.parent_id = ' . $aCategory['category_id'] . ' AND language_id = \''. $language_id . '\'')
                ->order('dc.ordering ASC')
                ->execute('getRows');

            foreach ($aCategories[$iKey]['sub'] as $iSubKey => $aSubCategory) {
                $aCategories[$iKey]['sub'][$iSubKey]['url'] = Phpfox::permalink('gettingstarted.categories', $aSubCategory['category_id'], $aSubCategory['name_url']);

            }
        }
        return $aCategories;
    }

    protected function _renderItem($aItems, $iCurrent, &$sHtml, $css = null)
    {
        $id = (is_array($css) && isset($css['id'])) ? $css['id'] : '';
        $name = (is_array($css) && isset($css['name'])) ? $css['name'] : '';
        $class = (is_array($css) && isset($css['class'])) ? $css['class'] : '';
        $aData = $this->database()->select('*')->from($this->_sTable)->execute('getSlaveRows');
        $sHtml .= '<ul id="sub' . $iCurrent . '" name="' . $name . '" class="' . $class . '">';

        foreach ($aItems as $aItem) {
            $name_url = Phpfox::getLib('parse.input')->cleanTitle($aItem[$this->_col_name]);
            $url = Phpfox::getLib('url')->makeUrl($this->_module . '.categories', array($aItem[$this->_col_id], $aItem['name_url'], 'view_'));

            if ($iCurrent != 0) {
                if ($aItem[$this->_col_id] == $iCurrent) {
                    $sHtml .= '<li class="active">';
                } elseif ($this->is_parent($aData, $aItem, $iCurrent) || $aItem[$this->_col_parent_id] == 0 || $aItem[$this->_col_parent_id] == $iCurrent || $aItem[$this->_col_id] == -1) {
                    $sHtml .= '<li>';
                } else {
                    continue;
                }
            } else {
                if ($aItem[$this->_col_parent_id] == 0 || $aItem[$this->_col_id] == -1) {
                    $sHtml .= '<li>';
                } else {


                    continue;
                }
            }

            $sHtml .= '<a   href="' . $url . '">' . $aItem[$this->_col_name] . '</a>';

            if (isset($aItem['items'])) {
                $this->_renderItem($aItem['items'], $iCurrent, $sHtml, null);
            }

            $sHtml .= '</li>';
        }

        $sHtml .= '</ul>';
    }

    protected function _renderItem_custom($aItems, $iCurrent, &$sHtml, $css = null, $first, $iActiveCatId)
    {
        $id = (is_array($css) && isset($css['id'])) ? $css['id'] : '';
        $name = (is_array($css) && isset($css['name'])) ? $css['name'] : '';
        $class = (is_array($css) && isset($css['class'])) ? $css['class'] : '';

        $aData = $this->database()->select('*')->from($this->_sTable)->execute('getSlaveRows');

        $bHasChild = true;
        foreach ($aItems as $aItem) {
            if ($aItem[$this->_col_id] == $iCurrent) {
                if ($aItem[$this->_col_parent_id] == 0) {
                    $bHasChild = false;
                }
            }
        }
        if (($bHasChild) && (!$first))
            $sHtml .= '<span class="spansubmenu" onclick="isClicked_' . $iCurrent . ' =true;toggleSubMenu(this); "><i class="fa fa-chevron-down"></i></span>';


        $sHtml .= '<ul class="action gs_sub_menu" id="sub' . $iCurrent . '"';

        $sHtml .= ' name="' . $name . '" class="' . $class . '">';

        foreach ($aItems as $aItem) {

            $name_url = Phpfox::getLib('parse.input')->cleanTitle($aItem[$this->_col_name]);
            #url: change it for your module
            $url = Phpfox::getLib('url')->makeUrl($this->_module . '.categories', array($aItem[$this->_col_id], $aItem['name_url'], 'view_'));

            if ($iCurrent != 0) {
                if ($aItem[$this->_col_id] == $iActiveCatId) {
                    $sHtml .= '<li class="active">';
                } elseif ($this->is_parent($aData, $aItem, $iCurrent) || $aItem[$this->_col_parent_id] == 0 || $aItem[$this->_col_parent_id] == $iCurrent || $aItem[$this->_col_id] == -1) {
                    $sHtml .= '<li>';
                } else {
                    continue;
                }
            } else {
                if ($aItem[$this->_col_parent_id] == 0 || $aItem[$this->_col_id] == -1) {
                    $sHtml .= '<li>';
                } else {


                    continue;
                }
            }

            $sHtml .= '<a   href="' . $url . '">' . $aItem[$this->_col_name] . '</a>';

            if (isset($aItem['items'])) {
                $this->_renderItem_custom($aItem['items'], $aItem[$this->_col_id], $sHtml, null, false, $iActiveCatId);
            }

            $sHtml .= '</li>';
        }

        $sHtml .= '</ul>';
    }


    protected function _renderItem_new($aItems, $iparentID, &$sHtml, $css = null, $currentCatID = 0)
    {
        $id = (is_array($css) && isset($css['id'])) ? $css['id'] : '';
        $name = (is_array($css) && isset($css['name'])) ? $css['name'] : '';
        $class = (is_array($css) && isset($css['class'])) ? $css['class'] : '';


        $aData = $this->database()->select('*')->from($this->_sTable)->order("ordering asc")->execute('getSlaveRows');

        $sHtml .= '<ul id="sub' . $iparentID . '" name="' . $name . '" class="' . $class . '">';

        foreach ($aData as $aItem) {
            // level 0 root
            if ($aItem[$this->_col_parent_id] == $iparentID) {
                $sHtml .= "<li>";
                $url = Phpfox::getLib('url')->makeUrl($this->_module . '.categories', array($aItem[$this->_col_id], $aItem['name_url'], 'view_'));
                $sHtml .= '<a   href="' . $url . '">' . $aItem[$this->_col_name] . '</a>';


                // level 1
                foreach ($aData as $aItem2) {
                    if ($aItem2[$this->_col_parent_id] == $aItem[$this->_col_id]) {
                        $sHtml .= "<ul>";
                    }
                }

                foreach ($aData as $aItem2) {

                    if ($aItem2[$this->_col_parent_id] == $aItem[$this->_col_id]) {

                        $sHtml .= "<li>";


                        $url = Phpfox::getLib('url')->makeUrl($this->_module . '.categories', array($aItem2[$this->_col_id], $aItem2['name_url'], 'view_'));
                        $sHtml .= '<a   href="' . $url . '">' . $aItem2[$this->_col_name] . '</a>';
                        // LEVEL 3
                        foreach ($aData as $aItem3) {
                            if ($aItem3[$this->_col_parent_id] == $aItem2[$this->_col_id]) {
                                $sHtml .= "<ul>";
                            }
                        }
                        foreach ($aData as $aItem3) {

                            if ($aItem3[$this->_col_parent_id] == $aItem2[$this->_col_id]) {

                                $sHtml .= "<li>";
                                $url = Phpfox::getLib('url')->makeUrl($this->_module . '.categories', array($aItem3[$this->_col_id], $aItem3['name_url'], 'view_'));
                                $sHtml .= '<a   href="' . $url . '">' . $aItem3[$this->_col_name] . '</a>';
                                $sHtml .= "</li>";

                            }
                        }

                        foreach ($aData as $aItem3) {
                            if ($aItem3[$this->_col_parent_id] == $aItem2[$this->_col_id]) {
                                $sHtml .= "</ul>";
                            }
                        }
                        $sHtml .= "</li>";
                    }
                }
                foreach ($aData as $aItem2) {

                    if ($aItem2[$this->_col_parent_id] == $aItem[$this->_col_id]) {
                        $sHtml .= "</ul>";
                    }
                }


                $sHtml .= "</li>";
            }

            if (($aItem[$this->_col_parent_id] == -1) && ($iparentID == 0)) {
                $sHtml .= "<li>";
                $url = Phpfox::getLib('url')->makeUrl($this->_module . '.categories', array($aItem[$this->_col_id], $aItem['name_url'], 'view_'));
                $sHtml .= '<a   href="' . $url . '">' . $aItem[$this->_col_name] . '</a>';
                $sHtml .= "</li>";
            }
        }

        $sHtml .= "<li>";
        $url = Phpfox::getLib('url')->makeUrl($this->_module . '.categories', array($aItem[$this->_col_id], $aItem['name_url'], 'view_'));
        $sHtml .= '<a   href="' . $url . '">' . $aItem[$this->_col_name] . '</a>';
        $sHtml .= "</li>";
        $sHtml .= '</ul>';
    }


    public function is_parent($aData, $aCheck, $iCurrent)
    {
        if ($iCurrent == -1) {
            return false;
        }

        $aCurrent = array();

        foreach ($aData as $one) {
            if ($one[$this->_col_id] == $iCurrent) {
                $aCurrent = $one;
            }
        }

        while ($aCurrent[$this->_col_parent_id] != 0) {
            if ($aCheck[$this->_col_id] == $aCurrent[$this->_col_parent_id] || $aCheck[$this->_col_parent_id] == $aCurrent[$this->_col_parent_id]) {
                return true;
            } else {
                foreach ($aData as $one) {
                    if ($one[$this->_col_id] == $aCurrent[$this->_col_parent_id]) {
                        $aCurrent = $one;
                        break;
                    }
                }
            }
        }
        return false;
    }


    /**
     * Build categories html select box for: add/edit category with parent, add/edit article
     * @param   array $css : array(id, name, class) of select box
     * @param   int $selected_id : id of current category,
     * @param   int $reduced_id : id of category you want to hide it and all child of it (for edit a category with parent)
     * @param   string $lang : language_id (if designed in db),
     * @return  html
     */
    public function getSelectBox($css = null, $selected_id = null, $reduced_id = null, $lang = null, $bIsParent = false)
    {
        $id = (is_array($css) && isset($css['id'])) ? $css['id'] : '';
        $name = (is_array($css) && isset($css['name'])) ? $css['name'] : '';
        $class = (is_array($css) && isset($css['class'])) ? $css['class'] : '';

        $select = '<select id="' . $id . '" name="' . $name . '" class="' . $class . '">';
        //if ($bIsParent)
        {
            $textSelect = _p('gettingstarted.select');
        }
        $select .= "\n\t" . '<option value="0">' . $textSelect . '</option>';

        $categories = $this->getItems($lang);

        $this->_catOption($select, $categories, 0, '', $selected_id, $reduced_id);

        $select .= "\n" . '</select>';
        return $select;
    }

    protected function _catOption(&$select, $categories, $pid, $refix, $selected_id, $reduced_id)
    {
        foreach ($categories as $category) {
            if ($category[$this->_col_id] == $reduced_id) {
                continue;
            }

            if ($category[$this->_col_parent_id] == $pid) {
                if ($selected_id == $category[$this->_col_id]) {
                    $selected = ' selected="selected"';
                } else {
                    $selected = '';
                }
                $select .= "\n\t" . '<option value="' . $category[$this->_col_id] . '"' . $selected . '>' . $refix . $category[$this->_col_name] . '</option>';
            }

            if (!empty($category['items'])) {
                $this->_catOption($select, $category['items'], $category[$this->_col_id], '&nbsp&nbsp&nbsp&nbsp' . $refix, $selected_id, $reduced_id);
            }
        }
    }
}

?>
