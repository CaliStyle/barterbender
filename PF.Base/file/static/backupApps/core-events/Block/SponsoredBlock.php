<?php
/**
 * [PHPFOX_HEADER]
 */

namespace Apps\Core_Events\Block;

use Phpfox;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');

class SponsoredBlock extends \Phpfox_Component
{
    /**
     * Class process method which is used to execute this component.
     */
    public function process()
    {
        if (!Phpfox::isModule('ad')) {
            return false;
        }

        if (defined('PHPFOX_IS_GROUP_VIEW') || defined('PHPFOX_IS_PAGES_VIEW') || defined('PHPFOX_IS_USER_PROFILE')) {
            return false;
        }

        $iLimit = $this->getParam('limit', 4);
        if (!(int)$iLimit) {
            return false;
        }
        $iCacheTime = $this->getParam('cache_time', 5);
        $aSponsorEvents = Phpfox::getService('event')->getRandomSponsored($iLimit, $iCacheTime);
        if (empty($aSponsorEvents)) {
            return false;
        }

        foreach ($aSponsorEvents as $aSponsorEvent) {
            Phpfox::getService('ad.process')->addSponsorViewsCount($aSponsorEvent['sponsor_id'], 'event');
        }
        $this->template()->assign(array(
                'sHeader' => _p('sponsored_event'),
                'aSponsorEvents' => $aSponsorEvents,

            )
        );
        if (Phpfox::getUserParam('event.can_sponsor_event') || Phpfox::getUserParam('event.can_purchase_sponsor')) {
            $this->template()->assign([
                'aFooter' => array(
                    _p('encourage_sponsor_events') => $this->url()->makeUrl('event', array('view' => 'my'))
                )
            ]);
        }
        return 'block';
    }

    /**
     * @return array
     */
    public function getSettings()
    {
        return [
            [
                'info' => _p('Sponsored Events Limit'),
                'description' => _p('Define the limit of how many sponsored events can be displayed when viewing the events section. Set 0 will hide this block.'),
                'value' => 4,
                'type' => 'integer',
                'var_name' => 'limit',
            ],
            [
                'info' => _p('Sponsored Events Cache Time'),
                'description' => _p('Define how long we should keep the cache for the <b>Sponsored Events</b> by minutes. 0 means we do not cache data for this block.'),
                'value' => Phpfox::getParam('core.cache_time_default'),
                'options' => Phpfox::getParam('core.cache_time'),
                'type' => 'select',
                'var_name' => 'cache_time',
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
                'title' => _p('"Sponsored Events Limit" must be greater than or equal to 0')
            ],
        ];
    }
    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('event.component_block_sponsored_clean')) ? eval($sPlugin) : false);
    }
}