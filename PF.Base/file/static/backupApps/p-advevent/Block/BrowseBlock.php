<?php
namespace Apps\P_AdvEvent\Block;

use Phpfox;
use Phpfox_Component;

class BrowseBlock extends Phpfox_Component
{
    public function process()
    {
        $iRsvp = $this->request()->get('rsvp');
        $iPage = $this->request()->getInt('page');

        $iPageSize = 20;

        $aEvent = Phpfox::getService('fevent')->getEvent($this->request()->get('id'), true);

        list($iCnt, $aInvites) = Phpfox::getService('fevent')->getInvites($aEvent['event_id'], $iRsvp, $iPage, $iPageSize);

        Phpfox::getLib('pager')->set(array('ajax' => 'fevent.browseList', 'page' => $iPage, 'size' => $iPageSize, 'count' => $iCnt, 'aParams' =>
                array(
                    'id' => $aEvent['event_id'],
                    'rsvp' => $iRsvp
                )
            )
        );

        $aLists = array(
            _p('attending') => '1',
            _p('maybe_attending') => '2',
            _p('awaiting_reply') => '0',
            _p('not_attending') => '3'
        );

        $this->template()->assign(array(
                'aEvent' => $aEvent,
                'aInvites' => $aInvites,
                'bIsInBrowse' => ($iPage > 0 ? true : false),
                'aLists' => $aLists
            )
        );
    }
}