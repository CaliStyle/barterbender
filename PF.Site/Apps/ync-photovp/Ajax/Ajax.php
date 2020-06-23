<?php
/**
 * User: YouNetCo
 * Date: 5/10/18
 * Time: 11:17 AM
 */

namespace Apps\YNC_PhotoViewPop\Ajax;

use Phpfox;
use Phpfox_Ajax;

class Ajax extends Phpfox_Ajax
{
    public function view()
    {
        Phpfox::getComponent('yncphotovp.view', array(), 'controller');
    }

    public function rotate()
    {
        Phpfox::isUser(true);
        if ($aPhoto = Phpfox::getService('photo.process')->rotate($this->get('photo_id'), $this->get('photo_cmd'))) {
            Phpfox::getService('photo.tag.process')->deleteAll($this->get('photo_id'));
            $this->call('yncphotovp.refresh();');
        }
    }

    public function updatePhoto()
    {
        $aPostVals = $this->get('val');
        $aVals = $aPostVals[$this->get('photo_id')];
        $aVals['set_album_cover'] = (isset($aPostVals['set_album_cover']) ? $aPostVals['set_album_cover'] : null);
        if (!isset($aVals['privacy']) && isset($aPostVals['privacy'])) {
            $aVals['privacy'] = $aPostVals['privacy'];
        } else {
            $aVals['privacy'] = (isset($aVals['privacy']) ? $aVals['privacy'] : 0);
        }
        $aVals['privacy_comment'] = 0;
        if (($iUserId = Phpfox::getService('user.auth')->hasAccess('photo', 'photo_id', $aVals['photo_id'],
                'photo.can_edit_own_photo',
                'photo.can_edit_other_photo')) && Phpfox::getService('photo.process')->update($iUserId,
                $aVals['photo_id'], $aVals)
        ) {
            $this->call('yncphotovp.refresh();');
//            $aPhoto = Phpfox::getService('photo')->getForEdit($aVals['photo_id']);
//            $newUrl = Phpfox::getLib('url')->permalink('photo', $aPhoto['photo_id'],
//                Phpfox::getLib('parse.input')->clean($aVals['title']));
        }
    }

    public function approve()
    {
        Phpfox::isUser(true);
        Phpfox::getUserParam('photo.can_approve_photos', true);
        if (Phpfox::getService('photo.process')->approve($this->get('id'))) {
            $this->call('yncphotovp.refresh();');
            $this->alert(_p('photo_has_been_approved'), _p('photo_approved'), 300, 100, true);
        }
    }

    public function feature()
    {
        Phpfox::isUser(true);
        Phpfox::getUserParam('photo.can_feature_photo', true);
        if (Phpfox::getService('photo.process')->feature($this->get('photo_id'), $this->get('type'))) {
            $this->call('yncphotovp.refresh();');
            $this->alert(($this->get('type') == '1' ? _p('photo_successfully_featured') : _p('photo_successfully_un_featured')),
                null, 300, 150, true);
        }
    }

    public function sponsor()
    {
        if (!Phpfox::isModule('ad')) {
            return $this->alert('your_request_is_invalid');
        }
        $iPhotoId = $this->get('photo_id');
        // 0 = remove sponsor; 1 = add sponsor
        if (Phpfox::getService('photo.process')->sponsor($iPhotoId, $this->get('type'))) {
            $aPhoto = Phpfox::getService('photo')->getForEdit($iPhotoId);
            if ($this->get('type') == '1') {
                $sModule = _p('photo');
                Phpfox::getService('ad.process')->addSponsor(array(
                    'module' => 'photo',
                    'item_id' => $this->get('photo_id'),
                    'name' => _p('default_campaign_custom_name', ['module' => $sModule, 'name' => $aPhoto['title']])
                ));
                // image was sponsored
            } else {
                Phpfox::getService('ad.process')->deleteAdminSponsor('photo', $this->get('photo_id'));
            }
            $this->call('yncphotovp.refresh();');
            $this->alert($this->get('type') == '1' ? _p('photo_successfully_sponsored') : _p('photo_successfully_un_sponsored'),
                null, 300, 150, true);
        }
    }

    public function removeSponsor()
    {
        if (Phpfox::isModule('feed') && (Phpfox::getUserParam('feed.can_purchase_sponsor') || Phpfox::getUserParam('feed.can_sponsor_feed')) && ($iSponsorId = Phpfox::getService('feed')->canSponsoredInFeed($this->get('type_id'),
                $this->get('item_id')))) {
            if ($iSponsorId === true) {
                $this->alert(_p('Cannot find the feed!'), null, 300, 150, true);
                return;
            }
            if (Phpfox::getService('ad.process')->deleteSponsor($iSponsorId, true)) {
                $this->call('yncphotovp.refresh();');
                $this->alert(_p('This item in feed has been unsponsored successfully!'),
                    null, 300, 150, true);
            } else {
                $this->alert(_p('Cannot unsponsor this item in feed!'),
                    null, 300, 150, true);
                return;
            }

        } else {
            $this->alert(_p('Cannot unsponsor this item in feed!'));
            return;
        }
    }
}