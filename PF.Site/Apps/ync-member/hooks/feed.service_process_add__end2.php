<?php

/*
 * This is how notification for follow work:
 *
 * We will hook the feed add event and check if the poster have any followers.
 * If yes we will create a notification for those followers with type is ynmember_follownotification.
 * We also insert a row in ynmember_follow_notification, storing the type and id of the item.
 * We the notification is called back, we wil access the ynmember_follow_notification.
 * There we will get the type and id of the item being shared.
 * Then we get the corresponding feed call back (getActivityFeed....())
 * From this we get the content and bookmark_url of the notification
 */

//$aInsert : the record that inserted into feed table

// get follower
$aFollowers = Phpfox::getService('ynmember.member')->getFollowingMembers($aInsert['user_id']);

foreach ($aFollowers as $aFollower) {

    if (!Phpfox::getService('ynmember.member')->canGetNotification($aInsert['user_id'], $aFollower['user_id'])) {
        continue;
    }

    $feed_params = json_encode($aInsert);

    $aYnmemberFollowNotificationInsert = [
        'feed_params' => $feed_params,
        'time_stamp' => PHPFOX_TIME
    ];
    $iFollowNotificationId = $this->database()->insert(Phpfox::getT('ynmember_follow_notification'), $aYnmemberFollowNotificationInsert);

    $aYnmemberNotificationInsert = [
        'type_id' => 'ynmember_follow_action',
        'item_id' => $iFollowNotificationId,
        'user_id' => $aFollower['user_id'],
        'owner_user_id' => $aInsert['user_id'],
        'time_stamp' => PHPFOX_TIME
    ];

    $iId = $this->database()->insert(Phpfox::getT('notification'), $aYnmemberNotificationInsert);
}
