<?php
/**
 * User: YouNetCo
 * Date: 5/9/18
 * Time: 5:51 PM
 */

namespace Apps\YNC_PhotoViewPop\Controller;

use Phpfox;
use Phpfox_Component;
use Phpfox_Error;
use Phpfox_Plugin;
use Phpfox_Url;

class ViewController extends Phpfox_Component
{
    public function process()
    {
        (($sPlugin = Phpfox_Plugin::get('yncphotovp.component_controller_view__1')) ? eval($sPlugin) : false);

        if (!Phpfox::getUserParam('photo.can_view_photos', false)) {
            Phpfox_Url::instance()->send('subscribe.message');
        }
        define('PHPFOX_SHOW_TAGS', true);

        (($sPlugin = Phpfox_Plugin::get('yncphotovp.component_controller_view__2')) ? eval($sPlugin) : false);

        $aCallback = $this->getParam('aCallback', null);
        $sId = $this->request()->get('photo_id');
        $this->setParam('sTagType', 'photo');
        $sLink = $this->request()->get('slink');

        (($sPlugin = Phpfox_Plugin::get('yncphotovp.component_controller_view_process_start')) ? eval($sPlugin) : false);

        $aPhoto = Phpfox::getService('photo')->getPhoto($sId);

        if (!empty($aPhoto['module_id']) && $aPhoto['module_id'] != 'photo') {
            if ($aCallback = Phpfox::callback($aPhoto['module_id'] . '.getPhotoDetails', $aPhoto)) {
                if (Phpfox::isModule($aPhoto['module_id']) && Phpfox::hasCallback($aPhoto['module_id'],
                        'checkPermission')
                ) {
                    if (!Phpfox::callback($aPhoto['module_id'] . '.checkPermission', $aCallback['item_id'],
                        'photo.view_browse_photos')
                    ) {
                        return Phpfox_Error::display(_p('unable_to_view_this_item_due_to_privacy_settings'));
                    }
                }
            }
        }
        if (!isset($aPhoto['photo_id']) || ($aPhoto['view_id'] && !Phpfox::getUserParam('photo.can_approve_photos') && $aPhoto['user_id'] != Phpfox::getUserId())) {
            return Phpfox_Error::display(_p('sorry_the_photo_you_are_looking_for_no_longer_exists',
                array('link' => $this->url()->makeUrl('photo'))));
        }
        if (Phpfox::isUser() && Phpfox::getService('user.block')->isBlocked(null, $aPhoto['user_id'])) {
            return Phpfox_Error::display(_p('unable_to_view_this_item_due_to_privacy_settings'));
        }
        if (Phpfox::isModule('privacy')) {
            Phpfox::getService('privacy')->check('photo', $aPhoto['photo_id'], $aPhoto['user_id'], $aPhoto['privacy'],
                $aPhoto['is_friend']);
        }
        if ($aPhoto['mature'] != 0) {
            if (Phpfox::getUserId()) {
                if ($aPhoto['user_id'] != Phpfox::getUserId()) {
                    if ($aPhoto['mature'] == 2 && Phpfox::getUserParam(array(
                            'photo.photo_mature_age_limit' => array(
                                '>',
                                (int)Phpfox::getUserBy('age')
                            )
                        ))
                    ) {
                        return Phpfox_Error::display('<div class="p-2"><div class="error_message">' . _p('sorry_this_photo_can_only_be_viewed_by_those_older_then_the_age_of_limit',
                            array('limit' => Phpfox::getUserParam('photo.photo_mature_age_limit'))) . '</div></div>');
                    }
                }
            } else {
                if (!Phpfox::isUser(false)) {
                    Phpfox_Url::instance()->send('user.login');
                }
            }
        }

        $this->setParam('bIsValidImage', true); // @TODO : optimize
        $aPhoto['bookmark_url'] = $this->url()->permalink('photo', $aPhoto['photo_id'], $aPhoto['title']);

        (($sPlugin = Phpfox_Plugin::get('yncphotovp.component_controller_view_process_photo')) ? eval($sPlugin) : false);

        $this->setParam('aPhoto', $aPhoto);
        define('TAG_ITEM_ID', $aPhoto['photo_id']);

        $this->_updateCounter($aPhoto);
        $this->_setMeta($aPhoto);
        $this->_setFeedParam($aPhoto);
        $aPhotos = $this->_getRelatedPhotos($aPhoto, $aCallback);
        list($previousPhoto, $nextPhoto) = $this->_getNextPrePhotos($sId, $aPhotos);

        $aPhoto['sCategories'] = $this->_getCategories($aPhoto);

        $this->template()->setHeader('cache', array(
                'jquery/plugin/imgnotes/jquery.tag.js' => 'static_script',
                'jquery/plugin/imgnotes/jquery.imgareaselect.js' => 'static_script',
                'jquery/plugin/imgnotes/jquery.imgnotes.js' => 'static_script',
                'places.js' => 'module_feed'
            )
        );

        $bLoadCheckin = false;
        if (Phpfox::isModule('feed') && Phpfox::getParam('feed.enable_check_in') && Phpfox::getParam('core.google_api_key')) {
            $this->template()->setHeader('cache', array(
                    'places.js' => 'module_feed'
                )
            );
            $bLoadCheckin = true;
        }

        $iAvatarId = ((Phpfox::isUser()) ? storage()->get('user/avatar/' . Phpfox::getUserId()) : null);
        if ($iAvatarId) {
            $iAvatarId = $iAvatarId->value;
        }
        $iCover = storage()->get('user/cover/' . Phpfox::getUserId());
        if ($iCover) {
            $iCover = $iCover->value;
        }

        $aTitleLabel = $this->_getTitleLabels($aPhoto);

        if ($aPhoto['view_id'] == 1) {
            $aTitleLabel['label']['pending'] = [
                'title' => '',
                'title_class' => 'flag-style-arrow',
                'icon_class' => 'clock-o'

            ];

            $this->template()->assign([
                'aPendingItem' => $this->_getPendingItem($aPhoto, $iAvatarId, $iCover)
            ]);
        }

        $sPhotoJsContent = Phpfox::getService('photo.tag')->getJs($aPhoto['photo_id']);

        $aTags = db()->select('p.user_id AS photo_owner_id, pt.tag_id, pt.user_id AS post_user_id, pt.content, pt.position_x, pt.position_y, pt.width, pt.height, pt.photo_width, ' . Phpfox::getUserField())
            ->from(Phpfox::getT('photo_tag'), 'pt')
            ->leftJoin(Phpfox::getT('user'), 'u', 'u.user_id = pt.tag_user_id')
            ->join(Phpfox::getT('photo'), 'p', 'p.photo_id = pt.photo_id')
            ->where('pt.photo_id = ' . (int)$sId)
            ->execute('getSlaveRows');

        $this->template()
            ->setMeta('description', _p('full_name_s_photo_from_time_stamp', array(
                    'full_name' => $aPhoto['full_name'],
                    'time_stamp' => Phpfox::getTime(Phpfox::getParam('core.description_time_stamp'),
                        $aPhoto['time_stamp'])
                )) . ': ' . (empty($aPhoto['description']) ? $aPhoto['title'] : $aPhoto['title'] . '.' . $aPhoto['description']))
            ->setMeta('description', Phpfox::getParam('photo.photo_meta_description'))
            ->setMeta('keywords', $this->template()->getKeywords($aPhoto['title']))
            ->setMeta('keywords', Phpfox::getParam('photo.photo_meta_keywords'))
            ->setPhrase(array(
                    'none_of_your_files_were_uploaded_please_make_sure_you_upload_either_a_jpg_gif_or_png_file',
                    'updating_photo',
                    'save',
                    'cancel',
                    'click_here_to_tag_as_yourself',
                    'done_tagging'
                )
            )
            ->keepBody(true)
            ->setEditor(array(
                    'load' => 'simple'
                )
            )->assign(array(
                    'sLink' => $sLink,
                    'aForms' => $aPhoto,
                    'aCallback' => $aCallback,
                    'sPhotoJsContent' => $sPhotoJsContent,
                    'sPhotos' => json_encode($aPhotos),
                    'previousPhoto' => $previousPhoto,
                    'nextPhoto' => $nextPhoto,
                    'iAvatarId' => $iAvatarId,
                    'iCover' => $iCover,
                    'sView' => 'view',
                    'bIsDetail' => true,
                    'sAddThisShareButton' => '',
                    'sShareDescription' => str_replace(array("\n", "\r", "\r\n"), '', $aPhoto['description']),
                    'bLoadCheckin' => $bLoadCheckin,
                    'aTitleLabel' => $aTitleLabel,
                    'bHasTag' => count($aTags),
                )
            );

        if (!empty($aPhoto['album_title'])) {
            $this->template()->setTitle(Phpfox::getLib('locale')->convert($aPhoto['album_title']));
            $this->template()->setMeta('description',
                '' . _p('part_of_the_photo_album') . ': ' . $aPhoto['album_title']);
        }

        (($sPlugin = Phpfox_Plugin::get('yncphotovp.component_controller_view_process_end')) ? eval($sPlugin) : false);

        return null;
    }

    private function _updateCounter($aPhoto)
    {
        $bUpdateCounter = false;
        if (Phpfox::isModule('track')) {
            if (!$aPhoto['is_viewed']) {
                $bUpdateCounter = true;
                Phpfox::getService('track.process')->add('photo', $aPhoto['photo_id']);
            } else {
                if (!setting('track.unique_viewers_counter')) {
                    $bUpdateCounter = true;
                    Phpfox::getService('track.process')->add('photo', $aPhoto['photo_id']);
                } else {
                    Phpfox::getService('track.process')->update('photo', $aPhoto['photo_id']);
                }
            }
        } else {
            $bUpdateCounter = true;
        }
        if ($bUpdateCounter) {
            Phpfox::getService('photo.process')->updateCounter($aPhoto['photo_id'], 'total_view');
        }
    }

    private function _setMeta($aPhoto)
    {
        if (!empty($aPhoto['tag_list']) && $aPhoto['tag_list'] && Phpfox::isModule('tag')) {
            $this->template()->setMeta('keywords', Phpfox::getService('tag')->getKeywords($aPhoto['tag_list']));
        }
    }

    private function _setFeedParam($aPhoto)
    {
        $aParamFeed = [
            'comment_type_id' => 'photo',
            'privacy' => $aPhoto['privacy'],
            'comment_privacy' => Phpfox::getUserParam('photo.can_post_on_photos') ? 0 : 3,
            'like_type_id' => 'photo',
            'feed_is_liked' => $aPhoto['is_liked'],
            'feed_is_friend' => $aPhoto['is_friend'],
            'item_id' => $aPhoto['photo_id'],
            'user_id' => $aPhoto['user_id'],
            'total_comment' => $aPhoto['total_comment'],
            'total_like' => $aPhoto['total_like'],
            'feed_link' => $this->url()->permalink('photo', $aPhoto['photo_id'], $aPhoto['title']),
            'feed_title' => $aPhoto['title'],
            'feed_display' => 'view',
            'feed_total_like' => $aPhoto['total_like'],
            'report_module' => 'photo',
            'report_phrase' => _p('report_this_photo')
        ];
        //Disable like and comment if non-friend view profile|cover album
        if ($aPhoto['is_profile_photo']) {
            if (!Phpfox::getService('user.privacy')->hasAccess($aPhoto['user_id'], 'feed.share_on_wall')) {
                unset($aParamFeed['comment_type_id']);
                $aParamFeed['disable_like_function'] = true;
            }
        }

        $this->setParam('aFeed', $aParamFeed);
    }

    private function _getRelatedPhotos($aPhoto, $aCallback)
    {
        $aPhotos = [];
        $iUserId = $this->request()->get('userid') ? $this->request()->get('userid') : 0;
        $iAlbumId = $this->request()->get('albumid') ? $this->request()->get('albumid') : 0;
        $bMyPhotos = $this->request()->get('myphotos') ? $this->request()->get('myphotos') : 0;
        $iFeedId = $this->request()->getInt('feed') ? $this->request()->getInt('feed') : '0';
        $sModule = $this->request()->get('module') ? $this->request()->get('module') : 'photo';

        if ($sModule == 'advancedphoto') {
            $aConditions = array();
            $aConditions[] = 'p.album_id = ' . $aPhoto['album_id'] . '';
            $limit = '';
            if (!$aPhoto['album_id']) {
                $limit = 45;
            }
            list(, $aPhotos) = Phpfox::getService('advancedphoto')->get($aConditions, 'p.ordering DESC', '', $limit);
        } else {

            if ($iUserId || $iAlbumId) {
                $aPhotos = Phpfox::getService('photo')->getPhotos($iAlbumId, $iUserId, $aPhoto['user_id'], $aPhoto,
                    $bMyPhotos);
            }
            if (!$aPhotos && $iFeedId) {
                $sFeedTablePrefix = ($aCallback && !empty($aCallback['feed_table_prefix'])) ? $aCallback['feed_table_prefix'] : '';
                $aPhotos = Phpfox::getService('photo')->getFeedPhotos($iFeedId, null, $sFeedTablePrefix);
            }
        }

        return $aPhotos;
    }

    private function _getCategories($aPhoto)
    {
        $sCategories = '';
        if (isset($aPhoto['categories']) && is_array($aPhoto['categories'])) {
            $sCategories = implode(', ', array_map(function ($aCategory) {
                return strtr('<a href=":link">:text</a>', [
                    ':text' => $aCategory[0],
                    ':link' => $aCategory[1]
                ]);
            }, $aPhoto['categories']));
        }

        return $sCategories;
    }

    private function _getTitleLabels($aPhoto)
    {
        $aTitleLabel = [
            'type_id' => 'photo'
        ];

        if ($aPhoto['is_featured']) {
            $aTitleLabel['label']['featured'] = [
                'title' => '',
                'title_class' => 'flag-style-arrow',
                'label_class' => 'flag_style',
                'icon_class' => 'diamond'

            ];
        }
        if ($aPhoto['is_sponsor']) {
            $aTitleLabel['label']['sponsored'] = [
                'title' => '',
                'title_class' => 'flag-style-arrow',
                'label_class' => 'flag_style',
                'icon_class' => 'sponsor'

            ];
        }
        $aTitleLabel['total_label'] = isset($aTitleLabel['label']) ? count($aTitleLabel['label']) : 0;

        return $aTitleLabel;
    }

    private function _getPendingItem($aPhoto, $iAvatarId, $iCover)
    {
        $aPendingItem = [
            'message' => _p('photo_is_pending_approval'),
            'actions' => []
        ];
        if ($aPhoto['canApprove']) {
            $aPendingItem['actions']['approve'] = [
                'is_ajax' => true,
                'label' => _p('approve'),
                'action' => '$.fn.ajaxCall(\'yncphotovp.approve\', \'id=' . $aPhoto['photo_id'] . '\', true, \'POST\', yncphotovp.refresh)'
            ];
        }
        if ($aPhoto['canDelete']) {
            $sDeleteMessage = _p('are_you_sure_you_want_to_delete_this_photo_permanently');
            if ($iAvatarId == $aPhoto['photo_id']) {
                $sDeleteMessage = _p('are_you_sure_you_want_to_delete_this_photo_permanently_this_will_delete_your_current_profile_picture_also');
            } elseif ($iCover == $aPhoto['photo_id']) {
                $sDeleteMessage = _p('are_you_sure_you_want_to_delete_this_photo_permanently_this_will_delete_your_current_cover_photo_also');
            }
            $aPendingItem['actions']['delete'] = [
                'is_ajax' => true,
                'label' => _p('delete'),
                'action' => '$Core.jsConfirm({message: \'' . $sDeleteMessage . '\'}, function () {$.ajaxCall(\'photo.deletePhoto\', \'id=' . $aPhoto['photo_id'] . '&is_detail=1\');}, function(){})'
            ];
        }

        return $aPendingItem;
    }

    private function _getNextPrePhotos($sId, $aPhotos)
    {
        $nextPhoto = $previousPhoto = array();

        foreach ($aPhotos as $index => $aPhoto) {
            if ($aPhoto['photo_id'] == $sId) {
                if (isset($aPhotos[$index + 1])) {
                    $nextPhoto = $aPhotos[$index + 1];
                }
                if (isset($aPhotos[$index - 1])) {
                    $previousPhoto = $aPhotos[$index - 1];
                }
                break;
            }
        }

        return array($previousPhoto, $nextPhoto);
    }
}