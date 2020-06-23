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
 
class Fevent_Component_Block_Maybeattending extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		$iPageSize = 8;
		$aEvent = $this->getParam('aEvent');
		list($iMaybeCnt, $aMaybeInvites) = Phpfox::getService('fevent')->getInvites($aEvent['event_id'], 2, 1, $iPageSize);
        if ($iMaybeCnt == 0) {
            return false;
        }

		$this->template()->assign(array(
				'sHeader' => (_p('maybe_attending')),
				'aMaybeInvites' => $aMaybeInvites,
				'iMaybeCnt' => $iMaybeCnt,
                'sCustomClassName' => 'ync-block'
			)
		);

        if ($iMaybeCnt > 8) {
            $this->template()->assign(array(
                'aFooter' => array(
                    _p('view_all') . ' (' .$iMaybeCnt.')' => '#'
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

	}
}

?>