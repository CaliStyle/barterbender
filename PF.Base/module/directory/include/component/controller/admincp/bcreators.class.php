<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Directory_Component_Controller_Admincp_Bcreators extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{

		// Page Number & Limit Per Page
        $iPage = $this->request()->getInt('page');
		$iPageSize = 10;

        list($iCount,$aCreators) = Phpfox::getService('directory')->getManageBusinessCreator($iPage,$iPageSize);

		if ($aVals = $this->request()->getArray('bcreator'))
        {
            if (Phpfox::getService('directory.process')->addBusinessCreator($aVals))
            {
                    $this->url()->send('admincp.directory.bcreators', array(), _p('directory.business_creator_successfully_added'));
            }
        }

		if ($iBcreatorDelete = $this->request()->getInt('delete'))
		{

			if (Phpfox::getService('directory.process')->deleteBusinessCreator($iBcreatorDelete))
			{
					$this->url()->send('admincp.directory.bcreators', array(), _p('directory.business_creator_successfully_deleted'));
			}
		}

			Phpfox::getLib('pager')->set(array(
						'page'  => $iPage, 
						'size'  => $iPageSize, 
						'count' => $iCount
			));

		$this->template()->setTitle(_p('directory.manage_business_creators'))
            ->setBreadCrumb(_p("Apps"), $this->url()->makeUrl('admincp.apps'))
            ->setBreadCrumb(_p('controller_directory'), $this->url()->makeUrl('admincp.app').'?id=__module_directory')
			->setBreadcrumb(_p('directory.manage_business_creators'))
			->assign(array(
					'aCreators' => $aCreators,
					'sCorePath' => Phpfox::getParam('core.path'),
					'sCreatorLink' => $this->url()->makeUrl('admincp.directory.bcreators'),
						)
		)->setHeader(array(
			'admin.js' => 'module_directory',
			'<script type="text/javascript">$Behavior.setURLDirectory = function() { $Core.directory.url(\'' . $this->url()->makeUrl('admincp.directory.bcreators') . '\'); } </script>'
		))->setPhrase(
			array('directory.search_user_by_their_name')
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