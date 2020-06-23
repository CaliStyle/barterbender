<?php
namespace Apps\YNC_Feed\Service\Photo;

use Phpfox;
use Phpfox_Plugin;
use Phpfox_Request;
use Core;
use Phpfox_Ajax;
use Phpfox_Url;
use Phpfox_Template;
use Apps\Core_Photos\Service\Process as Photo_Process;
use Phpfox_Cache;

defined('PHPFOX') or exit('NO DICE!');

class Process extends Photo_Process {
    /**
     * Adding a new photo.
     *
     * @param int $iUserId User ID of the user that the photo belongs to.
     * @param array $aVals Array of the post data being passed to insert.
     * @param boolean $bIsUpdate True if we plan to update the entry or false to insert a new entry in the database.
     * @param boolean $bAllowTitleUrl Set to true to allow the editing of the SEO url.
     *
     * @return int ID of the newly added photo or the ID of the current photo we are editing.
     */
    public function add($iUserId, $aVals, $bIsUpdate = false, $bAllowTitleUrl = false)
    {
        $iId = parent::add($iUserId, $aVals, $bIsUpdate, $bAllowTitleUrl);

        return $iId;
    }

    public function processPhotoForFeed($feedId, $editedPhotos = [], $deletedPhotos = [], $uploadedPhotos = [], $callback = null) {
        $feed = Phpfox::getService('feed')->callback($callback)->getFeed($feedId);
        if(!empty($feed)) {
            $deletePhotoFeed = $deletedPhotos;
            if(Phpfox::getUserParam('photo.photo_must_be_approved')) {
                $mainTempEditedPhotos = $editedPhotos;
                if(empty($mainTempEditedPhotos)) {
                    $lastId = 0;
                }
                else {
                    $lastId = array_pop($mainTempEditedPhotos);
                }

                $canUpdateFeed = true;
                if($lastId) {
                    $isPendingPhoto = db()->select('photo_id')
                                        ->from(Phpfox::getT('photo'))
                                        ->where('photo_id = ' . (int)$lastId . ' AND view_id = 1')
                                        ->execute('getSlaveField');
                    $canUpdateFeed = !$isPendingPhoto;
                    if(!$canUpdateFeed) {
                        if(empty($mainTempEditedPhotos)/* || ($lastId != $feed['item_id'] && !in_array($feed['item_id'], $editedPhotos))*/) {
                            $canUpdateFeed = true;
                            $lastId = 0;
                        }
                        else {
                            $currentFeedPhotos = db()->select('*')
                                ->from(Phpfox::getT('photo_feed'))
                                ->where("feed_id = " . $feedId . " AND feed_table = '" . (isset($callback['table_prefix']) ? $callback['table_prefix'] : '') . "feed'")
                                ->execute('getSlaveRows');
                            $currentFeedPhotos = array_merge(array_column($currentFeedPhotos,'photo_id'), [$feed['item_id']]);
                            $tempEditedPhotos = $mainTempEditedPhotos;
                            $tempLastId = 0;
                            while (count($tempEditedPhotos) > 0) {
                                $tempId = array_pop($tempEditedPhotos);
                                if(in_array($tempId, $currentFeedPhotos)) {
                                    $tempLastId = $tempId;
                                    break;
                                }
                            }
                            $lastId = $tempLastId;
                            if($lastId) {
                                $canUpdateFeed = true;
                            }
                        }
                    }
                }

                if($canUpdateFeed && $lastId != $feed['item_id']) {
                    db()->update(Phpfox::getT((isset($callback['table_prefix']) ? $callback['table_prefix'] : '') . 'feed'), ['item_id' => $lastId], 'feed_id = '. (int)$feedId);
                    if(!empty($callback['module'])) {
                        $check = db()->select('feed_id')
                            ->from(Phpfox::getT('feed'))
                            ->where('type_id = "photo" AND item_id = ' . (int)$feed['item_id'] . ' AND user_id = ' . (int)$feed['user_id'])
                            ->execute('getSlaveField');
                        if($check) {
                            db()->update(Phpfox::getT('feed'), ['item_id' => $lastId], 'feed_id = ' . (int)$check);
                        }
                    }
                    if($lastId) {
                        $deletePhotoFeed[] = $lastId;
                    }
                }

                if($lastId != $feed['item_id']) {
                    if($lastId && $lastId != $feed['item_id'] && !empty($editedPhotos) && $canUpdateFeed) {
                        $deletedPhotos[] = $feed['item_id'];
                    }
                    else if(!$lastId) {
                        storage()->set('ynfeed_updated_photo_feed_keep_last_photo_' . $feedId, (int)$feed['item_id']);
                    }
                }
            }
            else {
                if(!empty($uploadedPhotos)) {
                    $editedPhotos = !empty($editedPhotos) ? array_merge($editedPhotos, $uploadedPhotos) : $uploadedPhotos;
                }

                $lastId = array_pop($editedPhotos);

                $itemPhoto = $itemPhoto = db()->select('p.user_id, p.type_id, p.album_id, p.group_id, pi.description')
                    ->from(Phpfox::getT('photo'), 'p')
                    ->join(Phpfox::getT('photo_info'), 'pi', 'pi.photo_id = p.photo_id')
                    ->where('p.photo_id = ' . (int)$feed['item_id'])
                    ->execute('getSlaveRow');

                if(!empty($itemPhoto) && $lastId) {
                    db()->update(Phpfox::getT((isset($callback['table_prefix']) ? $callback['table_prefix'] : '') . 'feed'), ['item_id' => $lastId], 'feed_id = '. (int)$feedId);
                    if(!empty($callback['module'])) {
                        $check = db()->select('feed_id')
                            ->from(Phpfox::getT('feed'))
                            ->where('type_id = "photo" AND item_id = ' . (int)$feed['item_id'] . ' AND user_id = ' . (int)$feed['user_id'])
                            ->execute('getSlaveField');
                        if($check) {
                            db()->update(Phpfox::getT('feed'), ['item_id' => $lastId], 'feed_id = ' . (int)$check);
                        }
                    }
                    db()->update(Phpfox::getT('photo'), ['time_stamp' => $feed['time_stamp'],'is_cover' => 1, 'album_id' => (int)$itemPhoto['album_id'], 'type_id' => $itemPhoto['type_id'], 'user_id' => $itemPhoto['user_id']], 'photo_id = ' . (int)$lastId);

                    $addItemPhotoFeed = false;
                    if($lastId != $feed['item_id']) {
                        if(!in_array($feed['item_id'], $editedPhotos)) {
                            $deletedPhotos[] = $feed['item_id'];
                        }
                        else {
                            $addItemPhotoFeed = true;
                        }
                        $deletePhotoFeed[] = $lastId;
                    }

                    if(!empty($editedPhotos)) {
                        db()->update(Phpfox::getT('photo'), ['is_cover' => 0], 'album_id=' . (int)$itemPhoto['album_id']);
                        db()->update(Phpfox::getT('photo'), ['album_id' => (int)$itemPhoto['album_id'], 'type_id' => $itemPhoto['type_id'], 'user_id' => $itemPhoto['user_id']], 'photo_id IN (' . implode(',', $editedPhotos) . ')');

                        $currentFeedPhotos = db()->select('photo_id')
                            ->from(Phpfox::getT('photo_feed'))
                            ->where('feed_id = ' . (int)$feedId)
                            ->execute('getSlaveRows');
                        if(!empty($currentFeedPhotos)) {
                            $currentFeedPhotos = array_column($currentFeedPhotos, 'photo_id');
                        }
                        foreach($editedPhotos as $editedPhotoId) {
                            if(!in_array($editedPhotoId, $currentFeedPhotos) && ($editedPhotoId != $feed['item_id'])) {
                                db()->insert(Phpfox::getT('photo_feed'), [
                                    'feed_id' => $feedId,
                                    'photo_id' => $editedPhotoId,
                                    'feed_table' => (!empty($callback['table_prefix']) ? $callback['table_prefix'] : '') . 'feed'
                                ]);
                            }
                        }

                        if($addItemPhotoFeed) {
                            db()->insert(Phpfox::getT('photo_feed'), [
                                'feed_id' => $feedId,
                                'photo_id' => $feed['item_id'],
                                'feed_table' => (!empty($callback['table_prefix']) ? $callback['table_prefix'] : '') . 'feed'
                            ]);
                        }
                    }
                }
                else {
                    //change this post type from photo to user status
                }

            }
            $cachePhotos = Phpfox::getService('ynfeed')->getCachedPhotos($feedId, $callback['module']);
            if(!empty($cachePhotos)) {
                $cachePhotos = array_column($cachePhotos, 'photo_id');
                $diffCachedAndDeleted = !empty($editedPhotos) ? array_diff($cachePhotos, $editedPhotos) : $cachePhotos;
                $cacheDeleted = !empty($uploadedPhotos) && !empty($diffCachedAndDeleted) ? array_diff($diffCachedAndDeleted, $uploadedPhotos) : $diffCachedAndDeleted;
                $deletedPhotos = !empty($deletedPhotos) ? (!empty($cacheDeleted) ? array_merge($deletedPhotos, $cacheDeleted) : $deletedPhotos) : $cacheDeleted;
            }
            if(!empty($deletedPhotos)) {
                foreach($deletedPhotos as $deletedPhoto) {
                    Phpfox::getService('photo.process')->delete($deletedPhoto);
                    if(in_array($deletedPhoto, $cachePhotos)) {
                        storage()->del('ynfeed_pending_photo_' . $deletedPhoto);
                    }
                }
                db()->updateCount('photo', 'album_id = '. (int)$itemPhoto['album_id'], 'total_photo', 'photo_album', 'album_id = '. (int)$itemPhoto['album_id']);
            }

            if(!empty($deletePhotoFeed)) {
                db()->delete(Phpfox::getT('photo_feed'), 'photo_id IN ('. implode(',', $deletePhotoFeed) . ') AND feed_id = '. (int)$feedId);
            }

            $this->cache()->remove('ynfeed_extra_' . $feedId . '_' . Phpfox::getUserId());
            if($feed['user_id'] != Phpfox::getUserId()) {
                $this->cache()->remove('ynfeed_extra_' . $feedId . '_' . $feed['user_id']);
            }

            return $lastId;
        }
    }

    public function updateFeedTagData($typeId, $itemId, $params)
    {
        if (empty($typeId) || empty($itemId)) {
            return false;
        }

        if (empty($params)) {
            return false;
        }

        $feedTag = db()->select('item_id, type_id')
                    ->from(':feed_tag_data')
                    ->where([
                        'type_id' => $typeId,
                        'item_id' => $itemId
                    ])->executeRow(false);
        if (empty($feedTag['item_id']) || empty($feedTag['type_id'])) {
            return false;
        }

        $update = [];
        if (isset($params['new_item_id'])) {
            $update['item_id'] = $params['new_item_id'];
        }

        if (empty($update)) {
            return false;
        }

        if ($success = db()->update(':feed_tag_data', $update, ['item_id' => $itemId, 'type_id' => $typeId])) {
            if (isset($params['new_item_id'])) {
                db()->update(':ynfeed_feed_map', [
                    'item_id' => $params['new_item_id']
                ], [
                    'item_id' => $itemId,
                    'item_type' => $typeId,
                    'type_id' => 'tag',
                ]);
            }
        }

        return $success;
    }
}