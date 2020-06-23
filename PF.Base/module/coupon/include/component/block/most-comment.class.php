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
class Coupon_Component_Block_Most_Comment extends Phpfox_Component {

    /**
     * Class process method wnich is used to execute this component.
     */
    public function process() {
        $iLimit = 10;

        $aMostCommentCoupons = Phpfox::getService('coupon')->getCoupon($sType = 'most-comment', $iLimit);

        if(!$aMostCommentCoupons)
        {
            return false;
        }

        $this->template()->assign(array(
                'aMostCommentCoupons' => $aMostCommentCoupons,
                'sHeader' => _p('most_comment'),
                'sDefaultLink' => Phpfox::getParam('core.url_module') . "coupon/static/image/default/noimage.png",
                'aFooter' => array(
                    _p('view_more') => $this->url()->makeUrl('coupon') . '?view=running&sort=most-comment'
                ),
            )
        );

        return 'block';
    }

}

?>