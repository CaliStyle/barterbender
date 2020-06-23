<?php
namespace Apps\P_AdvEvent\Service;

use Phpfox;
use Phpfox_Service;
use Phpfox_Error;
use Phpfox_Plugin;

class MultiCat extends Phpfox_Service
{
    protected $_sTable;
    protected $_module = 'fevent';              //name of module
    protected $_col_name = 'name';              //name of column 'category name'
    protected $_col_id = 'category_id';         //name of column 'category id'
    protected $_col_parent_id = 'parent_id';    //name of column 'parent category id'
    protected $_cache_key = 'fevent_cat';

    /**
     * constructor
     * @return void
     */
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('fevent_category');
    }


    /**
     * Get all items in database and build to array
     * @param   string $lang    : language_id (if designed in database),
     * @return  array
     */
    public function getItems($lang = null) {
        $aItems = array();

        $this->_getItems($aItems, 0, $lang);

        return $aItems;
    }

    protected function _getItems(&$aItems, $pId, $lang = null) {
        $where = $this->_col_parent_id.'='.$pId;

        if($lang !== null) {
            $where .= ' AND language_id=\''.$lang.'\'';
        }

        $aTemps = $this->database()->select('*')
            ->from($this->_sTable)
            ->where($where)
            ->order('ordering ASC')
            ->execute('getSlaveRows');

        if(count($aTemps)) {
            foreach($aTemps as $k=>$aTemp) {
                $aItems[$k] = $aTemp;
                $this->_getItems($aItems[$k]['items'], $aItems[$k][$this->_col_id], $lang);
            }
        }
    }


//    /**
//     * Build Html menu from array items
//     * @param   array $css      : array(id, name, class) of ul
//     * @param   string $lang    : language_id (if designed in database),
//     * @return  html
//     */
    public function getMenu($iCurrent = 0, $css = null, $lang = null) {
        $aItems = $this->getItems($lang); //not cache items
        # build to Html
        $sHtml = '<div class="sub_section_menu">';
        $this->_renderItem($aItems, $iCurrent, $sHtml, $css);
        $sHtml .= '</div>';
        return $sHtml;
    }

    protected function _renderItem($aItems, $iCurrent, &$sHtml, $css = null) {
        $id = (is_array($css) && isset($css['id'])) ? $css['id'] : '';
        $name = (is_array($css) && isset($css['name'])) ? $css['name'] : '';
        $class = (is_array($css) && isset($css['class'])) ? $css['class'] : '';

        $aData = $this->database()->select('*')->from($this->_sTable)->execute('getSlaveRows');

        $sHtml .= '<ul id="'.$id.'" name="'.$name.'" class="action '.$class.'" >';

        foreach($aItems as $aItem) {
            $name_url = Phpfox::getLib('parse.input')->cleanTitle(Phpfox::getLib('locale')->convert($aItem[$this->_col_name]));
            #url: change it for your module
            if($sView = $this->request()->get('view'))
            {
                $url = Phpfox::getLib('url')->makeUrl($this->_module.'.category', array($aItem[$this->_col_id], $name_url, 'view_'.$sView));
            }
            else
            {
                $url = Phpfox::getLib('url')->makeUrl($this->_module.'.category', array($aItem[$this->_col_id], $name_url, 'view_all'));
            }

            if($iCurrent!=0)
            {
                if($aItem[$this->_col_id]==$iCurrent)
                {
                    $sHtml .= '<li class="active">';
                }
                elseif($this->is_parent($aData, $aItem, $iCurrent) || $aItem[$this->_col_parent_id]==0 || $aItem[$this->_col_parent_id]==$iCurrent)
                {
                    $sHtml .= '<li>';
                }
                else
                {
                    continue;
                }
            }
            else
            {
                if($aItem[$this->_col_parent_id]==0)
                {
                    $sHtml .= '<li>';
                }
                else
                {
                    continue;
                }
            }

            $sHtml .= '<a href="'.$url.'">'.Phpfox::getLib('locale')->convert($aItem[$this->_col_name]).'</a>';

            if(!empty($aItem['items'])) {
                $this->_renderItem($aItem['items'], $iCurrent, $sHtml, null);
            }

            $sHtml .= '</li>';
        }

        $sHtml .= '</ul>';
    }

    public function is_parent($aData, $aCheck, $iCurrent)
    {
        $aCurrent = array();

        foreach($aData as $one)
        {
            if($one[$this->_col_id]==$iCurrent)
            {
                $aCurrent = $one;
            }
        }

        if(empty($aCurrent))
        {
            return false;
        }

        while($aCurrent[$this->_col_parent_id] != 0)
        {
            if($aCheck[$this->_col_id]==$aCurrent[$this->_col_parent_id] || $aCheck[$this->_col_parent_id]==$aCurrent[$this->_col_parent_id])
            {
                return true;
            }
            else
            {
                foreach($aData as $one)
                {
                    if($one[$this->_col_id]==$aCurrent[$this->_col_parent_id])
                    {
                        $aCurrent = $one;
                        break;
                    }
                }
            }
        }
        return false;
    }


//    /**
//     * Build categories html select box for: add/edit category with parent, add/edit article
//     * @param   array $css          : array(id, name, class) of select box
//     * @param   int $selected_id    : id of current category,
//     * @param   int $reduced_id     : id of category you want to hide it and all child of it (for edit a category with parent)
//     * @param   string $lang        : language_id (if designed in db),
//     * @return  html
//    */
    public function getSelectBox($css = null, $selected_id = null, $reduced_id = null, $lang = null) {
        $id = (is_array($css) && isset($css['id'])) ? $css['id'] : '';
        $name = (is_array($css) && isset($css['name'])) ? $css['name'] : '';
        $class = (is_array($css) && isset($css['class'])) ? $css['class'] : '';

        $select = '<select id="'.$id.'" name="'.$name.'" class="'.$class.' form-control">';
        $select .= "\n\t".'<option value="">'._p('select').':</option>';

        $categories = $this->getItems($lang);

        $this->_catOption($select, $categories, 0, '', $selected_id, $reduced_id);

        $select .= "\n".'</select>';
        return $select;
    }

    protected function _catOption(&$select, $categories, $pid, $refix, $selected_id, $reduced_id) {
        foreach($categories as $category) {
            if($category[$this->_col_id] == $reduced_id) {
                continue;
            }

            if($category[$this->_col_parent_id] == $pid) {
                if($selected_id==$category[$this->_col_id]) {
                    $selected = ' selected="selected"';
                } else {
                    $selected = '';
                }
                $select .= "\n\t".'<option value="'.$category[$this->_col_id].'"'.$selected.'>'.$refix.Phpfox::getLib('locale')->convert($category[$this->_col_name]).'</option>';
            }

            if(!empty($category['items'])) {
                $this->_catOption($select, $category['items'], $category[$this->_col_id], '&nbsp&nbsp&nbsp&nbsp'.$refix, $selected_id, $reduced_id);
            }
        }
    }
}