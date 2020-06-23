<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Directory_Component_Controller_Admincp_Customfield_Add extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
        $bIsEditGroup = false;
        $bAddInput = false;

		$aValidation = array(
			'group_name' => array(
				'def' => 'required',
				'title'=> _p('directory.group_name_cannot_be_empty')
			),
		);

        if ($iEditGroupId = $this->request()->getInt('id'))
        {
                if ($aGroup = Phpfox::getService('directory.custom.group')->getGroupForEdit($iEditGroupId))
                {
                	$bAddInput = true;
                        $bIsEditGroup = true;
						$aCategories = Phpfox::getService('directory.category')->getParentCategory();
						$totalCategory = count($aCategories);
                        $this->template()->assign(array(
	                        	'aGroup' => $aGroup,
	                        	'aCategories' => $aCategories,
								'totalCategory' => $totalCategory,
	                        	'bIsEditGroup' =>$bIsEditGroup
                        	));
                }
        }
		
		if ($aVals = $this->request()->getArray('val'))
        {
        		if ($this->__isValid($aVals)){
	               
	                if ($bIsEditGroup)
	                {
	                        if (Phpfox::getService('directory.custom.group')->updateGroup($aGroup['group_id'], $aVals))
	                        {
	                                $this->url()->send('admincp.directory.customfield.add', array('id' => $aGroup['group_id']), _p('directory.custom_field_groups_successfully_updated'));
	                        }
	                } else
	                {
	                        if ($iGroupId = Phpfox::getService('directory.custom.group')->addGroup($aVals))
	                        {
	                                $this->url()->send('admincp.directory.customfield.add', array('id' => $iGroupId), _p('directory.custom_field_groups_successfully_added'));
	                        }
	                }
	            }
        }


		if ($iCustomFieldDelete = $this->request()->getInt('delete'))
		{

			if (Phpfox::getService('directory.custom.process')->delete($iCustomFieldDelete))
			{
				if($bIsEditGroup){
					$this->url()->send('admincp.directory.customfield.add', array('id' => $iEditGroupId), _p('directory.custom_field_successfully_deleted'));
				}
			}
		}

        $aLanguages = Phpfox::getService('language')->get();
        if ($bAddInput){
			$aKeys = array_keys($aGroup['group_name']);
			$aVals2 = array_values($aGroup['group_name']);        
        }
        foreach ($aLanguages as $iKey => $aLanguage)
        {
			if ($bAddInput && isset($aKeys[0]))
			{
				$aLanguages[$iKey]['phrase_var_name'] = $aKeys[0];
			}
	        $mPost = '';
	        if($bAddInput && isset($aVals2[0])){
		        foreach ($aVals2[0] as $keyaVals2 => $valueaVals2) {
		        	if($aLanguages[$iKey]['language_id'] == $keyaVals2){
		        		$mPost = $valueaVals2;
		        	}
		        }
	        }
	        $aLanguages[$iKey]['post_value'] = $mPost;        	
        }

		$this->template()->setTitle($bIsEditGroup?_p('directory.edit_custom_field_groups'):_p('directory.add_custom_field_groups'))
            ->setBreadCrumb(_p("Apps"), $this->url()->makeUrl('admincp.apps'))
            ->setBreadCrumb(_p('controller_directory'), $this->url()->makeUrl('admincp.app').'?id=__module_directory')
			->setBreadcrumb($bIsEditGroup?_p('directory.edit_custom_field_groups'):_p('directory.add_custom_field_groups'))
			->assign(array(
					'sCorePath' => Phpfox::getParam('core.path'),
					'aLanguages' => $aLanguages,
					'sUrlAdd' => $this->url()->makeUrl('admincp.directory.customfield.add'),
						)
		)->setHeader(array(
			'jquery/ui.js' => 'static_script',
			'yndirectory_custom_field_admin.js' => 'module_directory',
			'<script type="text/javascript">$Behavior.setURLDirectory = function() { $Core.directory.url(\'' . $this->url()->makeUrl('admincp.directory.customfield.add') . '\'); } </script>'
		));
	 
	}

	private function __isValid($aVals){
		$emptyGroupName = false;
		$group_name = $aVals['group_name'];
		foreach ($group_name as $keygroup_name => $valuegroup_name) {
			if(is_array($valuegroup_name)){
				foreach ($valuegroup_name as $keyvaluegroup_name => $valuevaluegroup_name) {
					if(strlen(trim($valuevaluegroup_name)) == 0){
						$emptyGroupName = true;
						break;
					}
				}
			} else {
				if(strlen(trim($valuegroup_name)) == 0){
					$emptyGroupName = true;
					break;
				}
			}
		}
		if($emptyGroupName){
			Phpfox_Error::set(_p('directory.group_name_cannot_be_empty'));
			return false;
		}

		return true;
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