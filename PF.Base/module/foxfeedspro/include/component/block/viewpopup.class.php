<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 *
 * @copyright      YouNet Company
 * @author         TienNPL
 * @package        Module_FoxFeedsPro
 * @version        3.02
 * 
 */
 
 Class FoxFeedsPro_Component_Block_ViewPopup extends Phpfox_Component
 {
 	/*
	 * Process method which is used to process this component
	 */
 	public function process()
	{
		$iNewsId   = (int) $this->getParam('id');
		$oFoxFeedsPro = Phpfox::getService('foxfeedspro');
		
		$aNews = $oFoxFeedsPro->getNewsById($iNewsId, FALSE);

        $this->template()->assign(array(
        	'aNews'	    => $aNews,
        	'sCorePath' => Phpfox::getParam('core.path')
		));
		
        return 'block';
	}
 }
 
 ?>	