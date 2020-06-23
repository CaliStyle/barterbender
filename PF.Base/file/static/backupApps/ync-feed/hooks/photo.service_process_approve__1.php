<?php
$storagedFeedId = storage()->get('ynfeed_pending_photo_' . $aPhoto['photo_id']);
if(!empty($storagedFeedId->value)) {
    $tempFeed = Phpfox::getService('feed')->callback($aCallback)->getFeed($iFeedId);
    if($tempFeed['item_id'] == $aPhoto['photo_id']) {
        //Delete new feed that photo is inserted above
        if(Phpfox::getService('feed.process')->callback($aCallback)->deleteFeed($iFeedId, $aPhoto['module_id'], $aPhoto['group_id'])) {
            if(!empty($aCallback)) {
                $checkInFeed = db()->select('feed_id')
                    ->from(Phpfox::getT('feed'))
                    ->where('type_id = "photo" AND item_id = ' . (int)$aPhoto['photo_id'] . ' AND user_id = ' . (int)$aPhoto['user_id'])
                    ->execute('getSlaveField');
                if(!empty($checkInFeed)) {
                    Phpfox::getService('feed.process')->callback(null)->deleteFeed($checkInFeed);
                }
            }
        }
        if(!empty($_SESSION['approve_photo_feed_' . $aPhoto['user_id'] . '_' . $aPhoto['album_id'] . '_' . $iTimeStamp])) {
            unset($_SESSION['approve_photo_feed_' . $aPhoto['user_id'] . '_' . $aPhoto['album_id'] . '_' . $iTimeStamp]);
        }
    }
    else {
        db()->delete(Phpfox::getT('photo_feed'), ['feed_id' => $iFeedId, 'photo_id' => $aPhoto['photo_id']]);
    }

    //Process photo to right feed id
    $feed = Phpfox::getService('feed')->callback($aCallback)->getFeed($storagedFeedId->value);
    if(!empty($feed) && $feed['type_id'] == 'photo') {
        $keepLastPhoto = storage()->get('ynfeed_updated_photo_feed_keep_last_photo_' . $storagedFeedId->value);
        $itemPhoto = db()->select('p.user_id, p.type_id, p.album_id, p.group_id, pi.description, p.photo_id')
            ->from(Phpfox::getT('photo'), 'p')
            ->join(Phpfox::getT('photo_info'), 'pi', 'pi.photo_id = p.photo_id')
            ->where('p.photo_id = ' . ($feed['item_id'] ? (int)$feed['item_id'] : $keepLastPhoto->value))
            ->execute('getSlaveRow');

        if(!empty($itemPhoto)) {
            db()->update(Phpfox::getT('photo'), ['album_id' => (int)$itemPhoto['album_id'], 'type_id' => $itemPhoto['type_id'], 'user_id' => $itemPhoto['user_id']], 'photo_id = ' . (int)$aPhoto['photo_id'] . '');
            if(db()->update(Phpfox::getT((isset($aCallback['table_prefix']) ? $aCallback['table_prefix'] : '') . 'feed'), ['item_id' => (int)$aPhoto['photo_id']], 'feed_id = '. (int)$storagedFeedId->value)) {
                if(!empty($aCallback['module'])) {
                    $check = db()->select('feed_id')
                        ->from(Phpfox::getT('feed'))
                        ->where('type_id = "photo" AND item_id = ' . (int)$feed['item_id'] . ' AND user_id = ' . (int)$feed['user_id'])
                        ->execute('getSlaveField');
                    if($check && (db()->update(Phpfox::getT('feed'), ['item_id' => (int)$aPhoto['photo_id']], 'feed_id = ' . (int)$check))) {
                        \Phpfox_Cache::instance()->remove('ynfeed_extra_' . $check . '_' . Phpfox::getUserId());
                        \Phpfox_Cache::instance()->remove('ynfeed_extra_' . $storagedFeedId->value . '_' . $itemPhoto['user_id']);
                    }
                }
            }

            db()->update(Phpfox::getT('photo'), ['time_stamp' => $feed['time_stamp']], 'photo_id = '. (int)$aPhoto['photo_id']);

            if($keepLastPhoto) {
                Phpfox::getService('photo.process')->delete($itemPhoto['photo_id']);
                storage()->del('ynfeed_updated_photo_feed_keep_last_photo_' . $storagedFeedId->value);
            }
            else {
                db()->update(Phpfox::getT('photo'), array('is_cover' => 0), 'album_id=' . (int)$itemPhoto['album_id']);
                db()->insert(Phpfox::getT('photo_feed'), [
                    'feed_id' => $storagedFeedId->value,
                    'photo_id' => $itemPhoto['photo_id'],
                    'feed_table' => (!empty($aCallback['table_prefix']) ? $aCallback['table_prefix'] : '') . 'feed'
                ]);
            }

            db()->update(Phpfox::getT('photo'), ['is_cover' => 1], 'photo_id = '. (int)$aPhoto['photo_id']);
            db()->update(Phpfox::getT('photo_info'), ['description' => $itemPhoto['description']],
                'photo_id = ' . (int)$aPhoto['photo_id']);
            db()->updateCount('photo', 'album_id = '. (int)$itemPhoto['album_id'], 'total_photo', 'photo_album', 'album_id = '. (int)$itemPhoto['album_id']);
            \Phpfox_Cache::instance()->remove('ynfeed_extra_' . $storagedFeedId->value . '_' . Phpfox::getUserId());
            if($feed['user_id'] != Phpfox::getUserId()) {
                \Phpfox_Cache::instance()->remove('ynfeed_extra_' . $storagedFeedId->value . '_' . $feed['user_id']);
            }
            if($keepLastPhoto) {
                storage()->del('ynfeed_updated_photo_feed_keep_last_photo_' . $storagedFeedId->value);
            }
        }
    }
    storage()->del('ynfeed_pending_photo_' . $aPhoto['photo_id']);
}