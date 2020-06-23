<?php

defined('PHPFOX') or exit('NO DICE!');

class Contest_Component_Block_Category extends Phpfox_Component {

    public function process() {
        $bIsProfile = false;
        if ($this->getParam('bIsProfile') === true && ($aUser = $this->getParam('aUser'))) {
            $bIsProfile = true;
        }
        $iCurrentCategory = $this->getParam('sCurrentCategory', null);
        $iParentCategoryId = $this->getParam('iParentCategoryId', 0);

        $aCategories = Phpfox::getService('contest.category')->getForBrowse();
        if (!is_array($aCategories)) {
            return false;
        }

        if (!$aCategories) {
            return false;
        }

        foreach ($aCategories as $iKey => $aCategory) {
            $aCategories[$iKey]['url'] = ($bIsProfile ? $this->url()->permalink(array($aUser['user_name'] . '.contest.category', 'view' => $this->request()->get('view')), $aCategory['category_id'], $aCategory['name']) : $this->url()->permalink(array('contest.category', 'view' => $this->request()->get('view')), $aCategory['category_id'], $aCategory['name']));
            if (isset($aCategory['sub'])) {
                foreach ($aCategories[$iKey]['sub'] as $iSubKey => $aSubCategory) {
                    $aCategories[$iKey]['sub'][$iSubKey]['url'] = ($bIsProfile ? $this->url()->permalink(array($aUser['user_name'] . '.contest.category', 'view' => $this->request()->get('view')), $aSubCategory['category_id'], $aSubCategory['name']) : $this->url()->permalink(array('contest.category', 'view' => $this->request()->get('view')), $aSubCategory['category_id'], $aSubCategory['name']));
                }
            }
        }

     
        $this->template()->assign(array(
            'sHeader' => _p('contest.categories'),
            'aCategories' => $aCategories,
            'iCurrentCategory' => $iCurrentCategory,
            'iParentCategoryId' => $iParentCategoryId,
            'iCategoryFundraisingView' => $this->request()->getInt('req3')
                )
        );

        (($sPlugin = Phpfox_Plugin::get('contest.component_block_categories_process')) ? eval($sPlugin) : false);

        return 'block';
    }

    public function clean() {
        $this->template()->clean(array(
            'aCategories'
                )
        );

        (($sPlugin = Phpfox_Plugin::get('contest.component_block_categories_clean')) ? eval($sPlugin) : false);
    }

}