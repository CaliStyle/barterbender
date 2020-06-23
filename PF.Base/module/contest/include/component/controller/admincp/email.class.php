<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Contest_Component_Controller_Admincp_Email extends Phpfox_Component
{
	/**
	 * Class process method which is used to execute this component.
	 */
	public function process()
	{

		$iTypeId = 0;
        if ($this->request()->getArray('val')) {
            $aVals = $this->request()->getArray('val');

            if($aVals['type_id'] != 0)
			{
				$iTypeId = $aVals['type_id'];
                Phpfox::getService('contest.mail.process')->addEmailTemplate($aVals);
			}
        }

        $aTemplateTypes = Phpfox::getService('contest.constant')->getAllEmailTemplateTypesWithPhrases();

        $this->template()->assign(array(
            'aTemplateTypes' => $aTemplateTypes,
			'iCurrentTypeId' => $iTypeId
        ))
            ->setTitle(_p('contest.contest'))
            ->setBreadCrumb(_p("Apps"), $this->url()->makeUrl('admincp.apps'))
            ->setBreadCrumb(_p('module_contest'), $this->url()->makeUrl('admincp.app').'?id=__module_contest')
            ->setBreadCrumb(_p('contest.manage_email_templates'),  $this->url()->makeUrl('admincp.contest.email'))
            ->setEditor();
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('contest.component_controller_admincp_email_clean')) ? eval($sPlugin) : false);
	}
}

?>