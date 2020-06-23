<?php
namespace Apps\P_AdvEvent\Block;

use Phpfox;
use Phpfox_Component;

class FeedEventBlock extends Phpfox_Component
{
    public function process()
    {
        if ($iFeedId = $this->getParam('this_feed_id')) {
            $event = $this->getParam('custom_param_advanced_event_' . $iFeedId);
            if(empty($event)) {
                return false;
            }

            $iSponsorFeedId = $this->getParam('sponsor_feed_id');
            $eventLink = Phpfox::permalink('fevent', $event['event_id'], $event['title']);
            if(Phpfox::isAppActive('Core_BetterAds') && (int)$iFeedId === (int)$iSponsorFeedId)
            {
                $iSponsorId = Phpfox::getService('ad.get')->getFeedSponsors($iFeedId);
                $eventLink = $iSponsorId ? Phpfox::getLib('url')->makeUrl('ad.sponsor', ['view' => $iSponsorId]) : $eventLink;
            }

            $helperObject = Phpfox::getService('fevent.helper');

            $event['d_type'] = $helperObject->getTimeLineStatus($event['start_time'],$event['end_time']);

            $timeNeedToFormatted = in_array($event['d_type'], ['ongoing', 'past']) ? $event['end_time'] : $event['start_time'];
            $event['date_formatted'] = $helperObject->formatTimeToDate($event['d_type'], $timeNeedToFormatted);
            $event['d_day'] = Phpfox::getTime('d', $timeNeedToFormatted);
            $event['d_month'] = Phpfox::getTime('F', $timeNeedToFormatted);
            $event['d_time'] = date('g:i a, d F', $timeNeedToFormatted);
            $event['is_invited'] = !empty($event['invitee_id']) && !empty($event['inviter_id']) ? ($event['inviter_id'] != $event['invitee_id'] ? true : ($event['user_id'] == $event['invitee_id'] ? true : false)) : false;

            $locationText = $event['location'];
            if (!empty($event['address'])) {
                $locationText .= ', ' . $event['address'];
            }
            if (!empty($event['city'])) {
                $locationText .= ', ' . $event['city'];
            }
            if (!empty($event['country_iso'])) {
                $locationText .= ', ' . Phpfox::getPhraseT(Phpfox::getService('core.country')->getCountry($event['event_country_iso']), 'country');
            }

            Phpfox::getService('fevent')->getMoreInfoForEventItem($event, true);

            $this->template()->assign([
                'link' => $eventLink,
                'aItem' => $event,
                'defaultImage' => Phpfox::getService('fevent')->getDefaultPhoto(),
                'location' => $locationText,
                'rsvpActionType' => 'list',
            ]);

            if(!$event['is_child_item']) {
                $this->clearParam('custom_param_advanced_event_' . $iFeedId);
            }
        }
    }
}