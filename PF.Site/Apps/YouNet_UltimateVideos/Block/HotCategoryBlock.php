<?php

namespace Apps\YouNet_UltimateVideos\Block;

use Hamcrest\Core\PhpForm;
use Phpfox_Component;
use Phpfox;
use Phpfox_Request;

class HotCategoryBlock extends Phpfox_Component
{

    public function process()
    {
        $bIsSearch = $this->getParam('bIsSearch');
        $sBlockLocation = $this->getParam('location', 0);  // 1,9 left 3,10 right
        $bIsSideLocation = Phpfox::getService('ultimatevideo')->bIsSideLocation($sBlockLocation);

        if (defined('PHPFOX_IS_USER_PROFILE') || ($bIsSearch && !$bIsSideLocation)) {
            return false;
        }

        $iLimit = $this->getParam('limit', 3);
        if (!$iLimit) {
            return false;
        }

        $aCategories = Phpfox::getService('ultimatevideo.category')->getCategories('c.is_hot = 1', 'RAND()', $iLimit);

        if (!is_array($aCategories) || !count($aCategories)) {
            return false;
        }

        foreach ($aCategories as $key => $aCategory) {
            $aLatestVideo = Phpfox::getService('ultimatevideo')->getLastestVideoByCategoryId($aCategory['category_id']);
            if (!empty($aLatestVideo)) {
                $aCategories[$key]['image_path'] = $aLatestVideo['image_path'];
                $aCategories[$key]['image_server_id'] = $aLatestVideo['image_server_id'];
            }
        }

        $sCustomContainerClassName = $bIsSideLocation ? '' : 'owl-carousel ultimatevideo-slider-category-container-js';

        $this->template()->assign([
                'sHeader' => _p('hot_categories'),
                'aCategories' => $aCategories,
                'sCustomClassName' => 'p-block',
                'bIsSideLocation' => $bIsSideLocation,
                'sCustomContainerClassName' => $sCustomContainerClassName,
            ]
        );

        return 'block';
    }

    public function getSettings()
    {
        return array(
            array(
                'info' => _p('limit'),
                'description' => _p('item_limit'),
                'value' => 6,
                'type' => 'integer',
                'var_name' => 'limit',
            ),
        );
    }


    public function getValidation()
    {
        return array(
            'limit' => array(
                'def' => 'int:required',
                'min' => 0,
                'title' => _p('video_limit_is_required_and_must_greater_or_equal_zero')
            )
        );
    }
}