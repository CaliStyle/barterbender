<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');
/**
 * 
 * 
 * @copyright       [YOUNET_COPYRIGHT]
 * @author          YouNet Company
 * @package         YouNet_Event
 */
class Fevent_Component_Controller_Group extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		$aGroup = $this->getParam('aGroup');
		
		if (!Phpfox::getService('group')->hasAccess($aGroup['group_id'], 'can_use_event'))
		{
			return Phpfox_Error::display(_p('the_events_section_is_closed'));
		}		
		
		if ($this->request()->get('req4') == 'view')
		{
			$this->setParam('aCallback', array(
					'request' => 'req5',
					'item' => $aGroup['group_id'],
					'module' => 'group',
					'host' => $aGroup['title'],
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
			
			return Phpfox::getLib('module')->setController('fevent.view');
		}		
		elseif ($this->request()->get('req4') == 'add')
		{			
			$this->url()->send('fevent.add', array('module' => 'group', 'item' => $aGroup['group_id']));
		}
		
		$this->setParam('aCallback', array(
				'category_request' => 3,
				'item' => $aGroup['group_id'],
				'module' => 'group',
				'url' => array(
					'group',
					array(
						$aGroup['title_url'],
						'fevent',
						'view'
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
		
		return Phpfox::getLib('module')->setController('fevent.index');
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('fevent.component_controller_group_clean')) ? eval($sPlugin) : false);
	}
}

?>