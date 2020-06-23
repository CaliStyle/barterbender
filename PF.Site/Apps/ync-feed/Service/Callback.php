<?php

namespace Apps\YNC_Feed\Service;

use Core;
use Phpfox;
use Phpfox_Template;
use Phpfox_Url;
use Phpfox_Error;
use Phpfox_Plugin;
use User_Service_User;
use Phpfox_Ajax;
use Comment_Service_Comment;
use Notification_Service_Process;
use Notification_Service_Notification;
defined('PHPFOX') or exit('NO DICE!');

/**
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Feed
 */
class Callback extends \Feed_Service_Callback
{
    /**
     * @return array
     */
    public function getProfileSettings()
    {
        $aOut = [
            'ynfeed_view_tagged_posts_on_wall' => [
                'phrase'  => _p('who_can_see_posts_you_have_been_tagged_in'),
                'default' => '0'
            ],
            'ynfeed_view_other_posts_on_wall' => [
                'phrase'  => _p('who_can_see_what_others_post_on_your_wall'),
                'default' => '0'
            ],
        ];
        return $aOut;
    }

    /**
     * @return array
     */
    public function getNotificationSettings() {
        return [
            'ynfeed.tagged_in_post' => [
                'phrase'  => _p('tagged_in_a_post'),
                'default' => 1
            ],
            'ynfeed.post_on_wall' => [
                'phrase'  => _p('post_on_my_wall'),
                'default' => 1
            ]
        ];
    }
    /**
     * @return array
     */
    public function getGroupPerms()
    {
        $aPerms = [
            'ynfeed.tag_in_feed' => _p('who_can_tag_this_group')
        ];
        return $aPerms;
    }

    /**
     * @return array
     */
    public function getPagePerms()
    {
        $aPerms = [
            'ynfeed.tag_in_feed' => _p('who_can_tag_this_page')
        ];
        return $aPerms;
    }

    /**
     * @param array $aNotification
     *
     * @return array|bool
     */
    public function getNotificationMention($aNotification) {
        $aMap = db()->select('*')
            ->from(Phpfox::getT('ynfeed_notification_map'))
            ->where('map_id = ' . $aNotification['item_id'])
            ->execute('getSlaveRow');
        $aCallback = json_decode($aMap['callback'], true);
        $aFeed = Phpfox::getService('ynfeed')->callback($aCallback)->get(null, $aMap['item_id']);
        if(isset($aFeed[0]))
            $aFeed = $aFeed[0];
        else return false;

        $aFeedMap = db()->select('*')
            ->from(Phpfox::getT('ynfeed_feed_map'))
            ->where('item_id = ' . $aFeed['item_id'])
            ->execute('getSlaveRow');
        if (!empty($aFeedMap) && $aFeedMap['parent_user_type'] == 'car') {
            $aCar = Phpfox::getService('ynclistingcar')->getQuickBusinessById($aFeedMap['parent_user_id']);
            if (empty($aCar)) {
                return false;
            }
            $sPhrase = _p('someone_mention_your_car_name_in_gender_something', [
                'someone' => $aFeed['full_name'],
                'name' => Phpfox::getLib('parse.output')->shorten($aCar['name'], Phpfox::getParam('notification.total_notification_title_length'), '...'),
                'gender' => Phpfox::getService('user')->gender($aFeed['gender'], 1),
                'something' => _p('post')
            ]);
        } else {
            $sPhrase = _p('someone_mention_you_in_gender_something', [
                'someone' => $aFeed['full_name'],
                'gender' => Phpfox::getService('user')->gender($aFeed['gender'], 1),
                'something' => _p('post')
            ]);
        }

//        $sLink = (isset($aCallback['link']) && $aCallback['link']) ? $aCallback['link'] : $aFeed['feed_link'];
        $sLink = (isset($aFeed['feed_link']) && $aFeed['feed_link']) ? $aFeed['feed_link'] : $aCallback['link'];
        return array(
            'link' => $sLink,
            'message' => $sPhrase,
            'icon' => Phpfox_Template::instance()->getStyle('image', 'activity.png', 'blog')
        );
    }

    /**
     * @param array $aNotification
     *
     * @return array|bool
     */
    public function getNotificationTag($aNotification) {
        $aMap = db()->select('*')
            ->from(Phpfox::getT('ynfeed_notification_map'))
            ->where('map_id = ' . $aNotification['item_id'])
            ->execute('getSlaveRow');
        if(!$aMap)
            return false;
        $aCallback = json_decode($aMap['callback'], true);
        $aFeed = Phpfox::getService('ynfeed')->callback($aCallback)->get(null, $aMap['item_id']);
        if(isset($aFeed[0]))
            $aFeed = $aFeed[0];
        else return false;
        $sPhrase = _p('someone_tagged_you_in_gender_something', [
            'someone' => $aFeed['full_name'],
            'gender' => Phpfox::getService('user')->gender($aFeed['gender'], 1),
            'something' => _p('post')
        ]);
        // Always redirect to a feed entry
        $sLink = (isset($aFeed['feed_link']) && $aFeed['feed_link']) ? $aFeed['feed_link'] : $aCallback['link'];

        $aFeedLinkNeedles = ['status-id', 'comment-id'];
        $bIsFeedLink = false;
        foreach($aFeedLinkNeedles as $needle) {
            $bIsFeedLink = $bIsFeedLink || strpos($sLink, $needle);
        }

        // Correct url for YouNetCo's modules
        $sLink = preg_replace('/(?:((?:auction|directory)(?:\/detail)|(?:social-store)(?:\/store)))((?:\/[\d]+)(?:\/[^\/]*))(.*)/', '$1$2/activities$3', $sLink);
        if(empty($aCallback['link'])) {
            switch (@$aCallback['module']) {
                case 'event':
                    $aCallback['link'] = Phpfox::getLib('url')->permalink('event', $aFeed['custom_data_cache']['parent_user_id']);
                    break;
                case 'pages':
                case 'groups':
                    $aCallback['link'] = Phpfox::getLib('url')->permalink($aCallback['module'], $aFeed['profile_page_id'] ?  $aFeed['profile_page_id'] : $aFeed['parent_user_id']);
                    break;
                case 'ynsocialstore':
                    $aCallback['link'] = Phpfox::getLib('url')->permalink('social-store.store', $aFeed['parent_user_id'], 'store') . 'activities';
                    break;
                case 'ecommerce':
                    $aCallback['link'] = Phpfox::getLib('url')->permalink('auction.detail', $aFeed['parent_user_id'], 'auction') . 'activities';
                    break;
                case 'directory':
                    $aCallback['link'] = Phpfox::getLib('url')->permalink('directory.detail', $aFeed['parent_user_id'], 'directory') . 'activities';
                    break;
            }
        }
        // Add feed id query
        if(!$bIsFeedLink) {
            if (@$aCallback['link']) {
                if (parse_url($aCallback['link'], PHP_URL_QUERY)) {
                    $sLink = $aCallback['link'] . '&feed=' . $aFeed['feed_id'];
                } else {
                    $sLink = $aCallback['link'] . '?feed=' . $aFeed['feed_id'];
                }
            } else {
                $sLink = Phpfox::getLib('url')->makeUrl('', ['feed' => $aFeed['feed_id']]);
                $support_themes = ['ynclean', 'ynresbusiness', 'ynresphoenix', 'ynrespassion'];
                foreach($support_themes as $theme) {
                    if (Phpfox::isModule($theme)) {
                        if($theme == 'ynrespassion') {
                            $theme = 'responsive-passion';
                        }
                        $sLink = Phpfox::getLib('url')->makeUrl("$theme.dashboard", ['feed' => $aFeed['feed_id']]);
                    }
                }
            }
        }
        return array(
            'link' => $sLink,
            'message' => $sPhrase,
            'icon' => Phpfox_Template::instance()->getStyle('image', 'activity.png', 'blog')
        );
    }

    /**
     * @param array $aNotification
     *
     * @return array|bool
     */
    public function getNotificationComment_Tag($aNotification) {
        $aMap = db()->select('*')
            ->from(Phpfox::getT('ynfeed_notification_map'))
            ->where('map_id = ' . $aNotification['item_id'])
            ->execute('getSlaveRow');
        $aCallback = json_decode($aMap['callback'], true);
        $aFeed = Phpfox::getService('ynfeed')->callback($aCallback)->get(null, $aMap['item_id']);
        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
        if(isset($aFeed[0]))
            $aFeed = $aFeed[0];
        else return false;
        $sPhrase = _p('someone_comment_on_a_post_that_you_were_tagged_in', [
            'someone' => $sUsers,
        ]);
        $sLink = (isset($aCallback['link']) && $aCallback['link']) ? $aCallback['link'] : $aFeed['feed_link'];
        return array(
            'link' => $sLink,
            'message' => $sPhrase,
            'icon' => Phpfox_Template::instance()->getStyle('image', 'activity.png', 'blog')
        );
    }

    /**
     * @param array $aNotification
     *
     * @return array|bool
     */
    public function getNotificationComment_Mention($aNotification) {
        $aMap = db()->select('*')
            ->from(Phpfox::getT('ynfeed_notification_map'))
            ->where('map_id = ' . $aNotification['item_id'])
            ->execute('getSlaveRow');
        $aCallback = json_decode($aMap['callback'], true);
        $aFeed = Phpfox::getService('ynfeed')->callback($aCallback)->get(null, $aMap['item_id']);
        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
        if(isset($aFeed[0]))
            $aFeed = $aFeed[0];
        else return false;
        $sPhrase = _p('someone_comment_on_a_post_that_you_were_mentioned_in', [
            'someone' => $sUsers,
        ]);
        $sLink = (isset($aCallback['link']) && $aCallback['link']) ? $aCallback['link'] : $aFeed['feed_link'];
        return array(
            'link' => $sLink,
            'message' => $sPhrase,
            'icon' => Phpfox_Template::instance()->getStyle('image', 'activity.png', 'blog')
        );
    }

    /**
     * @param array $aNotification
     *
     * @return array|bool
     */
    public function getNotificationLike_Tag($aNotification) {
        $aMap = db()->select('*')
            ->from(Phpfox::getT('ynfeed_notification_map'))
            ->where('map_id = ' . $aNotification['item_id'])
            ->execute('getSlaveRow');
        $aCallback = json_decode($aMap['callback'], true);
        $aFeed = Phpfox::getService('ynfeed')->callback($aCallback)->get(null, $aMap['item_id']);
        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
        if(isset($aFeed[0]))
            $aFeed = $aFeed[0];
        else return false;
        $sPhrase = _p('someone_like_a_post_that_you_were_tagged_in', [
            'someone' => $sUsers,
        ]);
        $sLink = (isset($aCallback['link']) && $aCallback['link']) ? $aCallback['link'] : $aFeed['feed_link'];
        return array(
            'link' => $sLink,
            'message' => $sPhrase,
            'icon' => Phpfox_Template::instance()->getStyle('image', 'activity.png', 'blog')
        );
    }

    /**
     * @param array $aNotification
     *
     * @return array|bool
     */
    public function getNotificationLike_Mention($aNotification) {
        $aMap = db()->select('*')
            ->from(Phpfox::getT('ynfeed_notification_map'))
            ->where('map_id = ' . $aNotification['item_id'])
            ->execute('getSlaveRow');
        $aCallback = json_decode($aMap['callback'], true);
        $aFeed = Phpfox::getService('ynfeed')->callback($aCallback)->get(null, $aMap['item_id']);
        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
        if(isset($aFeed[0]))
            $aFeed = $aFeed[0];
        else return false;
        $sPhrase = _p('someone_like_a_post_that_you_were_mentioned_in', [
            'someone' => $sUsers,
        ]);
        $sLink = (isset($aCallback['link']) && $aCallback['link']) ? $aCallback['link'] : $aFeed['feed_link'];
        return array(
            'link' => $sLink,
            'message' => $sPhrase,
            'icon' => Phpfox_Template::instance()->getStyle('image', 'activity.png', 'blog')
        );
    }
}