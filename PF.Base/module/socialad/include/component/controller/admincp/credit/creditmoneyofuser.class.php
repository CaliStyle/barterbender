
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

// Add and edit request both go here 
class Socialad_Component_Controller_Admincp_Credit_Creditmoneyofuser extends Phpfox_Component 
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		Phpfox::isUser(true);

		$userid = $this->request()->get('userid');
		$aUser = Phpfox::getService('user')->getUser((int)$userid);
		$this->setParam('yncm_user_id', $userid);

		$this->template()
			->setTitle(_p('credit_details_of') . ' "' . $aUser['full_name'] . '"')
            ->setBreadCrumb(_p("Apps"), $this->url()->makeUrl('admincp.apps'))
            ->setBreadCrumb(_p("module_socialad"), $this->url()->makeUrl('admincp.app').'?id=__module_socialad')
			->setBreadcrumb(_p('credit_details_of') . ' "' . $aUser['full_name'] . '"');
		Phpfox::getService('socialad.helper')->loadAdminSocialAdJsCss();
        $this->setParam('bIsAdminManage', true);
	}

	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
	
	}

}

