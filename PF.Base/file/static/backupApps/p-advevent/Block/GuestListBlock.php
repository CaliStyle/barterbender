<?php
/**
 * [PHPFOX_HEADER]
 */

namespace Apps\P_AdvEvent\Block;

use Phpfox;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');


class GuestListBlock extends \Phpfox_Component
{
    private $_aRsvp = [
        'attending' => 1,
        'maybe' => 2,
        'awaiting' => 3,
    ];

    /**
     * Controller
     */
    public function process()
    {
        $iPageSize = $this->getParam('limit', 10);
        $iPage = $this->getParam('page', 1);
        $sTab = $this->getParam('tab', 'attending');
        $isStatistic = $this->getParam('statistic');
        $sContainer = $this->getParam('container', '.item-event-member-block');
        if (!(int)$iPageSize) {
            return false;
        }
        $eventId = $this->getParam('iEventId');
        list($iCnt, $aInvites) = Phpfox::getService('fevent')->getInvites($eventId, $isStatistic ? 4 : $this->_aRsvp[$sTab],
            $iPage, $iPageSize);

        $aParamsPager = array(
            'page' => $iPage,
            'size' => $iPageSize,
            'count' => $iCnt,
            'paging_mode' => 'pagination',
            'ajax_paging' => [
                'block' => 'fevent.guest-list',
                'params' => [
                    'tab' => $sTab,
                    'iEventId' => $eventId,
                    'statistic' => $isStatistic
                ],
                'container' => $sContainer
            ]
        );
        $this->template()->assign(array(
                'iCnt' => $iCnt,
                'aInvites' => $aInvites,
                'bIsPaging' => $this->getParam('ajax_paging', 0)
            )
        );
        Phpfox::getLib('pager')->set($aParamsPager);
        return 'block';
    }

    /**
     * @return array
     */
    public function getSettings()
    {
        return [
            [
                'info' => _p('Attending Limit'),
                'description' => _p('Define the limit of how many attending users can be displayed when viewing the event detail. Set 0 will hide this block.'),
                'value' => 10,
                'type' => 'integer',
                'var_name' => 'limit',
            ]
        ];
    }
    /**
     * @return array
     */
    public function getValidation()
    {
        return [
            'limit' => [
                'def' => 'int',
                'min' => 0,
                'title' => _p('"Attending Limit" must be greater than or equal to 0')
            ],
        ];
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