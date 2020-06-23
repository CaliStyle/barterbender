<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

/**
 *
 *
 * @copyright      YouNet Company
 * @author         DatLV
 * @package        Module_Coupon
 * @version        3.01
 */
class Coupon_Component_Block_Most_Liked extends Phpfox_Component {

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

        $aMostLikeCoupons = Phpfox::getService('coupon')->getCoupon($sType = 'most-liked', $iLimit);

        if(!$aMostLikeCoupons)
        {
            return false;
        }

        $this->template()->assign(array(
                'aMostLikeCoupons' => $aMostLikeCoupons,
                'sHeader' => _p('most_liked'),
                'sDefaultLink' => Phpfox::getParam('core.url_module') . "coupon/static/image/default/noimage.png",
                'aFooter' => array(
                    _p('view_more') => $this->url()->makeUrl('coupon') . '?view=running&sort=most-liked'
                ),
            )
        );

        return 'block';
    }

}

?>