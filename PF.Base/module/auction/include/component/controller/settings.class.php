<?php

defined('PHPFOX') or exit('NO DICE!');

class Auction_Component_Controller_Settings extends Phpfox_Component {

    public function process()
    {
        Phpfox::isUser(true);
        Phpfox::getService('auction.helper')->buildMenu();
		$aCategories = Phpfox::getService('ecommerce.category')->getParentCategory();
        $aSellerSettings = Phpfox::getService('ecommerce.sellersettings')->get(Phpfox::getUserId());
        $this->template()
				->setPhrase(array('ecommerce.please_field_new_row_before_add_more', 'ecommerce.to_field_must_be_greater_than_from_field_in_each_rows'))
                ->assign(array('sCategories' => Phpfox::getService('ecommerce.category')->get(), 'aForms' => $aSellerSettings))
                ->setTitle(_p('auctions'))
                ->setBreadcrumb(_p('settings'), $this->url()->makeUrl('auction.settings'));
        Phpfox::getService('auction.helper')->loadAuctionJsCss();
        
    }

    /**
     * This function is used to add plugin. Do not delete.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('ynecommerce.component_controller_settings_clean')) ? eval($sPlugin) : false);
    }

}

?>