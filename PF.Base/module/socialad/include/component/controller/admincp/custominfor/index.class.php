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


class Socialad_Component_Controller_Admincp_Custominfor_Index extends Phpfox_Component 
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		$aVals = $this->request()->getArray('val');
		if($aVals) { //if a form is submitted, we handle the form 
			$this->handleSubmitForm($aVals);
		}

		$aRows = Phpfox::getService('socialad.custominfor')->getAllCustomInfors();
		$aCustoms = array(
			array(
				'phrase' => _p('terms_and_conditions'),
				'content' => $aRows['terms_and_conditions'],
				'content_parsed' => $aRows['terms_and_conditions_parsed'],
				'type_id' => 'terms_and_conditions'
			),
			array(
				'phrase' => _p('pay_later_instructions'),
				'content' => $aRows['pay_later_instructions'],
				'content_parsed' => $aRows['pay_later_instructions_parsed'],
				'type_id' => 'pay_later_instructions'
			),

		);
		$this->template()
            ->setBreadCrumb(_p("Apps"), $this->url()->makeUrl('admincp.apps'))
            ->setBreadCrumb(_p("module_socialad"), $this->url()->makeUrl('admincp.app').'?id=__module_socialad')
			->setBreadcrumb(_p('custom_information'))
			->assign(array(
			'aCustoms' => $aCustoms
		));
		Phpfox::getService('socialad.helper')->loadAdminSocialAdJsCss();
	}

	public function handleSubmitForm($aVals) {

		if(Phpfox::getService('socialad.custominfor.process')->handleSubmitForm($aVals)) {
			Phpfox::getLib('url')->send('admincp.socialad.custominfor.index', array(), $sMessage = _p('update_successfully'));
		}
	}

}

