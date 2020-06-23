<?php
namespace Apps\P_AdvEvent\Block;

use Phpfox;
use Phpfox_Component;
use Phpfox_Plugin;

class RsvpBlock extends Phpfox_Component
{
    public function process()
    {
        if (!Phpfox::isUser())
        {
            return false;
        }
        #Google calendar API
        $bIsGapi = false;
        if($aGapi = Phpfox::getService('fevent.gapi')->getForManage()) {
            $bIsGapi = true;
        }
        $this->template()->assign([
            'bIsGapi' => $bIsGapi,
            'rsvpActionType' => ''
        ]);

        if (PHPFOX_IS_AJAX)
        {
            $sModule = $this->request()->get('module', false);
            $iItem =  $this->request()->getInt('item', false);
            $aCallback = false;
            if ($sModule && $iItem && Phpfox::hasCallback($sModule, 'getEventInvites'))
            {
                $aCallback = Phpfox::callback($sModule . '.getEventInvites', $iItem);
            }
        }

        $aEvent = (PHPFOX_IS_AJAX ? Phpfox::getService('fevent')->callback($aCallback)->getEvent($this->request()->get('id'), true) : $this->getParam('aEvent'));

        if (PHPFOX_IS_AJAX)
        {
            $this->template()->assign(array(
                    'aEvent' => $aEvent,
                    'aCallback' => $aCallback
                )
            );
        }
        else
        {
            $aCallback = $this->getParam('aCallback', false);
            $this->template()->assign(array(
                    'aCallback' => $aCallback,
                    'aEvent'	=> $aEvent,
                    'sCustomClassName' => 'p-block'
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
        (($sPlugin = Phpfox_Plugin::get('fevent.component_block_rsvp_clean')) ? eval($sPlugin) : false);
    }
}