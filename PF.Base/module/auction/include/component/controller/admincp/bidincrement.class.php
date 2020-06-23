<?php

defined('PHPFOX') or exit('NO DICE!');

class Auction_Component_Controller_Admincp_Bidincrement extends Phpfox_Component {

    public function process()
    {
        $aCategories = Phpfox::getService('ecommerce.category')->getParentCategory();
        $this->template()->assign([
            'corepath' => phpfox::getParam('core.path'),
        ]);
        $this->template()->setTitle(_p('define_bid_increment'))
            ->setBreadcrumb(_p('Apps'), $this->url()->makeUrl('admincp.apps'))
            ->setBreadCrumb(_p('auction'), $this->url()->makeUrl('admincp.app',['id' => '__module_auction']))
            ->setBreadcrumb(_p('define_bid_increment'))
                ->setPhrase(array('auction.please_field_new_row_before_add_more', 'auction.to_field_must_be_greater_than_from_field_in_each_rows'))
                ->assign(array('aCategories' => $aCategories));
    }

}

?>