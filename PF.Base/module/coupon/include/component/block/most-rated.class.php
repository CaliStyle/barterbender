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
class Coupon_Component_Block_Most_Rated extends Phpfox_Component {

    /**
     * Class process method wnich is used to execute this component.
     */
    public function process() {
        $iLimit = 10;

        $aMostRateCoupons = Phpfox::getService('coupon')->getCoupon($sType = 'most-rated', $iLimit);

        if(!$aMostRateCoupons)
        {
            return false;
        }

        $this->template()->assign(array(
                'aMostRateCoupons' => $aMostRateCoupons,
                'sHeader' => _p('most_rated'),
                'sDefaultLink' => Phpfox::getParam('core.url_module') . "coupon/static/image/default/noimage.png",
                'aFooter' => array(
                    _p('view_more') => $this->url()->makeUrl('coupon') . '?view=running&sort=most-rated'
                ),
            )
        );

        return 'block';
    }

}

?>