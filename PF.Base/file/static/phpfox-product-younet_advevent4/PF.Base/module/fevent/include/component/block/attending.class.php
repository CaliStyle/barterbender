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
 
class Fevent_Component_Block_Attending extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		$iPageSize = 8;
		
		$aEvent = $this->getParam('aEvent');
		
		list($iCnt, $aInvites) = Phpfox::getService('fevent')->getInvites($aEvent['event_id'], 1, 1, $iPageSize);
		list($iAwaitingCnt, $aAwaitingInvites) = Phpfox::getService('fevent')->getInvites($aEvent['event_id'], 0, 1, $iPageSize);
		list($iMaybeCnt, $aMaybeInvites) = Phpfox::getService('fevent')->getInvites($aEvent['event_id'], 2, 1, $iPageSize);
		list($iNotAttendingCnt, $aNotAttendingInvites) = Phpfox::getService('fevent')->getInvites($aEvent['event_id'], 3, 1, $iPageSize);

        if ($iCnt == 0) {
            return false;
        }

		$this->template()->assign(array(
				'sHeader' => (_p('attending')),
				'iCnt' => $iCnt,
				'aInvites' => $aInvites,
				'aAwaitingInvites' => $aAwaitingInvites,
				'iAwaitingCnt' => $iAwaitingCnt,
				'aMaybeInvites' => $aMaybeInvites,
				'iMaybeCnt' => $iMaybeCnt,
				'iNotAttendingCnt' => $iNotAttendingCnt,
				'aNotAttendingInvites' => $aNotAttendingInvites,
                'sCustomClassName' => 'ync-block'
			)
		);

        if ($iCnt > 8) {
            $this->template()->assign(array(
                'aFooter' => array(
                    _p('view_all') . ' (' .$iCnt.')' => '#'
                )
            ));
        }
		
		return 'block';
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('fevent.component_block_attending_clean')) ? eval($sPlugin) : false);
	}
}

?>