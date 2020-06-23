<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

class Videochannel_Component_Block_Category extends Phpfox_Component
{

    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
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
            $strLink = '';

            if ($bIsProfile)
            {
                $strLink = $this->url()->permalink(array($aUser['user_name'] . '.videochannel.category', 'view' => $this->request()->get('view')), $aCategory['category_id'], $aCategory['name']);
            }
            else
            {
                $strLink = $this->url()->permalink(array('videochannel.category', 'view' => $this->request()->get('view')), $aCategory['category_id'], $aCategory['name']);
            }

            $aCategories[$iKey]['url'] = $strLink;

            if (isset($aCategory['sub']))
            {
                foreach ($aCategories[$iKey]['sub'] as $iSubKey => $aSubCategory)
                {
                    $strSubLink = '';
                    
                    if ($bIsProfile)
                    {
                        $strSubLink = $this->url()->permalink(array($aUser['user_name'] . '.videochannel.category', 'view' => $this->request()->get('view')), $aSubCategory['category_id'], $aSubCategory['name']);
                    }
                    else
                    {
                        $strSubLink = $this->url()->permalink(array('videochannel.category', 'view' => $this->request()->get('view')), $aSubCategory['category_id'], $aSubCategory['name']);
                    }
                    
                    $aCategories[$iKey]['sub'][$iSubKey]['url'] = $strSubLink;
                }
            }
        }

        $this->template()->assign(array(
            'sHeader' => ($iCategory) ? _p('sub_categories') : _p('videochannel.categories'),
            'aCategories' => $aCategories,
            'iCategoryChannelView' => $this->request()->getInt('req3'),
            'iLimitShowMore' => Phpfox::getParam('videochannel.subcategories_to_show_at_first'),
                )
        );

        (($sPlugin = Phpfox_Plugin::get('videochannel.component_block_categories_process')) ? eval($sPlugin) : false);

        return 'block';
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        $this->template()->clean(array(
            'aCategories'
                )
        );

        (($sPlugin = Phpfox_Plugin::get('videochannel.component_block_categories_clean')) ? eval($sPlugin) : false);
    }

}

?>