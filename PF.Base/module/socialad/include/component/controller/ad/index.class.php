<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright		[YOUNET_COPPYRIGHT]
 * @author  		MinhTA
 * @package  		Module_socialad
 */


class Socialad_Component_Controller_Ad_Index extends Phpfox_Component 
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{

		Phpfox::isUser(true);
		
		$aParams = array(
			'fb_small_loading_image_url' => Phpfox::getLib('template')->getStyle('image', 'ajax/add.gif'),
			'ajax_file_url' => Phpfox::getParam('core.path') . 'static/ajax.php',			
		);
		$this->template()
			->setBreadcrumb(_p('ad'), $this->url()->makeUrl('socialad.ad'))
			->setBreadcrumb( _p('my_ads'), '', true);
		$this->template()->setHeader(array(
			'ynsocialad.js' => 'module_socialad',
			'ynsocialad.ajaxForm.js' => 'module_socialad',
			'jquery.validate.js' => 'module_socialad',
			'chosen.jquery.min.js' => 'module_socialad',
			'ajax-chosen.js' => 'module_socialad',
			'<script type="text/javascript">$Behavior.loadYnsocialAdSetupParam = function() { ynsocialad.setParams(\''. json_encode($aParams) .'\'); }</script>'
		));
		$this->template()->assign(array(
			'sCorePath' => Phpfox::getParam('core.path')
		));
	}

	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
	
		(($sPlugin = Phpfox_Plugin::get('socialad.Socialad_Component_Controller_Index_clean')) ? eval($sPlugin) : false);
	}

}

