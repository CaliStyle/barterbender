<?php
namespace Apps\P_AdvEvent\Block;

use Phpfox;
use Phpfox_Component;

class CategoryBlock extends Phpfox_Component
{
    public function process()
    {
        if (defined('PHPFOX_IS_USER_PROFILE')) {
            return false;
        }

        $bInHomepage = $this->getParam('bInHomepage', false);
        $blockLocation = $this->getParam('location', 0);  // 1,9 left 3,10 right
        $isSideLocation = Phpfox::getService('fevent.helper')->isSideLocation($blockLocation);

        if (!($bInHomepage || $isSideLocation)) {
            return false;
        }

        $sCategory = $this->getParam('sCategory');
        if ($sCategory) {
            $parentCategoryId = Phpfox::getService('fevent.category')->getParentCategoryId($sCategory);
        } else {
            $parentCategoryId = 0;
        }

        $aCategories = Phpfox::getService('fevent.category')->getForBrowse($parentCategoryId);

        if (!is_array($aCategories))
        {
            return false;
        }

        if (!count($aCategories))
        {
            return false;
        }
        $sJdpickerPhrases = Phpfox::getService('fevent')->getJdpickerPhrases();
        echo "<script type='text/javascript'>".$sJdpickerPhrases."</script>";
        $this->template()->assign(array(
                'sHeader' => (empty($parentCategoryId) ? _p('categories') : _p('sub_categories')),
                'aCategories' => $aCategories,
                'sCustomClassName' => 'ync-block',
                'sCategory' => $sCategory,
                'iParentCategoryId' => $sCategory
            )
        );

        return 'block';
    }
}