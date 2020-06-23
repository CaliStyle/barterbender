<?php

if(Phpfox::isAppActive('P_AdvMarketplace') && $this->request()->get('is_advancedmarketpalce_contact_seller') && $bClaiming) {
    $this->template()->assign([
        'iPageId' => null,
        'aPage' => null,
        'sMessageClaim' => null
    ]);
    $tempVals = $this->request()->get('val');
    if(!empty($tempVals['page_id'])) {
        unset($tempVals['page_id']);
        $this->request()->set('val', $tempVals);
    }
    if(!Phpfox_Error::isPassed()) {
        Phpfox_Error::reset();
    }
}