<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

class Videochannel_Component_Block_Categoryinpage extends Phpfox_Component
{

    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
        $bDisplay = ($this->request()->get('req3') == 'videochannel');
        $iPageId = $this->request()->get('req2');
        $bIsProfile = false;
        if ($this->getParam('bIsProfile') === true && ($aUser = $this->getParam('aUser')))
        {
            $bIsProfile = true;
        }
        $iCategory = $this->getParam('sCategory');
        $aCategories = Phpfox::getService('videochannel.category')->getForBrowse('c.user_id = ' . ($bIsProfile ? $aUser['user_id'] : '0'),$iCategory);
        if (!is_array($aCategories))
        {
            return false;
        }

        if (!$aCategories)
        {
            return false;
        }

        foreach ($aCategories as $iKey => $aCategory)
        {
            $aCategories[$iKey]['url'] = $this->url()->permalink(array('pages.' . $iPageId . '.videochannel.category', 'view' => $this->request()->get('view')), $aCategory['category_id'], $aCategory['name']);;

            if (isset($aCategory['sub']))
            {
                foreach ($aCategories[$iKey]['sub'] as $iSubKey => $aSubCategory)
                {
                    $aCategories[$iKey]['sub'][$iSubKey]['url'] = $this->url()->permalink(array('pages.' . $iPageId . '.videochannel.category', 'view' => $this->request()->get('view')), $aSubCategory['category_id'], $aSubCategory['name']);
                }
            }
        }
        
        $arAssign = array(
            'sHeader' => ($iCategory) ? _p('sub_categories') : _p('videochannel.categories'),
            'aCategories' => $aCategories,
            'iCategoryChannelView' => $this->request()->getInt('req5'),
            'bDisplay' => $bDisplay
        );
        $this->template()->assign($arAssign);

        (($sPlugin = Phpfox_Plugin::get('videochannel.component_block_categories_process')) ? eval($sPlugin) : false);
        
        return 'block';
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        $this->template()->clean(array('aCategories'));

        (($sPlugin = Phpfox_Plugin::get('videochannel.component_block_categories_clean')) ? eval($sPlugin) : false);
    }

}

?>