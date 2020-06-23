<?php

namespace Apps\YNC_Feed\Block;

use Phpfox;
use Phpfox_Component;

defined('PHPFOX') or exit('NO DICE!');

class EditUserStatus extends Phpfox_Component
{
    public function process()
    {
        $iFeedId = $this->request()->get('id');
        $aFeedCallback = [];
        $iUserId = $this->request()->get('user_id', null);
        if (($module = $this->request()->get('module')) && ($module != 'link')) {
            $aFeedCallback = [
                'module' => $this->request()->get('module'),
                'table_prefix' => $this->request()->get('module') . '_',
                'item_id' => $this->request()->get('item_id')
            ];
            if ($aFeedCallback['module'] == 'groups') {
                $aFeedCallback['table_prefix'] = 'pages_';
            }
            if ($aFeedCallback['module'] == 'ynsocialstore') {
                $aFeedCallback['table_prefix'] = 'ynstore_';
            }
        }

        $aFeed = Phpfox::getService('ynfeed')->callback($aFeedCallback)->get(null, $iFeedId);
        if (!$aFeed) {
            return null;
        }
        $aFeed = $aFeed[0];

        $canEditStatus = false;
        $editedPhotos = [];

        switch ($aFeed['type_id']) {
            case 'user_status': {
                if((Phpfox::getUserParam('feed.can_edit_own_user_status') && $aFeed['user_id'] == Phpfox::getUserId()) || Phpfox::getUserParam('feed.can_edit_other_user_status')) {
                    $canEditStatus = true;
                }
                break;
            }
            case 'feed_comment': {
                if($aFeed['user_id'] == Phpfox::getUserId() || Phpfox::isAdmin()) {
                    $canEditStatus = true;
                }
                break;
            }
            case 'pages_comment':
            case 'groups_comment': {
                if($aFeed['parent_user_id'] != 0) {
                    if($aFeed['type_id'] == 'pages_comment'){
                        $canEditStatus = ($aFeed['user_id'] == Phpfox::getUserId() || Phpfox::getService('pages')->isAdmin($aFeed['parent_user_id']));
                    }
                    else {
                        $canEditStatus = ($aFeed['user_id'] == Phpfox::getUserId() || Phpfox::getService('groups')->isAdmin($aFeed['parent_user_id']));
                    }
                }
                    break;
            }
            case 'event_comment': {
                if($aFeed['user_id'] == Phpfox::getUserId()) {
                    $canEditStatus = true;
                }
                break;
            }
            case 'link':
            case 'v':
            case 'photo': {
                $module = '';
                $itemId = '';
                $feedCallback = $aFeedCallback;
                $feedType = $aFeed['type_id'];
                switch ($feedType) {
                    case 'link': {
                        $item = Phpfox::getService('link')->getLinkById($aFeed['item_id']);
                        break;
                    }
                    case 'photo': {
                        $item = Phpfox::getService('photo')->getPhotoItem($aFeed['item_id']);
                        $editedPhotos = [$item];

                        break;
                    }
                    case 'v': {
                        $item = Phpfox::getService('v.video')->getForEdit($aFeed['item_id']);
                        break;
                    }
                }

                if(!empty($feedCallback['module']) && !empty($feedCallback['item_id'])) {
                    $module = $feedCallback['module'];
                    $itemId = $feedCallback['item_id'];
                    if(in_array($module, ['pages', 'groups']) && !empty($item) && ($item['module_id'] == $module)) {
                        $appId = $module == 'pages' ? 'Core_Pages' : 'PHPfox_Groups';
                        if(Phpfox::isAppActive($appId)) {
                            $isAdmin = Phpfox::getService($module)->isAdmin($aFeed['parent_user_id']);
                            $canEditStatus = ($aFeed['user_id'] == Phpfox::getUserId()) || $isAdmin;
                        }
                    } else {
                        $canEditStatus = true;
                    }
                } else {
                    if((in_array($feedType, ['link', 'photo'])  && empty($item['module_id'])) || ($feedType == 'v' && in_array($item['module_id'], ['user', 'video']))) {
                        $canEditStatus = true;
                    }
                }
                break;
            }
        }

        //Check have permission to edit user status
        if (!$canEditStatus) {
            return null;
        }
        else {
            if($aFeed['type_id'] == 'photo') {
                $restPhotos = Phpfox::getService('ynfeed')->getPhotosForEditStatus($iFeedId, $aFeedCallback['module']);
                if(!empty($restPhotos)) {
                    $editedPhotos = array_merge($restPhotos, $editedPhotos);
                    usort($editedPhotos, function($a, $b) {
                        return $a['photo_id'] > $b['photo_id'] ? 1 : -1;
                    });
                }
                foreach($editedPhotos as $key => $editedPhoto) {
                    $editedPhotos[$key]['url'] = Phpfox::getParam('photo.url_photo') . sprintf($editedPhoto['destination'], '_500');
                }
                $this->template()->assign([
                    'editedFeedPhotos' => !empty($editedPhotos) ? $editedPhotos : '',
                    'editedFeedPhotosJson' => json_encode($editedPhotos),
                    'isEditedFeedPhoto' => true,
                ]);
            }
        }

        $bLoadCheckIn = false;
        
        $aEmojis = Phpfox::getService('ynfeed.emoticon')->getAll();
        $corePath = Phpfox::getParam('core.path_actual') . 'PF.Site/Apps/ync-feed';
        if (in_array($aFeed['type_id'], ['ultimatevideo_video'])) {
            $aFeed['feed_status'] = $aFeed['feed_content'];
        }
        if (isset($aFeed['feed_status']) && $aFeed['feed_status'] != '') {
            preg_match_all('/(?<match>\[(?<type>[\w]+)=(?<id>[\d]+)\](?<name>[\p{L}\p{P}\p{S}\p{N}\s]+)\[\/([\w]+)\])/Umu', $aFeed['feed_status'], $matches);
            if(isset($matches['match'])) {
                foreach($matches['match'] as $key=>$match) {
                    if(isset($matches['type'][$key]) && isset($matches['id'][$key]) && isset($matches['name'][$key])) {
                        $aFeed['feed_status'] = str_replace($match, sprintf('<span id="generated" class="generatedMention" contenteditable="false" data-type="%s" data-id="%d">%s</span>', $matches['type'][$key], $matches['id'][$key], $matches['name'][$key]), $aFeed['feed_status']);
                    }
                }
            }
        }
        $taggedUserIds = $aFeed['tagged'];
        if (!empty($taggedUserIds)) {
            $aTaggedUsers = Phpfox::getService('ynfeed.user.process')->getUsersById(explode(',', $taggedUserIds));
            foreach ($aTaggedUsers as $key => $aTaggedUser) {
                $aTaggedUsers[$key] = Phpfox::getService('ynfeed.user.process')->addMoreInfo($aTaggedUser);
            }
            echo '<script>
                $Cache.users = ' . json_encode($aTaggedUsers) . '
            </script>';
        }

        $this->template()->assign([
            'iFeedId' => $iFeedId,
            'bLoadCheckIn' => $bLoadCheckIn,
            'aForms' => $aFeed,
            'aEmojis' => $aEmojis,
            'corePath' => $corePath,
            'bLoadBusiness' => Phpfox::isModule('directory'),
            'iUserId' => $iUserId,
            'aFeedCallback' => $aFeedCallback,
        ]);
        return null;
    }
}