<?php
if(Phpfox::isAppActive('P_AdvMarketplace') && $this->request()->get('is_advancedmarketpalce_contact_seller')) {
    $bCanOverrideChecks = true;
}