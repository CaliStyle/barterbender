<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

class Directory_Component_Block_Form_Invite_Friend extends Phpfox_Component
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

       	$aBusiness = Phpfox::getService('directory')->getQuickBusinessById($iId);
		
		//in case user leave message box empty
	  	$sMessage = _p('directory.full_name_invited_you_to_title_to_check_out_this_business_follow_the_link_below_a_href_link_link_a', array(
				'full_name' => Phpfox::getUserBy('full_name'),
				'title' => $oParseInput->clean($aBusiness['name'], 55),
				'link' => $sUrl
			)
		);

	    $sSubject = _p('directory.full_name_invited_you_to_the_business_title', array(
				'full_name' => Phpfox::getUserBy('full_name'),
				'title' => $oParseInput->clean($aBusiness['name'], 55),
			)
		);

		if(!$aBusiness)
		{
			return FALSE;
		}

        $this->template()->assign(array(
            'aBusiness' 	=> $aBusiness,
            'sUrl'      => $sUrl,
            'sSubject'	=> $sSubject,
            'sMessage'	=> $sMessage
        ));
    }
    
}

?>