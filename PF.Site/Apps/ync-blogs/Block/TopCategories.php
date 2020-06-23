<?php

namespace Apps\YNC_Blogs\Block;

use Phpfox_Component;
use Phpfox;

class TopCategories extends Phpfox_Component
{
    public function process()
    {
        if (defined('PHPFOX_IS_USER_PROFILE') || defined('PHPFOX_IS_AJAX_CONTROLLER')) {
            return false;
        }

        $iLimit = $this->getParam('limit', 6);
        if (!$iLimit) {
            return false;
        }
        $bIsSearch = $this->getParam('bIsSearch');
        $sBlockLocation = $this->getParam('location', 0);  // 1,9 left 3,10 right
        $bIsSideLocation = Phpfox::getService('ynblog.helper')->bIsSideLocation($sBlockLocation);

        if (($bIsSearch || Phpfox::getLib('module')->getFullControllerName() == 'ynblog.following') && !$bIsSideLocation) {
            return false;
        }

        $aCategories = Phpfox::getService('ynblog.blog')->getTopCategories($iLimit);

        if (empty($aCategories) || $this->request()->get('view') != '') {
            return false;
        }

        $this->template()
            ->assign(array(
                'sHeader' => _p('top_categories'),
                'aCategories' => $aCategories,
                'bIsSideLocation' => $bIsSideLocation,
                'sCustomClassName' => 'p-block',
            ));

        return 'block';
    }

    public function getSettings()
    {
        return [
            [
                'info' => _p('Top Categories Limit'),
                'description' => _p('Define the limit of how many top categories can be displayed when viewing the blog section. Set 0 will hide this block.'),
                'value' => 6,
                'type' => 'integer',
                'var_name' => 'limit',
            ]
        ];
    }

    /**
     * @return array
     */
    public function getValidation()
    {
        return [
            'limit' => [
                'def' => 'int',
                'min' => 0,
                'title' => '"Suggestion Top Categories Limit" must be greater than or equal to 0'
            ]
        ];
    }
}
