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
 
 Class FoxFeedsPro_Component_Block_Js_Block_Remove_Add_Button extends Phpfox_Component
 {
 	/*
	 * Process method which is used to process this component
	 */
 	public function process()
	{
		$sYnJs = '';
		if (!Phpfox::getParam('foxfeedspro.is_using_advanced_category'))
        {
            //$sYnJs .= "<script type='text/javascript'>\$('.breadcrumbs_menu ul li ').last().remove();</script>";

        }

		$this->template()->assign(array(
			'sYnJs' => $sYnJs
		));
	}
}


?>