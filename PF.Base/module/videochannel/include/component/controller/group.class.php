<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Videochannel_Component_Controller_Group extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		$aGroup = $this->getParam('aGroup');
		
		if (!Phpfox::getService('group')->hasAccess($aGroup['group_id'], 'can_use_video'))
		{
			return Phpfox_Error::display(_p('videochannel.video_section_is_closed'));
		}		
		
		if ($this->request()->get('req4') == 'upload')
		{			
			$this->url()->send('videochannel.upload', array('module' => 'group', 'item' => $aGroup['group_id']));
		}	
		elseif ($this->request()->get('req4') == 'share')
		{			
			$this->url()->send('videochannel.share', array('module' => 'group', 'item' => $aGroup['group_id']));
		}
		elseif ($this->request()->get('req4') == 'tag')
		{
			// without this empty if it does not show the search by tag controller (index.php?do=/group/group-1/video/tag/tag1/)
		}
		elseif ($this->request()->get('req4') && $this->request()->get('req4') != 'category')
		{
			$this->setParam('aCallback', array(
					'request' => 'req4',
					'item' => $aGroup['group_id'],
					'module' => 'group',
					'url' => array(
						'group',
						array(
							$aGroup['title_url']				
						)
					),
					'url_home' => array(
						'group',
						array(
							$aGroup['title_url']							
						)
					)
				)	
			);
			
			return Phpfox::getLib('module')->setController('videochannel.view');			
		}
		
		$this->setParam('aCallback', array(
				'category_request' => 4,
				'item' => $aGroup['group_id'],
				'module' => 'group',
				'url' => array(
					'group',
					array(
						$aGroup['title_url'],
						'videochannel'
					)
				),
				'url_home' => array(
					'group',
					array(
						$aGroup['title_url']							
					)
				)				
			)
		);
		
		return Phpfox::getLib('module')->setController('videochannel.index');
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('videochannel.component_controller_group_clean')) ? eval($sPlugin) : false);
	}
}

?>