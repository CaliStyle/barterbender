<?php
if(Phpfox::isAppActive('P_AdvMarketplace') && $this->get('is_advancedmarketpalce_contact_seller') && $this->get('listing_id')) {
    echo '<script type="text/javascript">appAdvMarketplace.prepareComposeMessage('. $this->get('listing_id') .');</script>';
}