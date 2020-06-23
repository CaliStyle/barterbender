<?php
/**
 * [PHPFOX_HEADER]
 */
namespace Apps\YouNet_UltimateVideos\Block;

use Phpfox;

defined('PHPFOX') or exit('NO DICE!');

class PopupCustomFieldCategoryBlock extends \Phpfox_Component
{
    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
        $iCategoryId = (int)$this->getParam('category_id');

        $sCategory = Phpfox::getService('ultimatevideo.category')->getForEdit($iCategoryId);
        $iParentCategoryId = $iCategoryId;
        if (isset($sCategory['parent_id']) && (int)$sCategory['parent_id'] > 0) {
            $iParentCategoryId = (int)$sCategory['parent_id'];
        }
        $iNewParentId = $iParentCategoryId;
        while ($iNewParentId != 0) {
            $iNewParentId = Phpfox::getService('ultimatevideo.category')->getParentCategoryId($iParentCategoryId);
            if ($iNewParentId != 0) {
                $iParentCategoryId = $iNewParentId;
            }
        }

        $aGroupsInfo = Phpfox::getService('ultimatevideo.category')->getCustomGroup($iParentCategoryId);

        foreach ($aGroupsInfo as $key => $aGroup) {
            $aGroupsInfo[$key]['phrase_var_name'] = _p($aGroup['phrase_var_name']);
        }

        $this->template()->assign(array(
                'sCategory' => $sCategory,
                'aGroupsInfo' => $aGroupsInfo,
            )
        );
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {

    }
}
