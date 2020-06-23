<?php

defined('PHPFOX') or exit('NO DICE!');


class Directory_Component_Controller_Detail extends Phpfox_Component
{
    public function process()
    {
        // Check view permission
        Phpfox::getService('directory.permission')->canViewBusiness(true);

        // get callback for pages here
        $aCallback = $this->getParam('aCallback', false);
        $iViewerId = Phpfox::getUserId();

        // Check if form invite is submit here
        if ($this->request()->getInt('id')) {
            return Phpfox::getLib('module')->setController('error.404');
        }


        // Get related business
        $iBusinessId = $this->request()->getInt('req3');

        $aBusiness = Phpfox::getService('directory')->callback($aCallback)->getBusinessById($iBusinessId);
        if (empty($aBusiness['logo_path'])) {
            $aBusiness['default_logo_path'] = Phpfox::getService('directory')->getStaticPath() . 'module/directory/static/image/default_ava.png';
        }
        /*check status of business*/
        Phpfox::getService('directory')->checkAndUpdateStatus($aBusiness);

        if (!$aBusiness) {
            Phpfox::getLib('url')->send('directory', null, _p('directory.business_not_found'));
        }

        // Begin invite friend after get this detail campaign
        if ($this->request()->getArray('val')) {
            $aVals = $this->request()->getArray('val');
            $aVals['invite'] = $this->request()->getArray('friend');
        }
        if (isset($aVals['submit_invite'])) {
            Phpfox::getService('directory.process')->inviteFriends($aVals, $aBusiness);
        }

        $sView = $this->request()->get('req5');
        $this->setParam('sView', $sView);
        $this->template()->assign(array(
            'sView' => $sView,
        ));

        if (Phpfox::isModule('privacy')) {
            Phpfox::getService('privacy')->check('directory', $aBusiness['business_id'], $aBusiness['user_id'], $aBusiness['privacy'], $aBusiness['is_friend']);
        }

        if (Phpfox::isModule('pages')) {
            if ($aBusiness['module_id'] == 'pages' && !Phpfox::getService('pages')->hasPerm($aBusiness['item_id'],
                    'directory.view_browse_business')) {
                return Phpfox_Error::display(_p('directory.unable_to_view_this_item_due_to_privacy_settings'));
            }
        }

        if ($aBusiness['user_id'] != $iViewerId) {
            Phpfox::getService('directory.process')->updateTotalView($aBusiness['business_id']);
        }
        $bCanEdit = Phpfox::getService('directory.permission')->canEditBusiness($aBusiness['user_id'], $aBusiness['business_id']);
        $bCanDelete = Phpfox::getService('directory.permission')->canDeleteBusiness($aBusiness['user_id']);
        $aBusiness['canManageDashBoard'] = (($aBusiness['type'] != 'claiming') || ($aBusiness['type'] == 'claiming' && Phpfox::getService('directory.helper')->getConst('business.status.draft') != $aBusiness['business_status'])) && Phpfox::getService('directory.permission')->canManageBusinessDashBoard($aBusiness['business_id']);
        $aBusiness['canTransferOwner'] = Phpfox::getUserId() == $aBusiness['user_id'];
        $aBusiness['isClaimingDraft'] = ($aBusiness['type'] == 'claiming' && Phpfox::getService('directory.helper')->getConst('business.status.claimingdraft') == $aBusiness['business_status']) ? 1 : 0;
        $aBusiness['isDraft'] = ($aBusiness['type'] != 'claiming' && Phpfox::getService('directory.helper')->getConst('business.status.draft') == $aBusiness['business_status']) ? 1 : 0;
        if ($aBusiness['business_status'] == Phpfox::getService('directory.helper')->getConst('business.status.running') || $aBusiness['business_status'] == Phpfox::getService('directory.helper')->getConst('business.status.approved')) {
            $aBusiness['canCloseBusiness'] = 1;
        } else {
            $aBusiness['canCloseBusiness'] = 0;
        }
        $aBusiness['canOpenBusiness'] = Phpfox::getService('directory.helper')->getConst('business.status.closed') == $aBusiness['business_status'] ? 1 : 0;
        $aRoleOfViewer = Phpfox::getService('directory')->getRoleByUserIdWithGuest(Phpfox::getUserId(), $aBusiness['business_id']);
        // Draft/expire view permission
        switch ($aBusiness['business_status']) {
            case Phpfox::getService('directory.helper')->getConst('business.status.draft'):
                if ($aBusiness['user_id'] != $iViewerId) {
                    if (($aBusiness['type'] != 'claiming' || $aBusiness['business_status'] != Phpfox::getService('directory.helper')->getConst('business.status.draft'))) {
                        $this->url()->send('subscribe');
                    }
                }
                break;

            case Phpfox::getService('directory.helper')->getConst('business.status.completed'):
                if ($aBusiness['user_id'] == $iViewerId
                    || Phpfox::isAdmin()
                    || $aBusiness['canManageDashBoard']
                ) {
                } else {
                    $this->url()->send('subscribe');
                }
                break;
            case Phpfox::getService('directory.helper')->getConst('business.status.pending'):
                if ($aBusiness['user_id'] != $iViewerId) {
                    $this->url()->send('subscribe');
                }
                break;

        }

        $bCanPostComment = true;
        if (isset($aBusiness['privacy_comment']) && $aBusiness['user_id'] != Phpfox::getUserId() && !Phpfox::getUserParam('privacy.can_comment_on_all_items')) {
            switch ($aBusiness['privacy_comment']) {
                // Everyone is case 0. Skipped.
                // Friends only
                case 1:
                    if (!Phpfox::getService('friend')->isFriend(Phpfox::getUserId(), $aBusiness['user_id'])) {
                        $bCanPostComment = false;
                    }
                    break;
                // Friend of friends
                case 2:
                    if (!Phpfox::getService('friend')->isFriendOfFriend($aBusiness['user_id'])) {
                        $bCanPostComment = false;
                    }
                    break;
                // Only me
                case 3:
                    $bCanPostComment = false;
                    break;
            }
        }

        if (Phpfox::getUserId()) {
            $bIsBlocked = Phpfox::getService('user.block')->isBlocked($aBusiness['user_id'], Phpfox::getUserId());
            if ($bIsBlocked) {
                $bCanPostComment = false;
            }
        }

        $sLink = Phpfox::permalink('directory.detail', $aBusiness['business_id'], $aBusiness['name']);

        $this->setParam('aFeedCallback', array(
                'module' => 'directory',
                'table_prefix' => 'directory_',
                'ajax_request' => 'directory.addFeedComment',
                'item_id' => $aBusiness['business_id'],
                'disable_share' => ($bCanPostComment ? false : true)
            )
        );

        // Set title, meta, param, breadcrumb, header and variables
        // $this->setParam('aBusiness', $aBusiness);

        // check and redirect controller
        if ($aBusiness['type'] == 'claiming'
            || ($aBusiness['type'] == 'business' && $aBusiness['business_status'] == Phpfox::getService('directory.helper')->getConst('business.status.pendingclaiming'))
            || ($aBusiness['type'] == 'business' && $aBusiness['business_status'] == Phpfox::getService('directory.helper')->getConst('business.status.draft'))
        ) {
            if ($aBusiness['type'] == 'claiming') {
                Phpfox::getService('directory.permission')->canClaimBusiness(true);
            }
            $req5 = $this->request()->get('req5');
            $sCheckParam = $req5;
            if (in_array($sCheckParam, array('overview', 'aboutus'))) {
                $firstpage = $sCheckParam;
            } else {
                $firstpage = 'overview';
            }
        } else {
            $req5 = $this->request()->get('req5');
            $req5 = Phpfox::getService('directory')->changePageWhenAccessingBusinessDetail($req5);
            $sCheckParam = $req5;
            list($listPageMenu, $keyLandingPage) = Phpfox::getService('directory')->getMenuListCanAccessInBusinessDetail($aBusiness['business_id'], $aBusiness);
            $firstpage = null;

            /*check have pararm from url*/
            foreach ($listPageMenu as $key => $value) {
                if ($sCheckParam == $value) {
                    $firstpage = $value;
                    break;
                }
            }

            /*if don't have param,find landing page*/
            if (null == $firstpage) {
                if (count($listPageMenu) > 0) {

                    if (isset($listPageMenu[$keyLandingPage])) {
                        /*chooose landing page if have*/
                        $firstpage = $listPageMenu[$keyLandingPage];
                    } else {
                        /*just choose first page if don't have*/
                        foreach ($listPageMenu as $key => $value) {
                            $firstpage = $value;
                            break;
                        }
                    }
                } else {
                    $this->url()->send('directory', null, null);
                }
            }
        }
        Phpfox::getService('directory.helper')->removeSessionAddNewItemOfUser();

        // process if submit in block of detail controller
        if ($aVals = $this->request()->getArray('val')) {
            if (isset($aVals['yndirectory_detail_submit_sign'])) {
                switch ($aVals['yndirectory_detail_submit_sign']) {
                    case 'contactus':
                        $this->processContactus($aVals);
                        break;
                }
            }
        }

        $this->template()->setTitle($aBusiness['name']);
        if (!empty($aBusiness['module_id']) && $aBusiness['module_id'] != 'directory') {
            if (Phpfox::hasCallback($aBusiness['module_id'], 'getBusinessDetail')) {
                $aCallback = Phpfox::callback($aBusiness['module_id'] . '.getBusinessDetail', $aBusiness);
            } elseif (Phpfox::isModule('pages')) {
                $aCallback = $this->getBusinessDetail($aBusiness);

            }
            $this->template()
                ->setBreadcrumb($aCallback['breadcrumb_title'], $aCallback['breadcrumb_home'])
                ->setBreadcrumb($aCallback['title'], $aCallback['url_home']);
            if (Phpfox::isModule('pages')) {
                if (($aBusiness['module_id'] == 'pages' && !Phpfox::getService('pages')->hasPerm($aCallback['item_id'],
                        ''))) {
                    return \Phpfox_Error::display(_p('Unable to view this item due to privacy settings'));
                }
            }
            if ($aBusiness['module_id'] == 'groups' && !Phpfox::getService('groups')->hasPerm($aCallback['item_id'],
                    '')) {
                return \Phpfox_Error::display(_p('Unable to view this item due to privacy settings'));
            }

        } else {
            $this->template()->setBreadCrumb(_p('directory.business_directory'), $aBusiness['module_id'] == 'directory' ? $this->url()->makeUrl('directory') : $this->url()->permalink('pages', $aBusiness['item_id'], 'directory'))
                ->setBreadCrumb($aBusiness['name'], $this->url()->permalink('directory.detail', $aBusiness['business_id'], $aBusiness['name']));
        }

        if (!empty($aBusiness['logo_path'])) {
            $aImage = Phpfox::getLib('image.helper')->display(array(
                'server_id' => $aBusiness['server_id'],
                'path' => 'core.url_pic',
                'file' => $aBusiness['logo_path'],
                'suffix' => '_200_square',
                'return_url' => true
            ));
        } else {
            $aImage = Phpfox::getLib('image.helper')->display(array(
                'server_id' => $aBusiness['server_id'],
                'path' => '',
                'file' => Phpfox::getService('directory')->getStaticPath() . 'module/directory/static/image/default_ava.png',
                'suffix' => '_200_square',
                'return_url' => true
            ));
        }

        $this->template()->setMeta('description', $aBusiness['name'] . '.')
            ->setMeta('description', $aBusiness['short_description'] . '.')
            ->setMeta('keywords', $this->template()->getKeywords($aBusiness['name']))
            ->setMeta('og:url', $sLink);

        $this->template()->setEditor(array(
                'load' => 'simple'
            )
        )->setHeader('cache', array(
                'quick_edit.js' => 'static_script',
                'switch_menu.js' => 'static_script',
                'jquery/plugin/jquery.highlightFade.js' => 'static_script',
                'jquery/plugin/jquery.scrollTo.js' => 'static_script',
                'pager.css' => 'style_css',
                'feed.js' => 'module_feed',
                'jquery.rating.css' => 'style_css',
            )
        );


        $this->template()->setPhrase(
            array(
                'directory.manage_business',
                'directory.transfer_owner',
                'directory.actions',
                'directory.like',
                'directory.unlike',
                'directory.change_role',
                'directory.rating',
                'directory.rate_this_business',
                'directory.delete_role',
                'directory.are_you_sure_you_want_to_delete_this_member_of_business',
                'directory.download_pdf',
                'directory.print',
                'directory.close_business',
                'directory.open_business',
                'directory.make_payment',
            )
        );

        Phpfox::getService('directory.helper')->loadDirectoryJsCss();

        $sViewPage = (int)$this->request()->get('custompage');

        // get data for menu (which is as same as dashboard)
        $aModules = Phpfox::getService('directory')->getPageModuleForManage($aBusiness['business_id']);
        $aModuleView = array();
        $IsModuleActive = false;

        foreach ($aModules[0] as $iModuleId => $aModule) {
            $aItem = Phpfox::getService('directory')->getPageByBusinessModuleId($aBusiness['business_id'], $iModuleId);
            if (isset($aItem['module_name'])) {
                $aModuleView[$aItem['module_name']] = $aItem;

                $sTitle = $aBusiness['name'];
                if (!empty($sTitle)) {
                    if (preg_match('/\{phrase var\=(.*)\}/i', $sTitle, $aMatches) && isset($aMatches[1])) {
                        $sTitle = str_replace(array("'", '"', '&#039;'), '', $aMatches[1]);
                        $sTitle = _p($sTitle);
                    }

                    $sTitle = Phpfox::getLib('url')->cleanTitle($sTitle);
                }

                $aModuleView[$aItem['module_name']]['link'] = Phpfox::getLib('url')->makeUrl('directory.detail' . '.' . $aBusiness['business_id'] . '.' . $sTitle . '.' . $aItem['module_name']);

                $aModuleView[$aItem['module_name']]['active'] = false;
                if ($sView == '' && $sViewPage == 0 && $aModuleView[$aItem['module_name']]['module_landing']) {
                    $aModuleView[$aItem['module_name']]['active'] = true;
                    $IsModuleActive = true;
                } else
                    if ($sView == $aItem['module_name'] || ($sView == 'advanced-blog' && $aItem['module_name'] == 'ynblog')) {
                        $aModuleView[$aItem['module_name']]['active'] = true;
                        $IsModuleActive = true;
                    }
            }
        }

        $aPagesModule = Phpfox::getService('directory')->getModuleInBusiness($aBusiness['business_id']);
        $shownPages = array();

        foreach ($aPagesModule as $key_page => $aPage) {
            if ($aPagesModule[$key_page]['module_type'] == 'contentpage' && $aPagesModule[$key_page]['is_show']) {
                $sTitle = $aBusiness['name'];
                if (!empty($sTitle)) {
                    if (preg_match('/\{phrase var\=(.*)\}/i', $sTitle, $aMatches) && isset($aMatches[1])) {
                        $sTitle = str_replace(array("'", '"', '&#039;'), '', $aMatches[1]);
                        $sTitle = _p($sTitle);
                    }
                    $sTitle = Phpfox::getLib('url')->cleanTitle($sTitle);
                }

                $aPagesModule[$key_page]['link'] = Phpfox::getLib('url')->makeUrl('directory.detail.' . $aBusiness['business_id'] . '.' . $sTitle . '.custompage_' . $aPage['data_id']);

                $aPagesModule[$key_page]['active'] = false;
                if ($sViewPage == 0 && $sView == '' && !$IsModuleActive && $aPagesModule[$key_page]['module_landing']) {
                    $aPagesModule[$key_page]['active'] = true;
                } else if ($sViewPage == $aPagesModule[$key_page]['data_id'] && !$IsModuleActive) {
                    $aPagesModule[$key_page]['active'] = true;
                }
                $shownPages[] = $aPagesModule[$key_page]['data_id'];
            } else {
                unset($aPagesModule[$key_page]);
            }
        }

        /*for custom page from dashboard*/
        /*get param from url*/
        if ($sViewPage && in_array($sViewPage, $shownPages)) {

            $this->setParam('iCustomPage', $sViewPage);
            $this->template()->assign(array(
                'iCustomPage' => $sViewPage,
            ));
        } else {
            /*if have landing,go to custompage*/
            if (count($aPagesModule)) {
                foreach ($aPagesModule as $key_page => $aPage) {
                    if ($aPage['active']) {
                        $iLandingContentPage = $aPage['data_id'];
                        $this->setParam('iCustomPage', $iLandingContentPage);
                        $this->template()->assign(array(
                            'iCustomPage' => $iLandingContentPage,
                        ));
                        break;
                    }
                }
            }
        }

        // Get Image
        if (!empty($aBusiness['logo_path'])) {
            $aBusiness['image'] = Phpfox::getLib('image.helper')->display(array(
                'server_id' => $aBusiness['server_id'],
                'path' => 'core.url_pic',
                'file' => $aBusiness['logo_path'],
                'suffix' => '_400',
                'return_url' => true
            ));
            $size_img = @getimagesize($aBusiness['image']);
            if (!empty($size_img)) {
                $og_image_width = $size_img[0];
                $og_image_height = $size_img[1];
            } else {
                $og_image_width = 640;
                $og_image_height = 442;
            }
            $this->template()
                ->setMeta('og:image', $aBusiness['image'])
                ->setMeta('og:image:width', $og_image_width)
                ->setMeta('og:image:height', $og_image_height);
        }
        $aBusiness['bookmark_url'] = Phpfox::permalink('directory.detail', $aBusiness['business_id'], $aBusiness['name']);
        $aBusiness['canInviteMember'] = Phpfox::getService('directory.permission')->canInviteMember($aBusiness['business_id']);
        $aBusiness['displayLikeButtonTheme2'] = (Phpfox::isUser()) ? ($aBusiness['theme_id']) : 0;
        $aBusiness['linkBusinessDashBoard'] = Phpfox::getLib('url')->makeUrl('directory.dashboard', array('id' => $aBusiness['business_id']));
        $aBusiness['linkBusiness'] = Phpfox::getLib('url')->makeUrl('directory.detail', array($aBusiness['business_id'], $aBusiness['name']));
        $aBusiness['isLiked'] = Phpfox::getService('like')->didILike('directory', $aBusiness['business_id']);
        $aBusiness['isMember'] = Phpfox::getService('directory')->isMemberOfBusiness($aBusiness['business_id'], Phpfox::getUserId());
        $aYnDirectoryDetail = array(
            'aBusiness' => $aBusiness,
            'bCanDelete' => $bCanDelete,
            'bCanEdit' => $bCanEdit,
            'firstpage' => $firstpage,
            'aModules' => $aModules,
            'aModuleView' => $aModuleView,
            'aPagesModule' => $aPagesModule,
            'sDetailUrl' => $this->url()->permalink('directory.detail', $aBusiness['business_id'], $aBusiness['name']),
            'sDownloadBusinessUrl' => Phpfox::getService('directory')->getStaticPath() . 'module/directory/static/php/download.php?type=business&id=' . $aBusiness['business_id'],
        );

        $aNumberOfItem = array(
            'members' => Phpfox::getService('directory')->getCountMemberOfBusiness($aBusiness['business_id']),
            'followers' => Phpfox::getService('directory')->getCountFollowerOfBusiness($aBusiness['business_id']),
            'reviews' => Phpfox::getService('directory')->getCountReviewOfBusiness($aBusiness['business_id']),
        );

        $aBusiness = $aYnDirectoryDetail['aBusiness'];
        $aBusiness['total_score'] = $aBusiness['total_score'] / 2;

        $isFollowedBusiness = Phpfox::getService('directory')->isFollowBusiness(Phpfox::getUserId(), $aBusiness['business_id']);
        $isFavoriteBusiness = Phpfox::getService('directory')->isFavoriteBusiness(Phpfox::getUserId(), $aBusiness['business_id']);
        $isLiked = Phpfox::getService('like')->didILike('directory', $aBusiness['business_id']);

        $aCoverPhotos = Phpfox::getService('directory')->getImages($aBusiness['business_id']);

        $sPathCoverPhoto = "";
        if (count($aCoverPhotos)) {

            $aBusiness['default_cover'] = false;
            $sPathCoverPhoto = 'yndirectory/' . $aCoverPhotos[0]['image_path'];
        } else {
            $aBusiness['default_cover'] = true;
            $sPathCoverPhoto = Phpfox::getParam('core.path') . 'module/directory/static/image/default_cover.png';
        }

        $aBusiness['cover_photo'] = $sPathCoverPhoto;

        $aBusiness['linkBusiness'] = Phpfox::getLib('url')->makeUrl('directory.detail', array($aBusiness['business_id']));
        $canManageBusiness = $aBusiness['canManageDashBoard'];
        $canInviteMember = $aBusiness['canInviteMember'];

        $bIsNotClaim = true;
        if ($aBusiness['business_status'] == 10 || $aBusiness['business_status'] == 1) {
            $bIsNotClaim = false;
        }

        Phpfox::getService('directory.helper')->buildMenu();
        $this->template()->assign(array(
            'aYnDirectoryDetail' => $aYnDirectoryDetail,
            'aBusiness' => $aBusiness,
            'bIsNotClaim' => $bIsNotClaim,
            'aModuleView' => $aModuleView,
            'aCoverPhotos' => $aCoverPhotos,
            'isFollowedBusiness' => $isFollowedBusiness,
            'isFavoriteBusiness' => $isFavoriteBusiness,
            'canManageBusiness' => $canManageBusiness,
            'canInviteMember' => $canInviteMember,
            'isLiked' => $isLiked,
            'aBusinesLocations' => $aBusiness['locations'],
            'aNumberOfItem' => $aNumberOfItem,
            'apiKey' => Phpfox::getParam('core.google_api_key'),
            'bIsDetail' => true
        ));
        $this->setParam('aYnDirectoryDetail', $aYnDirectoryDetail);
    }

    public function processContactus($aVals = array())
    {
        $aFields = Phpfox::getService('directory.customcontactus.custom')->getCustomFieldByContactUsId($aVals['yndirectory_contactus_contactusid']);
        if (isset($aVals['custom'])) {
            foreach ($aVals['custom'] as $key_custom => $custom_value) {
                foreach ($aFields as &$aFieldChange) {
                    if ($aFieldChange['field_id'] == $key_custom) {
                        if (is_array($custom_value)) {
                            foreach ($custom_value as $key => $value) {
                                $aFieldChange['value'][$value] = $value;
                            }
                        } else {
                            $aFieldChange['value'] = $custom_value;
                        }
                    }
                }
            }
        }
        $this->setParam('aYnDirectoryDetailContactUs', array(
            'aForm' => $aVals,
            'aFields' => $aFields,
        ));

        $warning = _p('directory.please_fill_required_field_s');
        if (strlen(trim($aVals['yndirectory_contactus_full_name'])) == 0) {
            return Phpfox_Error::set($warning);
        }
        if (Phpfox::getLib('mail')->checkEmail($aVals['yndirectory_contactus_email']) == false) {
            return Phpfox_Error::set(_p('directory.please_input_valid_email'));
        }
        if (strlen(trim($aVals['yndirectory_contactus_subject'])) == 0) {
            return Phpfox_Error::set($warning);
        }
        if (strlen(trim($aVals['yndirectory_contactus_message'])) == 0) {
            return Phpfox_Error::set($warning);
        }

        $sCustom = '<br />';
        if (isset($aVals['custom'])) {
            $aFieldValues = $aVals['custom'];

            foreach ($aFields as $k => $aField) {
                if ($aField['is_required'] && (!isset($aFieldValues[$aField['field_id']]) || (isset($aFieldValues[$aField['field_id']]) && empty($aFieldValues[$aField['field_id']])))) {
                    return Phpfox_Error::set($warning);
                } else {
                    if (isset($aFieldValues[$aField['field_id']]) && empty($aFieldValues[$aField['field_id']]) == false) {
                        if (is_array($aFieldValues[$aField['field_id']])) {
                            $sTmp = '';
                            foreach ($aFieldValues[$aField['field_id']] as $key => $value) {
                                $sTmp .= _p($aField['option'][$value]) . ' | ';
                            }
                            $sTmp = rtrim($sTmp, ' | ');
                            $sCustom .= _p($aField['phrase_var_name']) . ': ' . $sTmp . '<br />';
                        } else {
                            $sCustom .= _p($aField['phrase_var_name']) . ': ' . $aFieldValues[$aField['field_id']] . '<br />';
                        }
                    }
                }
            }
        }

        // send email right now (not include in queue)
        $sMessage = $aVals['yndirectory_contactus_message'] . $sCustom;
        $sInform = _p('directory.send_email_successfully');
        if (!Phpfox::getService('directory.mail.phpfoxmail')
            ->fromName($aVals['yndirectory_contactus_full_name'])
            ->fromEmail($aVals['yndirectory_contactus_email'])
            ->to($aVals['yndirectory_contactus_department'])
            ->subject($aVals['yndirectory_contactus_subject'])
            ->message(Phpfox::getLib('parse.input')->prepare($sMessage))
            ->send()) {
            $sInform = _p('directory.there_are_error_s_in_processing_please_try_again');
        }

        $this->setParam('aYnDirectoryDetailContactUs', array(
            'sInform' => $sInform,
        ));
    }

    public function getBusinessDetail($aItem)
    {
        Phpfox::getService('pages')->setIsInPage();

        $aRow = Phpfox::getService($aItem['module_id'])->getPage($aItem['item_id']);

        if (!isset($aRow['page_id'])) {
            return false;
        }

        Phpfox::getService($aItem['module_id'])->setMode();

        $sLink = Phpfox::getService($aItem['module_id'])->getUrl($aRow['page_id'], $aRow['title'], $aRow['vanity_url']);

        return array(
            'breadcrumb_title' => $aItem['module_id'] == 'groups' ? _p('Groups') : _p('pages.pages'),
            'breadcrumb_home' => \Phpfox_Url::instance()->makeUrl('groups'),
            'module_id' => $aItem['module_id'],
            'item_id' => $aRow['page_id'],
            'title' => $aRow['title'],
            'url_home' => $sLink,
            'url_home_photo' => $sLink . 'directory/',
            'theater_mode' => _p('pages.in_the_page_link_title', array('link' => $sLink, 'title' => $aRow['title']))
        );
    }
}

?>