<?php

namespace Apps\YouNet_UltimateVideos\Block;

use Phpfox_Component;
use Phpfox;
use Phpfox_Request;

class CategoryBlock extends Phpfox_Component
{

    public function process()
    {
        if (defined('PHPFOX_IS_USER_PROFILE')) {
            return false;
        }

        $sBlockLocation = $this->getParam('location', 0);  // 1,9 left 3,10 right
        $bIsSideLocation = Phpfox::getService('ultimatevideo')->bIsSideLocation($sBlockLocation);
        $bIsSearch = $this->getParam('bIsSearch');

        if ($bIsSearch && !$bIsSideLocation) {
            return false;
        }

        $iCategoryId = $this->getParam('sCategory', 0);
        if ($iCategoryId) {
            $aParentCategoryId = Phpfox::getService('ultimatevideo.category')->getParentCategoryId($iCategoryId);
        } else {
            $aParentCategoryId = 0;
        }

        switch (\Phpfox_Module::instance()->getFullControllerName()) {
            case 'ultimatevideo.playlist':
                $aCategories = Phpfox::getService('ultimatevideo.category')->getForBrowsePlaylist($aParentCategoryId);
                break;
            default:
                $aCategories = Phpfox::getService('ultimatevideo.category')->getForBrowse($aParentCategoryId);
        }

        if (!(is_array($aCategories) && count($aCategories))) {
            return false;
        }

        $this->template()->assign([
                'sHeader' => _p('Categories'),
                'aCategories' => $aCategories,
                'iCurrentCategory' => $iCategoryId,
                'iParentCategoryId' => $iCategoryId
            ]
        );

        return 'block';
    }
}