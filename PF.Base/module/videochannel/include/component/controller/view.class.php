<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

class Videochannel_Component_Controller_View extends Phpfox_Component
{

    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {

        Phpfox::getUserParam('videochannel.can_access_videos', true);
        $aCallback = $this->getParam('aCallback', false);
        $iVideo = $this->request()->getInt(($aCallback !== false ? $aCallback['request'] : 'req2'));
        if (Phpfox::isUser() && Phpfox::isModule('notification')) {
            Phpfox::getService('notification.process')->delete('videochannel_like', $this->request()->getInt('req2'),
                Phpfox::getUserId());
            Phpfox::getService('notification.process')->delete('videochannel', $this->request()->getInt('req2'),
                Phpfox::getUserId());
        }
        // Get video item.
        if (!($aVideo = Phpfox::getService('videochannel')->callback($aCallback)->getVideo($iVideo))) {
            return Phpfox_Error::display(_p('videochannel.the_video_you_are_looking_for_does_not_exist_or_has_been_removed'));
        }
        // Delete notification.
        if (Phpfox::getUserId() == $aVideo['user_id'] && Phpfox::isModule('notification')) {
            Phpfox::getService('notification.process')->delete('videochannel_approved',
                $this->request()->getInt('req2'), Phpfox::getUserId());
        }
        // Check privacy.
        if (Phpfox::isModule('privacy')) {
            Phpfox::getService('privacy')->check('channel_video', $aVideo['video_id'], $aVideo['user_id'],
                $aVideo['privacy'], $aVideo['is_friend']);
        }
        $this->setParam('aVideo', $aVideo);
        $this->setParam('sGroup', ($this->request()->get('req1') == 'group') ? $this->request()->get('req2') : '');
        $arRatingCallback = array(
            'type' => 'videochannel',
            'total_rating' => _p('videochannel.total_rating_ratings', array('total_rating' => $aVideo['total_rating'])),
            //$aVideo['total_rating'] . ' Ratings',
            'default_rating' => $aVideo['total_score'],
            'item_id' => $aVideo['video_id'],
            'stars' => array(
                '2' => _p('videochannel.poor'),
                '4' => _p('videochannel.nothing_special'),
                '6' => _p('videochannel.worth_watching'),
                '8' => _p('videochannel.pretty_cool'),
                '10' => _p('videochannel.awesome')
            )
        );
        $this->setParam('aRatingCallback', $arRatingCallback);
        $arFeed = array(
            'comment_type_id' => 'videochannel',
            'privacy' => $aVideo['privacy'],
            'comment_privacy' => $aVideo['privacy_comment'],
            'like_type_id' => 'videochannel',
            'feed_is_liked' => (isset($aVideo['is_liked']) ? $aVideo['is_liked'] : false),
            'feed_is_friend' => $aVideo['is_friend'],
            'item_id' => $aVideo['video_id'],
            'user_id' => $aVideo['user_id'],
            'total_comment' => $aVideo['total_comment'],
            'total_like' => $aVideo['total_like'],
            'feed_link' => Phpfox::permalink('videochannel', $aVideo['video_id'], $aVideo['title']),
            'feed_title' => $aVideo['title'],
            'feed_display' => 'view',
            'feed_total_like' => $aVideo['total_like'],
            'report_module' => 'videochannel',
            'report_phrase' => _p('videochannel.report_this_video')
        );
        $this->setParam('aFeed', $arFeed);

        if (!empty($aVideo['module_id']) && $aVideo['module_id'] != 'videochannel') {
            if (Phpfox::hasCallback($aVideo['module_id'], 'getVideoDetails') && Phpfox::isModule('pages')) {
                if ($aCallback = Phpfox::callback($aVideo['module_id'] . '.getVideoDetails', $aVideo)) {
                    $this->template()
                        ->setBreadcrumb($aCallback['breadcrumb_title'], $aCallback['breadcrumb_home'])
                        ->setBreadcrumb($aCallback['title'], $aCallback['url_home']);
                }
            } else {
                if (Phpfox::isModule('pages')) {
                    if ($aCallback = $this->getVideoDetails($aVideo)) {
                        $this->template()
                            ->setBreadcrumb($aCallback['breadcrumb_title'], $aCallback['breadcrumb_home'])
                            ->setBreadcrumb($aCallback['title'], $aCallback['url_home']);
                    }
                } else {
                    return Phpfox_Error::display(_p('videochannel.the_video_you_are_looking_for_does_not_exist_or_has_been_removed'));
                }
            }
            if (Phpfox::isModule('pages')) {
                if (($aVideo['module_id'] == 'pages') && !Phpfox::getService($aVideo['module_id'])->hasPerm($aCallback['item_id'],
                        'videochannel.view_browse_videos')) {
                    return Phpfox_Error::display(_p('videochannel.unable_to_view_this_item_due_to_privacy_settings'));
                }
            } elseif (Phpfox::isModule('groups')) {
                if ($aVideo['module_id'] == 'groups' && !Phpfox::getService($aVideo['module_id'])->hasPerm($aCallback['item_id'],
                        'videochannel.view_browse_videos')) {
                    return Phpfox_Error::display(_p('videochannel.unable_to_view_this_item_due_to_privacy_settings'));
                } else {
                    return Phpfox_Error::display(_p('videochannel.the_video_you_are_looking_for_does_not_exist_or_has_been_removed'));
                }
            }
        }
        if (Phpfox::isModule('rate')) {
            $this->template()->setPhrase(array('rate.thanks_for_rating'));
        }
        $bIsFavourite = Phpfox::getService('videochannel')->isFavourite($aVideo['video_id']);

        //  support HTTPS/HTTP
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
            if (isset($aVideo['embed_code'])) {
                $aVideo['embed_code'] = str_replace('http://', 'https://', $aVideo['embed_code']);
            }
        }
        $aVideo['clean_description'] = str_replace(array("\n", "\r", "\r\n"), '', $aVideo['text']);



        /*pending*/
        if ($aVideo['view_id'] == 2) {
            $aTitleLabel['label']['pending'] = [
                'title' => '',
                'title_class' => 'flag-style-arrow',
                'icon_class' => 'clock-o'

            ];
            $aPendingItem = [
                'message' => _p('video_is_pending_approval'),
                'actions' => []
            ];
            if (Phpfox::getUserParam('videochannel.can_approve_videos') && $aVideo['view_id'] == 2) {
                $aPendingItem['actions']['approve'] = [
                    'is_ajax' => true,
                    'label' => _p('approve'),
                    'action' => '$.ajaxCall(\'videochannel.approve\', \'inline=true&amp;video_id=' . $aVideo['video_id'] . '\');'
                ];
            }
            if (($aVideo['user_id'] == Phpfox::getUserId() && Phpfox::getUserParam('videochannel.can_edit_own_video')) || Phpfox::getUserParam('videochannel.can_edit_other_video')) {
                $aPendingItem['actions']['edit'] = [
                    'is_ajax' => true,
                    'label' => _p('edit'),
                    'action' => 'tb_show(\'' . _p('videochannel.edit_this_video') . '\', $.ajaxBox(\'videochannel.edit\', \'video_id=' . $aVideo['video_id'] . '\'));',
                ];
            }
            if (($aVideo['user_id'] == Phpfox::getUserId() && Phpfox::getUserParam('videochannel.can_delete_own_video')) || Phpfox::getUserParam('videochannel.can_delete_other_video')) {
                $aPendingItem['actions']['delete'] = [
                    'is_confirm' => true,
                    'confirm_message' => _p('are_you_sure_you_want_to_delete_this_video_permanently'),
                    'label' => _p('delete'),
                    'action' => $this->url()->makeUrl('videochannel',['delete' => $aVideo['video_id']])
                ];
            }
            $this->template()->assign([
                'aPendingItem' => $aPendingItem
            ]);
        }

        if(!empty($aVideo['is_featured'])) {
            Phpfox::getLib('module')->appendPageClass('item-featured');
        }
        if (!empty($aVideo['is_sponsor'])) {
            Phpfox::getLib('module')->appendPageClass('item-sponsor');
        }

        $aTitleLabel = [
            'type_id' => 'videochannel'
        ];

        if (!empty($aVideo['is_featured'])) {
            $aTitleLabel['label']['featured'] =[
                'title' => '',
                'title_class' => 'flag-style-arrow',
                'icon_class'  => 'diamond'

            ];
        }
        if (!empty($aVideo['is_sponsor'])) {
            $aTitleLabel['label']['sponsored'] = [
                'title' => '',
                'title_class' => 'flag-style-arrow',
                'icon_class'  => 'sponsor'

            ];
        }

        $aTitleLabel['total_label'] = isset($aTitleLabel['label']) ? count($aTitleLabel['label']) : 0;

        $this->template()
            ->setTitle($aVideo['title'])
            ->setTitle(_p('videochannel.videochannel'))
            ->setBreadcrumb(_p('videochannel.videochannel'),
                ($aCallback === false ? $this->url()->makeUrl('videochannel') : $aCallback['url_home'] . 'videochannel'))
            ->setBreadcrumb($aVideo['title'],
                $this->url()->permalink('videochannel', $aVideo['video_id'], $aVideo['title']), true)
            ->setMeta('description', $aVideo['title'] . '.' . (!empty($aVideo['text']) ? $aVideo['text'] : ''))
            ->setMeta('keywords', $this->template()->getKeywords($aVideo['title']))
            ->setHeader('cache', array(
                    'video.js' => 'module_videochannel',
                    'videochannel.js' => 'module_videochannel',
                    'jquery.rating.css' => 'style_css',
                    'jquery/plugin/star/jquery.rating.js' => 'static_script',
                    'jquery/plugin/jquery.highlightFade.js' => 'static_script',
                    'rate.js' => 'module_rate',
                    'jquery/plugin/jquery.scrollTo.js' => 'static_script',
                    'quick_edit.js' => 'static_script',
                    'pager.css' => 'style_css',
                    'switch_legend.js' => 'static_script',
                    'switch_menu.js' => 'static_script',
                    'feed.js' => 'module_feed'
                )
            )
            ->setEditor(array('load' => 'simple'))
            ->assign(array(
                'aVideo' => $aVideo,
                'bIsFavourite' => $bIsFavourite,
                'sCorePath' => Phpfox::getParam('core.path'),
                'sAddThisPubId' => setting('core.addthis_pub_id', ''),
                'bShowAddThisSection' => setting('core.show_addthis_section', false),
                'aTitleLabel' => $aTitleLabel
            ));
        (($sPlugin = Phpfox_Plugin::get('videochannel.component_controller_view_end')) ? eval($sPlugin) : false);

        if (Phpfox::isModule('rate')) {
            $this->template()->setHeader(array(
                '<script type="text/javascript">$Behavior.rateVideo = function() { $Core.rate.init({module: \'videochannel\', display: ' . ($aVideo['has_rated'] ? 'false' : ($aVideo['user_id'] == Phpfox::getUserId() ? 'false' : 'true')) . ', error_message: \'' . ($aVideo['has_rated'] ? _p('videochannel.you_have_already_voted',
                    array('phpfox_squote' => true)) : _p('videochannel.you_cannot_rate_your_own_video',
                    array('phpfox_squote' => true))) . '\'}); };</script>'
            ));
        }
        if (!$aVideo['is_stream']) {
            $sVideoPath = (preg_match("/\{file\/videos\/(.*)\/(.*)\.flv\}/i", $aVideo['destination'],
                $aMatches) ? Phpfox::getParam('core.path') . str_replace(array('{', '}'), '',
                    $aMatches[0]) : Phpfox::getParam('video.url') . $aVideo['destination']);
            if (!empty($aVideo['server_id'])) {
                $sTempVideoPath = Phpfox::getLib('cdn')->getUrl($sVideoPath, $aVideo['server_id']);
                if (!empty($sTempVideoPath)) {
                    $sVideoPath = $sTempVideoPath;
                }
            }
            (($sPlugin = Phpfox_Plugin::get('videochannel.component_controller_view_video_path')) ? eval($sPlugin) : false);
            $this->template()->setHeader(
                array(
                    'player/flowplayer/flowplayer.js' => 'static_script',
                    'player/' . Phpfox::getParam('core.default_music_player') . '/core.js' => 'static_script',
                    '<script type="text/javascript">$Behavior.playVideo = function() { $Core.player.load({id: \'js_video_player\', auto: true, type: \'video\', play: \'' . $sVideoPath . '\'}); }</script>'
                )
            );
        }
        //to make facebook know the image
        if (!empty($aVideo['image_path'])) {
            $sImage = Phpfox::getLib('image.helper')->display(array(
                    'server_id' => $aVideo['image_server_id'],
                    'path' => 'core.url_pic',
                    'file' => $aVideo['image_path'],
                    'suffix' => '_120',
                    'return_url' => true
                )
            );
        } else {
            $sImage = Phpfox::getParam('core.path_file') . 'module/videochannel/static/image/noimg_video.jpg';
        }
        $this->template()->setMeta('og:image', $sImage);
        $this->template()->setMeta('og:image:width', 640);
        $this->template()->setMeta('og:image:height', 442);
        if (isset($aVideo['breadcrumb']) && is_array($aVideo['breadcrumb'])) {
            foreach ($aVideo['breadcrumb'] as $aParentCategory) {
                if (isset($aParentCategory[0])) {
                    $strCategoryLink = '';
                    if ($aVideo['module_id'] == 'pages' && $aCallback !== false) {
                        $strCategoryLink = $aCallback['url_home'] . 'videochannel/category/' . $aParentCategory[2] . '/' . $this->url()->cleanTitle($aParentCategory[0]);
                    } else {
                        $strCategoryLink = $aParentCategory[1];
                    }
                    $this->template()
                        ->setBreadcrumb($aParentCategory[0], $strCategoryLink)
                        ->setMeta('description', $aParentCategory[0])
                        ->setMeta('keywords', $this->template()->getKeywords($aParentCategory[0]));
                }
            }
        } else {
            $this->template()->setBreadCrumb(_p('Un-Category'), '', false);
        }
    }

    public function getVideoDetails($aItem)
    {
        Phpfox::getService('groups')->setIsInPage();

        $aRow = Phpfox::getService('groups')->getPage($aItem['item_id']);

        if (!isset($aRow['page_id'])) {
            return false;
        }

        Phpfox::getService('groups')->setMode();

        $sLink = Phpfox::getService('groups')->getUrl($aRow['page_id'], $aRow['title'], $aRow['vanity_url']);

        return array(
            'breadcrumb_title' => _p('Groups'),
            'breadcrumb_home' => Phpfox_Url::instance()->makeUrl('pages'),
            'module_id' => 'pages',
            'item_id' => $aRow['page_id'],
            'title' => $aRow['title'],
            'url_home' => $sLink,
            'url_home_photo' => $sLink . 'video/',
            'theater_mode' => _p('in_the_page_link_title', array('link' => $sLink, 'title' => $aRow['title']))
        );
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('videochannel.component_controller_view_clean')) ? eval($sPlugin) : false);
    }

}

?>
