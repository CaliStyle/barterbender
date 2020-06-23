<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

class Directory_Component_Block_Add_Faq extends Phpfox_Component
{

    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
        $faq_id = $this->getParam('faq_id');
        $business_id = $this->getParam('business_id');

        $aFaq = array();
        if($faq_id != ''){
            $aFaq = Phpfox::getService('directory')->getFAQById($faq_id);
        }

        $this->template()->assign(array(
                'aFaq'             => $aFaq,
                'faq_id'           => $faq_id,
                'business_id'      => $business_id,
                'sCorePath'        => Phpfox::getParam('core.path'),
                'sCustomClassName' => 'ync-block'
            )
        );

        return 'block';
    }

}

?>