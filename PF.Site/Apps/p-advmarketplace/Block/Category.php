<?php

namespace Apps\P_AdvMarketplace\Block;

use Phpfox_Component;
use Phpfox;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');

class Category extends Phpfox_Component
{
    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
        if (defined('PHPFOX_IS_USER_PROFILE')) {
            return false;
        }

        $bIsSearch = $this->getParam('bIsSearch');
        $blockLocation = $this->getParam('location', 0);  // 1,9 left 3,10 right
        $isSideLocation = Phpfox::getService('advancedmarketplace.helper')->bIsSideLocation($blockLocation);
        if ($bIsSearch && !$isSideLocation) {
            return false;
        }

        $sCategory = $this->getParam('sCategory');
        if ($sCategory) {
            $parentCategoryId = Phpfox::getService('advancedmarketplace.category')->getParentCategoryId($sCategory);
        } else {
            $parentCategoryId = 0;
        }

        $aCategories = Phpfox::getService('advancedmarketplace.category')->getForBrowse($parentCategoryId);

        if (empty($aCategories) || !is_array($aCategories))
        {
            return false;
        }

        $this->template()->assign(array(
                'aCategories' => $aCategories,
                'sCategory' => $sCategory,
                'sHeader' => _p('categories'),
                'sCustomClassName' => 'ync-block',
                'iCurrentCategory' => $sCategory,
                'iParentCategoryId' => $sCategory
            )
        );

        return 'block';
    }



    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('advancedmarketplace.component_block_category_clean')) ? eval($sPlugin) : false);
    }
}
