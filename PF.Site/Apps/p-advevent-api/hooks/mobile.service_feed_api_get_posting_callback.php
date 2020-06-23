<?php

if (Phpfox::isModule('fevent')) {
    if (!empty($type) && $type == 'fevent') {
        $aEvent = Phpfox::getService('fevent')->getForEdit($item);
        if (!empty($aEvent['event_id'])) {
            $sLink = Phpfox::permalink('fevent', $aEvent['event_id'], $aEvent['title']);
            $aCallback = array(
                'module' => 'fevent',
                'table_prefix' => 'fevent_',
                'link' => $sLink,
                'email_user_id' => $aEvent['user_id'],
                'subject' => _p('full_name_wrote_a_comment_on_your_event_title', array('full_name' => Phpfox::getUserBy('full_name'), 'title' => $aEvent['title'])),
                'message' => _p('full_name_wrote_a_comment_on_your_event_message', array('full_name' => Phpfox::getUserBy('full_name'), 'link' => $sLink, 'title' => $aEvent['title'])),
                'notification' => 'fevent_comment',
                'feed_id' => 'fevent_comment',
                'item_id' => $aEvent['event_id']
            );
        }
    }
}
