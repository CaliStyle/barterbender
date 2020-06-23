<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class Profile_Component_Block_Pic
 */
class Profile_Component_Block_Pic extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {

        if (!defined('PHPFOX_IS_USER_PROFILE') && !defined('PAGE_TIME_LINE')) {
            return false;
        }

        (($sPlugin = Phpfox_Plugin::get('profile.component_block_pic_start')) ? eval($sPlugin) : false);

        if (isset($bHideThisBlock)) {
            return false;
        }

        $aUser = $this->getParam('aUser');

        if ($aUser === null) {
            $aUser = $this->getParam('aPage');
            $aUser['user_image'] = $aUser['image_path'];
            foreach ($aUser as $sKey => $sValue) {
                if (strpos($sKey, 'owner_') !== false && $sKey != 'owner_user_image') {
                    $aUser[str_replace('owner_', '', $sKey)] = $sValue;
                }
            }
        }

        if (defined('PHPFOX_IS_PAGES_VIEW')) {
            $aUser['user_id'] = $aUser['page_user_id'];
            $aUser['server_id'] = $aUser['image_server_id'];
            $aUser['full_name'] = $aUser['title'];
            $aUser['user_name'] = !empty($aUser['vanity_url']) ? $aUser['vanity_url'] : $aUser['title'];
            $aUser['user_group_id'] = 2;

            $this->template()->assign([
                    'aUser' => $aUser
                ]
            );
        }

        $aUserInfo = [
            'title' => $aUser['full_name'],
            'path' => 'core.url_user',
            'file' => $aUser['user_image'],
            'suffix' => '_200_square',
            'max_width' => 200,
            'no_default' => (Phpfox::getUserId() == $aUser['user_id'] ? false : true),
            'thickbox' => true,
            'class' => 'profile_user_image',
            'no_link' => true
        ];

        (($sPlugin = Phpfox_Plugin::get('profile.component_block_pic_process')) ? eval($sPlugin) : false);

        $sImage = Phpfox::getLib('image.helper')->display(array_merge([
            'user' => Phpfox::getService('user')->getUserFields(true, $aUser)
        ], $aUserInfo));
        if ($oAvatar = storage()->get('user/avatar/' . $aUser['user_id'])) {
            $aProfileImage = Phpfox::getService('photo')->getPhoto($oAvatar->value);
        }

        if ($aUser['user_image']) {
            $sPhotoUrl = Phpfox::getLib('image.helper')->display([
                'server_id' => Phpfox::getUserBy('server_id'),
                'title' => Phpfox::getUserBy('full_name'),
                'path' => 'core.url_user',
                'file' => $aUser['user_image'],
                'suffix' => '',
                'no_default' => true,
                'return_url' => true,
            ]);
        }

        $this->template()->assign([
                'sProfileImage' => $sImage,
                'sPhotoUrl' => isset($sPhotoUrl) ? $sPhotoUrl : '',
                'aProfileImage' => isset($aProfileImage) ? $aProfileImage : false,
                'iServerId' => Phpfox::getUserBy('server_id')
            ]
        );

        $bCanSendPoke = Phpfox::isAppActive('Core_Poke') && PhpFox::getService('poke')->canSendPoke($aUser['user_id']);
        $aCoverPhoto = Phpfox::getService('photo')->getCoverPhoto($aUser['cover_photo']);

        $aUser['bRelationshipHeader'] = true;
        $aUser['is_header'] = true;
        $sRelationship = Phpfox::getService('custom')->getRelationshipPhrase($aUser);

        $this->template()->assign([
                'bCanPoke' => $bCanSendPoke,
                'aCoverPhoto' => $aCoverPhoto,
                'aUser' => $aUser,
                'iCoverPhotoPosition' => intval($aUser['cover_photo_top']),
                'sCoverDefaultUrl' => flavor()->active->default_photo('user_cover_default', true),
                'sRelationship' => trim($sRelationship),
                'sModule' => $this->request()->get('req2', '')
            ]
        );
        return null;
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('profile.component_block_pic_clean')) ? eval($sPlugin) : false);
    }
}
