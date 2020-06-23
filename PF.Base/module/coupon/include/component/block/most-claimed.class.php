<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

class Coupon_Component_Block_Most_Claimed extends Phpfox_Component {

    /**
     * Class process method wnich is used to execute this component.
     */
    public function process() {
        if(!$this->getParam('bInHomepageFr'))
        {
            return false;
        }

        $iLimit = 10;
        $aMostClaimedCoupons = Phpfox::getService('coupon')->getCoupon($sType = 'most-claimed', $iLimit );

        if(!$aMostClaimedCoupons)
        {
            return false;
        }

        $this->template()->assign(array(
                'aMostClaimedCoupons' => $aMostClaimedCoupons,
                'sHeader' => _p('most_claimed'),
                'sDefaultLink' => Phpfox::getParam('core.url_module') . "coupon/static/image/default/noimage.png",
                'aFooter' => array(
                    _p('view_more') => $this->url()->makeUrl('coupon') . '?view=running&sort=most-claimed'
                ),
            )
        );
        return 'block';
    }

}

?>
