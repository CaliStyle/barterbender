<?php

namespace Apps\YNC_Blogs\Block;

use Phpfox_Component;
use Phpfox;

class Category extends Phpfox_Component
{
    public function process()
    {
        $sBlockLocation = $this->getParam('location', 0);  // 1,9 left 3,10 right
        $bIsSideLocation = Phpfox::getService('ynblog.helper')->bIsSideLocation($sBlockLocation);
        $bIsSearch = $this->getParam('bIsSearch');

        if (($bIsSearch || Phpfox::getLib('module')->getFullControllerName() == 'ynblog.following') && !$bIsSideLocation) {
            return false;
        }

        if (defined('PHPFOX_IS_USER_PROFILE')) {
            return false;
        }

        $iCategoryId = ($this->request()->get('req2') == 'category' && $this->request()->get('req3') > 0) ? $this->request()->getInt('req3') : 0;
        if ($iCategoryId) {
            $aParentCategoryId = Phpfox::getService('ynblog.category')->getParentCategoryId($iCategoryId);
            $aParentCategory = Phpfox::getService('ynblog.category')->getCategory($aParentCategoryId);
        } else {
            $aParentCategoryId = 0;
        }

        $aCategories = Phpfox::getService('ynblog.category')->getForUsers($aParentCategoryId, 1, true);

        if (empty($aCategories)) {
            return false;
        }

        $this->template()
            ->assign([
                'sHeader' => $aParentCategoryId ? _p($aParentCategory['name']) : _p('Categories'),
                'aCategories' => $aCategories,
                'iCurrentCategory' => $iCategoryId,
                'iParentCategoryId' => $iCategoryId
            ]);

        return 'block';
    }
}
