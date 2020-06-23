<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

class Ynsocialstore_Component_Block_Store_Add_Faq extends Phpfox_Component
{

    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
        $faq_id = $this->getParam('faq_id');
        $store_id = $this->getParam('store_id');

        $aFaq = array();
        if($faq_id != ''){
            $aFaq = Phpfox::getService('ynsocialstore')->getFAQById($faq_id);
        }

        $this->template()->assign(array(
                'aFaq'             => $aFaq,
                'faq_id'           => $faq_id,
                'store_id'         => $store_id,
                'sCorePath'        => Phpfox::getParam('core.path'),
            )
        );

        return 'block';
    }

}

?>