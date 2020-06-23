<?php

namespace Apps\YNC_Reaction\Service;

use Phpfox;
use Phpfox_Error;
use Phpfox_Plugin;
use Phpfox_Service;

/**
 * Class React
 * @package Apps\YNC_Reaction\Service
 */
class React extends Phpfox_Service
{

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('like');
    }

    public function add(
        $sType,
        $iItemId,
        $iUserId = null,
        $app_id = null,
        $params = [],
        $sTablePrefix = '',
        $iReactId = null,
        $bIsReReact = false
    ) {
        $bIsNotNull = false;
        if ($iUserId === null) {
            $iUserId = Phpfox::getUserId();
            $bIsNotNull = true;
        }
        if ($sType == 'pages') {
            $bIsNotNull = false;
        }

        // check if iUserId can Like this item
        $aFeed = db()->select('*')
            ->from(Phpfox::getT($sTablePrefix . 'feed'))
            ->where(($app_id === null ? 'item_id = ' . (int)$iItemId . ' AND type_id = \'' . Phpfox::getLib('parse.input')->clean($sType) . '\'' : 'feed_id = ' . (int)$iItemId))
            ->execute('getSlaveRow');

        if (!empty($aFeed) && isset($aFeed['privacy']) && !empty($aFeed['privacy']) && !empty($aFeed['user_id']) && $aFeed['user_id'] != $iUserId) {
            if (Phpfox::getService('user.block')->isBlocked($iUserId, $aFeed['user_id'])) {
                return Phpfox_Error::display(_p('you_are_not_allowed_to_react_this_item'));
            }
            if ($aFeed['privacy'] == 1 && Phpfox::isModule('friend') && Phpfox::getService('friend')->isFriend($iUserId,
                    $aFeed['user_id']) != true
            ) {
                return Phpfox_Error::display(_p('you_are_not_allowed_to_react_this_item'));
            } else {
                if ($aFeed['privacy'] == 2 && Phpfox::isModule('friend') && Phpfox::getService('friend')->isFriendOfFriend($iUserId) != true) {
                    return Phpfox_Error::display(_p('you_are_not_allowed_to_react_this_item'));
                } else {
                    if ($aFeed['privacy'] == 3 && ($aFeed['user_id'] != Phpfox::getUserId())) {
                        return Phpfox_Error::display(_p('you_are_not_allowed_to_react_this_item'));
                    } else {
                        if ($aFeed['privacy'] == 4 && ($bCheck = Phpfox::getService('privacy')->check($sType, $iItemId,
                                $aFeed['user_id'], $aFeed['privacy'], null, true)) != true
                        ) {
                            return Phpfox_Error::display(_p('you_are_not_allowed_to_react_this_item'));
                        }
                    }
                }
            }
        }

        $iCheck = db()->select('COUNT(*)')
            ->from($this->_sTable)
            ->where('type_id = \'' . db()->escape($sType) . '\' AND item_id = ' . (int)$iItemId . ' AND user_id = ' . $iUserId)
            ->execute('getSlaveField');

        if ($iCheck) {
            if ($bIsReReact) {
                $iCheckReacted = db()->select('COUNT(*)')
                    ->from($this->_sTable)
                    ->where('type_id = \'' . db()->escape($sType) . '\' AND item_id = ' . (int)$iItemId . ' AND user_id = ' . $iUserId . ' AND ync_react_id = ' . (int)$iReactId)
                    ->execute('getSlaveField');
                if ($iCheckReacted) {
                    return false;
                }
                $this->delete($sType, $iItemId, $iUserId, false, $sTablePrefix);
            } else {
                return Phpfox_Error::set(_p('you_have_already_reacted_this_feed'));
            }
        }

        //check permission when like an item
        if (empty($params['ignoreCheckPermission']) && Phpfox::isModule($sType) && Phpfox::hasCallback($sType,
                'canLikeItem') && !Phpfox::callback($sType . '.canLikeItem', $iItemId)
        ) {
            return Phpfox_Error::set(_p('you_are_not_allowed_to_react_this_item'));
        }

        $iCnt = (int)db()->select('COUNT(*)')
            ->from(Phpfox::getT('like_cache'))
            ->where('type_id = \'' . db()->escape($sType) . '\' AND item_id = ' . (int)$iItemId . ' AND user_id = ' . (int)$iUserId)
            ->execute('getSlaveField');

        $data = [
            'type_id' => $sType,
            'item_id' => (int)$iItemId,
            'user_id' => $iUserId,
            'ync_react_id' => (int)$iReactId,
            'time_stamp' => PHPFOX_TIME
        ];

        if ($sType == 'app') {
            $data['feed_table'] = $sTablePrefix . 'feed';
        }

        db()->insert($this->_sTable, $data);

        //Update time_update of feed when like
        if (Phpfox::getParam('feed.top_stories_update') != 'comment') {
            db()->update(Phpfox::getT($sTablePrefix . 'feed'), [
                'time_update' => PHPFOX_TIME
            ], [
                    'item_id' => (int)$iItemId,
                    'type_id' => $sType
                ]
            );

            if (!empty($sTablePrefix)) {
                db()->update(Phpfox::getT('feed'), [
                    'time_update' => PHPFOX_TIME
                ], [
                        'item_id' => (int)$iItemId,
                        'type_id' => $sType
                    ]
                );
            }
        }
        if (!$iCnt) {
            db()->insert(Phpfox::getT('like_cache'), array(
                    'type_id' => $sType,
                    'item_id' => (int)$iItemId,
                    'user_id' => $iUserId
                )
            );
        }

        Phpfox::getService('feed.process')->clearCache($sType, $iItemId);

        if ($sPlugin = Phpfox_Plugin::get('like.service_process_add__1')) {
            eval($sPlugin);
        }

        if (redis()->enabled()) {
            redis()->set('is/feed/liked/' . user()->id . '/' . $sType . '/' . $iItemId, 1);
            redis()->lpush('likes/' . $sType . '/' . $iItemId, user()->id);
            redis()->incr('total/feed/liked/' . $sType . '/' . $iItemId);
        }

        if ($sType == 'app') {
            $app = app($app_id);

            if (isset($app->notifications) && isset($app->notifications->{'__like'})) {
                notify($app->id, '__like', $iItemId, $aFeed['user_id'], false);
            }

            return true;
        }

        Phpfox::callback($sType . '.addLike', $iItemId, ($iCnt ? true : false), ($bIsNotNull ? null : $iUserId));

        return true;
    }

    public function delete($sType, $iItemId, $iUserId = 0, $bDeleteItem = false, $sTablePrefix = '')
    {
        $sExtraCond = ($sType == 'app') ? " AND feed_table = '{$sTablePrefix}feed'" : '';
        if ($iUserId > 0 || ($sType != 'pages' && $sType != 'groups')) {
            if (!$bDeleteItem) {
                $iUserId = Phpfox::getUserId();
            } else {
                $iUserId = 0;
            }
        }
        $aLike = db()->select('like_id, user_id')
            ->from($this->_sTable)
            ->where('type_id = \'' . db()->escape($sType) . '\' AND item_id = ' . (int)$iItemId . ($iUserId ? ' AND user_id = ' . $iUserId : '') . $sExtraCond)
            ->execute('getRow');
        if (!$aLike) {
            return false;
        }
        if ($iUserId > 0 && ($sType == 'pages' || $sType == 'groups')) {
            if (!Phpfox::getService($sType)->isAdmin($iItemId)) {
                return Phpfox_Error::set(_p('unable_to_remove_this_user_dot'));
            }

            db()->delete($this->_sTable, 'like_id =' . (int)$aLike['like_id']);
        } else {
            if (!$bDeleteItem) {
                $iUserId = Phpfox::getUserId();
                db()->delete($this->_sTable, 'like_id =' . (int)$aLike['like_id']);
            } else {
                db()->delete($this->_sTable, 'like_id =' . (int)$aLike['like_id']);
                db()->delete(Phpfox::getT('like_cache'),
                    'type_id = \'' . db()->escape($sType) . '\' AND item_id = ' . (int)$iItemId);
            }

        }

        Phpfox::getService('feed.process')->clearCache($sType, $iItemId);

        if (redis()->enabled()) {
            redis()->del('is/feed/liked/' . user()->id . '/' . $sType . '/' . $iItemId);
            redis()->lrem('likes/' . $sType . '/' . $iItemId, 0, user()->id);
            redis()->decr('total/feed/liked/' . $sType . '/' . $iItemId);
        }
        if (!$bDeleteItem) {
            Phpfox::callback($sType . '.deleteLike', $iItemId, $iUserId);
        }

        return true;
    }

    /**
     * If a call is made to an unknown method attempt to connect
     * it to a specific plug-in with the same name thus allowing
     * plug-in developers the ability to extend classes.
     *
     * @param string $sMethod is the name of the method
     * @param array $aArguments is the array of arguments of being passed
     *
     * @return null
     */
    public function __call($sMethod, $aArguments)
    {
        /**
         * Check if such a plug-in exists and if it does call it.
         */
        if ($sPlugin = Phpfox_Plugin::get('react.service_process__call')) {
            eval($sPlugin);
            return null;
        }

        /**
         * No method or plug-in found we must throw a error.
         */
        Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
    }
}