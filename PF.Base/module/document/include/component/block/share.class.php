<?php
defined('PHPFOX') or exit('NO DICE!');
/**
 * 
 * 
 * @copyright        [YOUNET_COPYRIGHT]
 * @author          YouNet Company
 * @package         YouNet_Document
 */
 
class Document_Component_Block_Share extends Phpfox_Component
{
    public function process()
    {
        $aUser = $this->getParam('aUser');
        if(isset($aUser['user_id']) && $aUser['user_id'] == Phpfox::getUserId())
        {
            $this->template()->assign(array(
                'bProfile' => true
            ));
        }

        $sCategories = Phpfox::getService('document.category')->get();
        $sCategories = str_replace('js_mp_category_list"', 'js_mp_category_list_fx js_mp_category_list feed_validation" onchange="validateFeedInput();"', $sCategories);

        $this->template()->assign(array(
            'max_file_size_mb' => (Phpfox::getUserParam('document.document_max_file_size') === 0) ? 200 : Phpfox::getUserParam('document.document_max_file_size'),
            'sCategories' => $sCategories,
        ));
    }
}