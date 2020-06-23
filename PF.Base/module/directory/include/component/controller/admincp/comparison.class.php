<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Directory_Component_Controller_Admincp_Comparison extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{

		$aFields =  Phpfox::getService('directory')->getFieldsComparison();

		if ($aVals = $this->request()->getArray('val'))
        {
        	unset($aVals['submit']);
            if (Phpfox::getService('directory.process')->activateFieldComparison($aVals))
            {
                    $this->url()->send('admincp.directory.comparison', array(), _p('directory.field_comparison_successfully_updated'));
            }
        }


		$this->template()->setHeader(array(
			))->setTitle(_p('directory.manage_comparison'))
            ->setBreadCrumb(_p("Apps"), $this->url()->makeUrl('admincp.apps'))
            ->setBreadCrumb(_p('controller_directory'), $this->url()->makeUrl('admincp.app').'?id=__module_directory')
			->setBreadcrumb(_p('directory.manage_comparison'))
			->assign(array(
					'aFields' => $aFields,
						)
		);
	 
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		
	}
}

?>