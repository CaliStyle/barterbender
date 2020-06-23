<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright		[YOUNET_COPPYRIGHT]
 * @author  		MinhTA
 * @package  		Module_foxfeedspro
 */


defined('PHPFOX') or exit('NO DICE!');

class Foxfeedspro_Component_Block_Category_News extends Phpfox_component {

    public function process() {


        if (Phpfox::getParam('foxfeedspro.is_using_advanced_category'))
        {
            return false;
        }

        $bIsProfile = false;
        if ($this->getParam('bIsProfile') === true && ($aUser = $this->getParam('aUser'))) {
            $bIsProfile = true;
        }

        $aCategories = Phpfox::getService('foxfeedspro.category')->getForBrowse();

        if (!is_array($aCategories)) {
            return false;
        }

        if (!$aCategories) {
            return false;
        }

        foreach ($aCategories as $iKey => $aCategory) {
            $aCategories[$iKey]['url'] = ($bIsProfile ? $this->url()->permalink(array($aUser['user_name'] . '.foxfeedspro.category', 'view' => $this->request()->get('view')), $aCategory['category_id'], $aCategory['name']) : $this->url()->permalink(array('foxfeedspro.category', 'view' => $this->request()->get('view')), $aCategory['category_id'], $aCategory['name']));
            if (isset($aCategory['sub'])) {
                foreach ($aCategories[$iKey]['sub'] as $iSubKey => $aSubCategory) {
                    $aCategories[$iKey]['sub'][$iSubKey]['url'] = ($bIsProfile ? $this->url()->permalink(array($aUser['user_name'] . '.foxfeedspro.category', 'view' => $this->request()->get('view')), $aSubCategory['category_id'], $aSubCategory['name']) : $this->url()->permalink(array('foxfeedspro.category', 'view' => $this->request()->get('view')), $aSubCategory['category_id'], $aSubCategory['name']));
                }
            }
        }

     
        $this->template()->assign(array(
            'sHeader' => _p('foxfeedspro.categories'),
            'aCategories' => $aCategories,
            'iCategoryItemView' => $this->request()->getInt('req3')
                )
        );

        (($sPlugin = Phpfox_Plugin::get('foxfeedspro.component_block_categories_process')) ? eval($sPlugin) : false);

        return 'block';
    }

    public function clean() {
        $this->template()->clean(array(
            'aCategories'
                )
        );

        (($sPlugin = Phpfox_Plugin::get('foxfeedspro.component_block_categories_clean')) ? eval($sPlugin) : false);
    }

}