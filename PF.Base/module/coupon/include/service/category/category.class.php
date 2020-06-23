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
 */
class Coupon_Service_Category_Category extends Phpfox_Service
{

    private $_sDisplay = 'select';
    private $_iCnt = 0;
    protected  $_sOutput = '';

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('coupon_category');
    }

    public function getCategoryIds($iId)
    {
        $aCategories = $this->database()->select('category_id')->from(Phpfox::getT('coupon_category_data'))->where('coupon_id = '.(int)$iId)->execute('getSlaveRows');

        $aCache = array();
        foreach ($aCategories as $aCategory)
        {
            $aCache[] = $aCategory['category_id'];
        }

        return implode(',', $aCache);
    }

    public function getForEdit($iId)
    {
        $aRow = $this->database()->select('*')->from($this->_sTable)->where('category_id = '.(int)$iId)->execute('getRow');

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
        $aLanguages =Phpfox::getService('language')->getAll();
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
        if ($this->_sDisplay == 'admincp')
        {
            $sOutput = $this->_get(0, 1);
            return $sOutput;
        }
        else
        {
            if(empty($this->_sOutput)) {
                if ($this->_sDisplay == 'search')
                {
                    $this->_get(0, 1, $iSelected);
                }
                else
                {
                    $this->_get(0, 1);
                }
            }
            return $this->_sOutput;
        }
    }

    private function _get($iParentId, $iActive = null, $iSelected = null)
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
            elseif ($this->_sDisplay == 'search')
            {
                $display = (isset($iSelected) && ($this->isChild($iSelected, $iParentId) || $iSelected == $iParentId)) ? '' : 'display:none; ';
                $this->_sOutput .= '<div class="js_mp_parent_holder" id="js_mp_holder_'.$iParentId.'" '.($iParentId > 0 ? ' style="'.$display.'padding:5px 0px 0px 0px;"' : '').'>';
                $this->_sOutput .= '<select name="search[category]['.$iParentId.']" class="js_mp_category_list form-control" id="js_mp_id_'.$iParentId.'">'."\n";
                $this->_sOutput .= '<option value="">'.($iParentId === 0 ? _p('coupon.select') : _p('coupon.select_a_sub_category')).':</option>'."\n";
            }
            else
            {
                $this->_sOutput .= '<div class="js_mp_parent_holder" id="js_mp_holder_'.$iParentId.'" '.($iParentId > 0 ? ' style="display:none; padding:5px 0px 0px 0px;"' : '').'>';
                $this->_sOutput .= '<select name="val[category]['.$iParentId.']" class="js_mp_category_list form-control" id="js_mp_id_'.$iParentId.'">'."\n";
                $this->_sOutput .= '<option value="">'.($iParentId === 0 ? _p('coupon.select') : _p('coupon.select_a_sub_category')).':</option>'."\n";
            }

            foreach ($aCategories as $iKey => $aCategory)
            {
                $aCache[] = $aCategory['category_id'];

                if ($this->_sDisplay == 'option')
                {
                    $this->_sOutput .= '<option value="'.$aCategory['category_id'].'" id="js_mp_category_item_'.$aCategory['category_id'].'">'.($this->_iCnt > 0 ? str_repeat('&nbsp;', ($this->_iCnt * 2)).' ' : '').(( \Core\Lib::phrase()->isPhrase($aCategory['title'])) ? _p($aCategory['title']) : Phpfox::getLib('locale')->convert($aCategory['title'])).'</option>'."\n";
                }
                elseif ($this->_sDisplay == 'admincp')
                {
                    $sOutput .= '<li><img src="'.Phpfox::getLib('template')->getStyle('image', 'misc/draggable.png').'" alt="" /> <input type="hidden" name="order['.$aCategory['category_id'].']" value="'.$aCategory['ordering'].'" class="js_mp_order" /><a href="#?id='.$aCategory['category_id'].'" class="js_drop_down">'.((\Core\Lib::phrase()->isPhrase($aCategory['title'])) ? _p($aCategory['title']) : Phpfox::getLib('locale')->convert($aCategory['title'])).'</a>'.$this->_get($aCategory['category_id'], $iActive, $iSelected).'</li>'."\n";
                }
                elseif ($this->_sDisplay == 'search')
                {
                    $selected = (isset($iSelected) && ($iSelected == $aCategory['category_id'] || $this->isChild($iSelected, $aCategory['category_id']))) ? ' selected="selected"' : '';
                    $this->_sOutput .= '<option value="'.$aCategory['category_id'].'" id="js_mp_category_item_'.$aCategory['category_id'].'"'.$selected.' >'.((\Core\Lib::phrase()->isPhrase($aCategory['title'])) ? _p($aCategory['title']) : Phpfox::getLib('locale')->convert($aCategory['title'])).'</option>'."\n";
                }
                else
                {
                    $this->_sOutput .= '<option value="'.$aCategory['category_id'].'" id="js_mp_category_item_'.$aCategory['category_id'].'">'.((\Core\Lib::phrase()->isPhrase($aCategory['title'])) ? _p($aCategory['title']) : Phpfox::getLib('locale')->convert($aCategory['title'])).'</option>'."\n";
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
                    $this->_get($iCateoryId, $iActive, $iSelected);
                }
            }

            $this->_iCnt = 0;
        }
    }

    /**
     * get categories by Id or list of Ids seperated by comma
     * @by minhta
     * @param string $iCouponId purpose
     * @return
     */
    public function getCategoriesByCouponId($iCouponId)
    {

        $aCategories = $this->database()->select('pc.parent_id, pc.category_id, pc.title')->from(Phpfox::getT('coupon_category_data'), 'pcd')->join($this->_sTable, 'pc', 'pc.category_id = pcd.category_id')->where('pcd.coupon_id = '.(int)$iCouponId)->order('pc.parent_id ASC, pc.ordering ASC')->execute('getSlaveRows');

        if (!count($aCategories))
        {
            return null;
        }

        $aBreadcrumb = array();
        if (count($aCategories) > 1)
        {
            foreach ($aCategories as $aCategory)
            {
                $aBreadcrumb[] = array(((\Core\Lib::phrase()->isPhrase($aCategory['title'])) ? _p($aCategory['title']) : Phpfox::getLib('locale')->convert($aCategory['title'])), Phpfox::permalink('coupon.category', $aCategory['category_id'], $aCategory['title']));
            }
        }
        else
        {
            $aBreadcrumb[] = array(((\Core\Lib::phrase()->isPhrase($aCategories[0]['title'])) ? _p($aCategories[0]['title']) : Phpfox::getLib('locale')->convert($aCategories[0]['title'])), Phpfox::permalink('coupon.category', $aCategories[0]['category_id'], $aCategories[0]['title']));
        }

        return $aBreadcrumb;
    }

    /**
     * @TODO: LIST OF CATEGORY TO SELECT
     * <pre>
     * PhpFox::getService('coupon.category')->getCategories($aConds  = array() , $sSort = string);
     * </pre>
     * @by datlv
     * @param stringarray $aConds condition for query
     * @param string $sSort condition for sort in query
     * @return $aItems list of all categories
     */
    public function getCategories($aConds = 'c.parent_id = 0', $sSort = 'c.title ASC')
    {
        $aItems = $this->database()->select('c.category_id, c.title')->from(Phpfox::getT('coupon_category'), 'c')->where($aConds)->group('c.category_id')->order($sSort)->execute('getSlaveRows');
        return $aItems;
    }

    public function getForBrowse($iCategoryId = null)
    {
        $aCategories = $this->database()->select('mc.category_id, mc.title')->from($this->_sTable, 'mc')->where('mc.parent_id = 0'.($iCategoryId === null ? '0' : (int)$iCategoryId).' AND mc.is_active = 1')->order('mc.ordering ASC')->execute('getRows');

        foreach ($aCategories as $iKey => $aCategory)
        {
            $aCategories[$iKey]['url'] = Phpfox::permalink('coupon.category', $aCategory['category_id'], $aCategory['title']);

            //if ($sCategory === null)
            {
                $aCategories[$iKey]['sub'] = $this->database()->select('mc.category_id, mc.title')->from($this->_sTable, 'mc')->where('mc.parent_id = '.$aCategory['category_id'].' AND mc.is_active = 1')->order('mc.ordering ASC')->execute('getRows');

                foreach ($aCategories[$iKey]['sub'] as $iSubKey => $aSubCategory)
                {
                    $aCategories[$iKey]['sub'][$iSubKey]['url'] = Phpfox::permalink('coupon.category', $aSubCategory['category_id'], $aSubCategory['title']);
                }
            }
        }

        return $aCategories;
    }
    
    public function isChild($iId, $iParentId)
    {
        $aRow = $this->database()->select('*')->from($this->_sTable)->where('category_id = '.(int)$iId.' AND parent_id = '.(int)$iParentId)->execute('getSlaveRow');
        
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
		$aCategories = $this->database()->select('c.category_id')
			->from($this->_sTable, 'c')
			->where('c.parent_id = ' . (int) $iParentId)
			->execute('getRows');
		
		$sCategories = '';
		foreach ($aCategories as $aCategory)
		{
			$sCategories .= $aCategory['category_id'] . ',' . $this->_getChildIds($aCategory['category_id']);
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
        if ($sPlugin = Phpfox_Plugin::get('coupon.service_category_category__call'))
        {
            return eval($sPlugin);
        }

        /**
         * No method or plug-in found we must throw a error.
         */
        Phpfox_Error::trigger('Call to undefined method '.__class__.'::'.$sMethod.'()', E_USER_ERROR);
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
                $aRows[$iKey]['categories'] = $this->getForAdmin($aRow['category_id']);
            }
        }

        return $aRows;
    }

}

?>
