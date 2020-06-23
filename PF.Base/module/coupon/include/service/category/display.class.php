<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright      YouNet Company
 * @author         LyTK
 * @package        Module_Coupon
 * @version        3.01
 * 
 * @tutorial    Change your module name, column name follow in database
 *              Change your url pattern in function _renderItem
 *              Remove comment tag in function getHtml to use cache
 */
class Coupon_Service_Category_Display extends Phpfox_Service
{

        protected $_sTable;
        protected $_module = 'coupon';              //name of module
        protected $_col_name = 'title';              //name of column 'category name'
        protected $_col_id = 'category_id';         //name of column 'category id'
        protected $_col_parent_id = 'parent_id';    //name of column 'parent category id'
        protected $_cache_key = 'coupon_cat';

        /**
         * constructor
         * @return void
         */
        public function __construct()
        {
                $this->_sTable = Phpfox::getT('coupon_category');
        }

        /**
         * Get all items in database and build to array
         * @param   $lang (null or value)  : language_id (if designed in database), 
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

                if ($lang !== null)
                {
                        $where .= ' AND language_id=\'' . $lang . '\'';
                }

                $aTemps = $this->database()->select('*')
                        ->from($this->_sTable)
                        ->where($where)
                        ->order('ordering ASC')
                        ->execute('getSlaveRows');

                if (count($aTemps))
                {
                        foreach ($aTemps as $k => $aTemp)
                        {
                                $aItems[$k] = $aTemp;
                                $this->_getItems($aItems[$k]['items'], $aItems[$k][$this->_col_id], $lang);
                        }
                }
        }

        /**
         * Build Html menu from array items
         * @param   $css (null or array)   : array(id, name, class) of ul
         * @param   $lang (null or value)  : language_id (if designed in database), 
         * @return  html
         */
        public function getMenu($css = null, $lang = null)
        {
                /* If you want to cache items
                  # get items from cache
                  $sCacheId = $this->cache()->set($this->_cache_key);
                  $aItems = $this->cache()->get($sCacheId);

                  if (!$aItems)
                  {
                  $aItems = $this->getItems($lang);

                  # update cache
                  $this->cache()->save($sCacheId, $aItems);
                  }
                 */

                $aItems = $this->getItems($lang); //do not cache items

                $sHtml = '';

                # build to Html
                $this->_renderItem($aItems, $sHtml, $css);

                return $sHtml;
        }

        protected function _renderItem($aItems, &$sHtml, $css = null)
        {
                $id = (is_array($css) && isset($css['id'])) ? $css['id'] : '';
                $name = (is_array($css) && isset($css['name'])) ? $css['name'] : '';
                $class = (is_array($css) && isset($css['class'])) ? $css['class'] : '';

                $sHtml .= '<ul id="' . $id . '" name="' . $name . '" class="' . $class . '">';

                foreach ($aItems as $aItem)
                {
                        $name_url = Phpfox::getLib('parse.input')->cleanTitle($aItem[$this->_col_name]);
                        #url: change it for your module
                        $url = Phpfox::getLib('url')->makeUrl($this->_module . '.category', array($aItem[$this->_col_id], $name_url, 'when_upcoming'));

                        $sHtml .= '<li><a href="' . $url . '"><span>' . $aItem[$this->_col_name] . '</span></a>';

                        if (isset($aItem['items']))
                        {
                                $this->_renderItem($aItem['items'], $sHtml, null);
                        }

                        $sHtml .= '</li>';
                }

                $sHtml .= '</ul>';
        }

        /**
         * Build categories html select box for: add/edit category with parent, add/edit article
         * @param   $css (null or array)        : array(id, name, class) of select box
         * @param   $selected_id (null or value): id of current category, 
         * @param   $reduced_id (null or value) : id of category you want to hide it and all child of it (for edit a category with parent)
         * @param   $lang (null or value)       : language_id (if designed in db), 
         * @return  html
         */
        public function getSelectBox($css = null, $selected_id = null, $reduced_id = null, $lang = null)
        {
                $id = (is_array($css) && isset($css['id'])) ? $css['id'] : '';
                $name = (is_array($css) && isset($css['name'])) ? $css['name'] : '';
                $class = (is_array($css) && isset($css['class'])) ? $css['class'] : '';

                $select = '<select id="' . $id . '" name="' . $name . '" class="' . $class . '">';
                $select .= "\n\t" . '<option value="0">Select:</option>';

                $categories = $this->getItems($lang);

                $this->_catOption($select, $categories, 0, '', $selected_id, $reduced_id);

                $select .= "\n" . '</select>';
                return $select;
        }

        protected function _catOption(&$select, $categories, $pid, $refix, $selected_id, $reduced_id)
        {
                foreach ($categories as $category)
                {
                        if ($category[$this->_col_id] == $reduced_id)
                        {
                                continue;
                        }

                        if ($category[$this->_col_parent_id] == $pid)
                        {
                                if ($selected_id == $category[$this->_col_id])
                                {
                                        $selected = ' selected="selected"';
                                } else
                                {
                                        $selected = '';
                                }
                                $select .= "\n\t" . '<option value="' . $category[$this->_col_id] . '"' . $selected . '>' . $refix . $category[$this->_col_name] . '</option>';
                        }

                        if (!empty($category['items']))
                        {
                                $this->_catOption($select, $category['items'], $category[$this->_col_id], '&nbsp&nbsp&nbsp&nbsp' . $refix, $selected_id, $reduced_id);
                        }
                }
        }

}

?>
