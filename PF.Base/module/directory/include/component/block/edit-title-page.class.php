<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

class Directory_Component_Block_Edit_Title_Page extends Phpfox_Component
{

    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
        $page_business_id = $this->getParam('data_id');

        $aPage = Phpfox::getService('directory')->getPageBusinessByDataId($page_business_id);

        $aPage['module_phrase'] = Phpfox::getLib('locale')->convert($aPage['module_phrase']);
        $this->template()->assign(array(
                'aPage'             => $aPage,
                'page_business_id'  => $page_business_id,
                'sCorePath' => Phpfox::getParam('core.path'),
                'sCustomClassName' => 'ync-block'
            )
        );

        return 'block';
    }

}

?>