<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * @copyright      YouNet Company
 * @author         TienNPL, DatLV
 * @package        Module_Coupon
 * @version        3.01
 * 
 */
 
class Coupon_Component_Block_Form_Invite_Friend extends Phpfox_Component
{
    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
    	$oParseInput = Phpfox::getLib('parse.input');
		
        $iId = (int) $this->getParam('id');
		
		if(!$iId)
		{
			$iId = (int) $this->request()->get('id');
		}
		
        $sUrl = $this->getParam('url');

       	$aCoupon = Phpfox::getService('coupon')->getCouponById($iId);
		
		//in case user leave message box empty
	  	$sMessage = _p('full_name_invited_you_to_the_title', array(
				'full_name' => Phpfox::getUserBy('full_name'),
				'title' => $oParseInput->clean($aCoupon['title'], 55),
				'link' => $sUrl
			)
		);

	    $sSubject = _p('full_name_invited_you_to_the_coupon_title', array(
				'full_name' => Phpfox::getUserBy('full_name'),
				'title' => $oParseInput->clean($aCoupon['title'], 55),
			)
		);

		if(!$aCoupon)
		{
			return FALSE;
		}

        $this->template()->assign(array(
            'aCoupon' 	=> $aCoupon,
            'sUrl'      => $sUrl,
            'sSubject'	=> $sSubject,
            'sMessage'	=> $sMessage
        ));
    }
    
}

?>