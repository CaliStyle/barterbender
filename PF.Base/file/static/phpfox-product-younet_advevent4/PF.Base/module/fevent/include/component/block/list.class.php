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
class Fevent_Component_Block_List extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{		
		$iRsvp = $this->request()->get('rsvp', 1);
		$iPage = $this->request()->getInt('page');		
		$sModule = $this->request()->get('module', false);
		$iItem =  $this->request()->getInt('item', false);
		$aCallback = $this->getParam('aCallback', false);		
		$iPageSize = 1000;
		
		if (PHPFOX_IS_AJAX)
		{
			$aCallback = false;
			if ($sModule && $iItem && Phpfox::hasCallback($sModule, 'getEventInvites'))
			{
				$aCallback = Phpfox::callback($sModule . '.getEventInvites', $iItem);				
			}			
			
			$aEvent = Phpfox::getService('fevent')->callback($aCallback)->getEvent($this->request()->get('id'), true);
			$aEvent = Phpfox::getService('fevent.helper')->retrieveEventPermissions($aEvent);
			$this->template()->assign('aEvent', $aEvent);
		}
		else 
		{
			$aEvent = $this->getParam('aEvent');			
			$aEvent = Phpfox::getService('fevent.helper')->retrieveEventPermissions($aEvent);
			$this->template()->assign('aEvent', $aEvent);
		}

		if ($aCallback !== false)
		{
			$sModule = $aCallback['module'];
			$iItem = $aCallback['item'];
		}		
		
		list($iCnt, $aInvites) = Phpfox::getService('fevent')->getInvites($aEvent['event_id'], $iRsvp, $iPage, $iPageSize);		
				
		Phpfox::getLib('pager')->set(array('ajax' => 'fevent.listGuests', 'page' => $iPage, 'size' => $iPageSize, 'count' => $iCnt, 'aParams' => 
			array(
					'id' => $aEvent['event_id'],
					'module' => $sModule,
					'item' => $iItem,
					'rsvp' => $iRsvp
				)
			)
		);
		
		$this->template()->assign(array(
				'aInvites' => $aInvites,
				'iRsvp' => $iRsvp,
                'sCustomClassName' => 'ync-block'
			)
		);		
		
		if (!PHPFOX_IS_AJAX)
		{			
			$sExtra = '';
			if ($aCallback !== false)
			{
				$sExtra .= '&amp;module=' . $aCallback['module'] . '&amp;item=' . $aCallback['item'];	
			}			
			
			$this->template()->assign(array(
					'aMenu' => array(
						_p('attending') => '#fevent.listGuests?rsvp=1&amp;id=' . $aEvent['event_id'] . $sExtra,
						_p('maybe') => '#fevent.listGuests?rsvp=2&amp;id=' . $aEvent['event_id'] . $sExtra,
						_p('can_t_make_it') => '#fevent.listGuests?rsvp=3&amp;id=' . $aEvent['event_id'] . $sExtra,
						_p('not_responded') => '#fevent.listGuests?rsvp=0&amp;id=' . $aEvent['event_id'] . $sExtra
					),
					'sBoxJsId' => 'event_guests'
				)
			);
			
			return 'block';
		}		
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('fevent.component_block_list_clean')) ? eval($sPlugin) : false);
	}
}

?>