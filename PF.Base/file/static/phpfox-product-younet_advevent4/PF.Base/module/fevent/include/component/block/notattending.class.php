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
 
class Fevent_Component_Block_Notattending extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		$iPageSize = 8;
		
		$aEvent = $this->getParam('aEvent');

        list($iNotAttendingCnt, $aNotAttendingInvites) = Phpfox::getService('fevent')->getInvites($aEvent['event_id'], 3, 1, $iPageSize);

        if ($iNotAttendingCnt == 0) {
            return false;
        }

        $this->template()->assign(array(
				'sHeader' => (_p('not_attending')),
                'iNotAttendingCnt' => $iNotAttendingCnt,
                'aNotAttendingInvites' => $aNotAttendingInvites,
			)
		);

        if ($iNotAttendingCnt > 8) {
            $this->template()->assign(array(
                'aFooter' => array(
                    _p('view_all') . ' (' .$iNotAttendingCnt.')' => '#'
                ),
                'sCustomClassName' => 'ync-block'
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

	}
}

?>