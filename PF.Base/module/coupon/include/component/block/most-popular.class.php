<?php

defined('PHPFOX') or exit('NO DICE!');

class Coupon_Component_Block_Most_Popular extends Phpfox_Component {

    /**
     * Class process method wnich is used to execute this component.
     */
    public function process() {
        if(!$this->getParam('bInHomepageFr'))
        {
            return false;
        }

        $iLimit = Phpfox::getParam('coupon.limit_of_coupon_in_a_block');
        if(!$iLimit)
        {
            $iLimit = 2;
        }

        $aMostPopularCoupons = Phpfox::getService('coupon')->getCoupon($sType = 'most-popular', $iLimit );

        if(!$aMostPopularCoupons)
        {
            return false;
        }

        $this->template()->assign(array(
                'aMostPopularCoupons' => $aMostPopularCoupons,
                'sHeader' => _p('most_popular'),
                'sDefaultLink' => Phpfox::getParam('core.url_module') . "coupon/static/image/default/noimage.png",
                'aFooter' => array(
                    _p('view_more') => $this->url()->makeUrl('coupon') . '?view=running&sort=most-popular'
                ),
            )
        );
        return 'block';
    }

}

?>