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
class Coupon_Component_Block_Featured_Slideshow extends Phpfox_Component
{

    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
        $iLimit = Phpfox::getParam("coupon.number_of_featured_coupons");
        if(!$iLimit)
        {
        	$iLimit = 5;
		}

        $aFeaturedCoupons = Phpfox::getService('coupon')->getCoupon($sType = 'featured', $iLimit);

        if(empty($aFeaturedCoupons))
        {
            return false;
        }
        $this->template()->setHeader(array(
                            'owl.carousel.css' => 'module_coupon'));
        $this->template()->assign(array(
        		'sHeader' => _p('featured_coupons'),
                'aFeaturedCoupons' => $aFeaturedCoupons,
                'sNoimageUrl' => Phpfox::getLib('template')->getStyle('image', 'noimage/' . 'profile_50.png'),
                'sCorePath' => Phpfox::getParam('core.path'),
                'sDefaultLink' => Phpfox::getParam('core.url_module') . "coupon/static/image/default/noimage.png"
            )
        );

        return 'block';
    }

}

?>