<?php
namespace Apps\P_AdvEvent\Block;

use Phpfox;
use Phpfox_Component;
use Phpfox_Plugin;

class ListBlock extends Phpfox_Component
{
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

        if(empty($iPage)) {
            $iPage = 1;
        }

        list($iCnt, $aInvites) = Phpfox::getService('fevent')->getInvites($aEvent['event_id'], $iRsvp, $iPage, $iPageSize);

        $canPaging = (count($aInvites) == $iPageSize) && ((int)$iCnt > (count($aInvites) * $iPage));

        if($canPaging) {
            Phpfox::getLib('pager')->set([
                    'page' => $iPage,
                    'size' => $iPageSize,
                    'count' => $iCnt,
                    'ajax_paging' => [
                        'block' => 'fevent.list',
                        'params' => [
                            'id' => $aEvent['event_id'],
                            'module' => $sModule,
                            'item' => $iItem,
                            'rsvp' => $iRsvp
                        ],
                        'container' => '.js_fevent_member_list'
                    ]
                ]
            );
        }

        $this->template()->assign(array(
                'aInvites' => $aInvites,
                'iRsvp' => $iRsvp,
                'sCustomClassName' => 'p-block',
                'canPaging' => $canPaging
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