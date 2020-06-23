<?php

defined('PHPFOX') or exit('NO DICE!');

class Ecommerce_Component_Block_Popup_Customfield_Category extends Phpfox_Component {

    public function process()
    {
        $iCategoryId = (int) $this->getParam('category_id');

        $sCategory = Phpfox::getService('ecommerce.category')->getForEdit($iCategoryId);
        $iParentCategoryId = $iCategoryId;
        if (isset($sCategory['parent_id']) && (int) $sCategory['parent_id'] > 0)
        {
            $iParentCategoryId = (int) $sCategory['parent_id'];
        }

        $aGroupsInfo = Phpfox::getService('ecommerce.category')->getCustomGroup($iParentCategoryId);

        foreach ($aGroupsInfo as $key => $aGroup)
        {
            $aGroupsInfo[$key]['phrase_var_name'] = _p($aGroup['phrase_var_name']);
        }

        $this->template()->assign(array(
            'sCategory' => $sCategory,
            'aGroupsInfo' => $aGroupsInfo,
                )
        );
    }

}

?>
