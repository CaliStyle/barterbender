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
class JobPosting_Component_Block_Job_Featured_Slideshow extends Phpfox_Component
{

    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
       	$iLimit = 10;
		$sCond = 'job.is_featured = 1 AND job.is_activated = 1';
		$order = 'job.time_stamp desc';
		$aFeaturedJobs = Phpfox::getService('jobposting.job')->getBlockJob($sCond, $order, $iLimit);

        $this->template()->assign(array(
                'aFeaturedJobs' => $aFeaturedJobs,
                'sNoimageUrl' => Phpfox::getLib('template')->getStyle('image', 'noimage/' . 'profile_50.png'),
                'sCorePath' => Phpfox::getParam('core.path'),
            ));
        return 'block';
    }

}

?>