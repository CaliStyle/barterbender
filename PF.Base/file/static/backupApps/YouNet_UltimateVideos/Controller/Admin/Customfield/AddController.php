<?php
/**
 * [PHPFOX_HEADER]
 */
namespace Apps\YouNet_UltimateVideos\Controller\Admin\Customfield;

use Phpfox;
use Phpfox_Component;
use Phpfox_Plugin;
defined('PHPFOX') or exit('NO DICE!');

class AddController extends \Phpfox_Component
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
				'title'=> _p("Group Name cannot be empty")
			),
		);
		
		if ($aVals = $this->request()->getArray('val'))
        {
        		if ($this->__isValid($aVals)){
	               
	                if ($bIsEditGroup || isset($aVals['group_id']))
	                {
	                        if (Phpfox::getService('ultimatevideo.custom.group')->updateGroup($aVals['group_id'], $aVals))
	                        {
	                        		$this->url()->send('admincp.app', [
	                        		'id' => 'YouNet_UltimateVideos'
	                    			],_p('custom_field_groups_successfully_updated'));
	                        }
	                } else
	                {
	                        if ($iGroupId = Phpfox::getService('ultimatevideo.custom.group')->addGroup($aVals))
	                        {
	                        	$this->url()->send('admincp.app', [
	                        		'id' => 'YouNet_UltimateVideos'
	                    			],_p('custom_field_groups_successfully_added'));
	                        }
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

		$this->template()->setTitle($bIsEditGroup?_p('edit_custom_field_groups'):_p('add_custom_field_groups'))
			->setBreadcrumb($bIsEditGroup?_p('edit_custom_field_groups'):_p('add_custom_field_groups'))
			->assign(array(
					'sCorePath' => Phpfox::getParam('core.path_actual'),
					'aLanguages' => $aLanguages,
					'sUrl' => $this->url()->makeUrl('ultimatevideo.admincp.customfield.add'),
						)
		)->setHeader(array(
			'jquery/ui.js' => 'static_script',
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
			Phpfox_Error::set(_p('group_name_cannot_be_empty'));
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