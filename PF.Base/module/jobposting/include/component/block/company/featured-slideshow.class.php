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
class JobPosting_Component_Block_Company_Featured_Slideshow extends Phpfox_Component
{
    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
       	$iLimit = 10;
		$sCond = 'ca.is_sponsor = 1 AND is_activated = 1';
		$order = 'ca.time_stamp desc';
		$aFeaturedCompany = Phpfox::getService('jobposting.company')->getBlockCompany($sCond, $order, $iLimit);
		if(count($aFeaturedCompany)==0)
			return false;
		
        $this->template()->assign(array(
                'aFeaturedCompany' => $aFeaturedCompany,
                'sNoimageUrl' => Phpfox::getLib('template')->getStyle('image', 'noimage/' . 'profile_50.png'),
                'sCorePath' => Phpfox::getParam('core.path'),
            ));
		
        return 'block';
    }
}

?>