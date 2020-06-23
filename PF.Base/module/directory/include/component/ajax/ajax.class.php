<?php

defined('PHPFOX') or exit('NO DICE!');

class Directory_Component_Ajax_Ajax extends Phpfox_Ajax
{
    public function showAnnouncement()
    {
        $announcement_id = (int) $this->get('announcement_id');
        Phpfox::getBlock('directory.showannouncement', array('announcement_id' => $announcement_id));
        $this->setTitle(_p('announcement_detail'));
    }

    public function featureInBox()
    {
        $iBusinessId = (int) $this->get('iBusinessId');
        Phpfox::getBlock('directory.featureinbox', array('iBusinessId' => $iBusinessId));
        $this->setTitle(_p('business'));
    }

    public function browsecheckinhere()
    {
        $iBusinessId = (int) $this->get('item_id');
        Phpfox::getBlock('directory.browsecheckinhere', array('iBusinessId' => $iBusinessId));
        $this->setTitle(_p('people_who_check_in_here'));
    }

    public function checkinhere()
    {
        $iBusinessId = (int) $this->get('iBusinessId');
        $aBusiness = Phpfox::getService('directory')->getQuickBusinessById($iBusinessId);
        if(isset($aBusiness['business_id'])
            && (
                $aBusiness['business_status'] == (int)Phpfox::getService('directory.helper')->getConst('business.status.completed')
                || $aBusiness['business_status'] == (int)Phpfox::getService('directory.helper')->getConst('business.status.approved')
                || $aBusiness['business_status'] == (int)Phpfox::getService('directory.helper')->getConst('business.status.running')
                )
        ){
            if(Phpfox::isModule('feed')){
                $sFeed = 'directory_checkinhere';
                $iFeedId = Phpfox::getService('feed.process')->add($sFeed, $iBusinessId);
                Phpfox::getService('directory.process')->addCheckinhere($iBusinessId, Phpfox::getUserId());
                $this->alert(_p('check_in_successfully'));
                $this->call('setTimeout(function() {window.location.href = window.location.href;},2000);');
            }
        } else {
            $this->alert(_p('business_is_not_found'));
            $this->call('setTimeout(function() {window.location.href = "' + $this->url()->makeUrl('directory') + '";},2000);');
        }
    
        Phpfox::getLib('database')->updateCount('directory_checkinhere', 'business_id = ' . (int) $iBusinessId . '', 'total_checkin', 'directory_business', 'business_id = ' . (int) $iBusinessId); 

    }

    public function getPromoteBusinessBox()
    {
        $iBusinessId = (int) $this->get('iBusinessId');
        
        $this->setTitle(_p('promote_business'));
        Phpfox::getBlock('directory.promotebusiness', array('iBusinessId' => $iBusinessId));
    }

    public function transferownerBusiness(){
        $iBusinessId = (int)$this->get('iBusinessId');
        $iUserId = (int)$this->get('iUserId');

        if ($iBusinessId && $iUserId)
        {
            Phpfox::getService('directory.process')->updateOwner($iBusinessId, $iUserId);
        }
        $this->call("window.location = window.location;");
    }

    public function getUserForTransferOwner(){
        $sWhere = 'u.profile_page_id = 0 AND u.user_id != ' . Phpfox::getUserId();

        $aRows = Phpfox::getLib("database")->select('u.*, ' . Phpfox::getUserField())
            ->from(Phpfox::getT('user'), 'u', 'u.user_id = ')
            ->where($sWhere)
            ->limit(Phpfox::getParam('friend.friend_cache_limit'))
            ->order('u.last_activity DESC')
            ->execute('getSlaveRows');

        foreach ($aRows as $iKey => $aRow)
        {       
            if (Phpfox::getUserId() == $aRow['user_id'])
            {
                unset($aRows[$iKey]);
                
                continue;
            }
            
            $aRows[$iKey]['full_name'] = html_entity_decode(Phpfox::getLib('parse.output')->split($aRow['full_name'], 20), null, 'UTF-8');                      
            $aRows[$iKey]['user_profile'] = ($aRow['profile_page_id'] ? Phpfox::getService('pages')->getUrl($aRow['profile_page_id'], '', $aRow['user_name']) : Phpfox::getLib('url')->makeUrl($aRow['user_name']));
            $aRows[$iKey]['is_page'] = ($aRow['profile_page_id'] ? true : false);
            $aRows[$iKey]['user_image'] = Phpfox::getLib('image.helper')->display(array(
                    'user' => $aRow,
                    'suffix' => '_50_square',
                    'max_height' => 32,
                    'max_width' => 32,
                    'return_url' => true
                )
            );
        }       
        $this->call('$Cache.friends = ' . json_encode($aRows) . ';');
        $this->call("$('input[id^=\"search_input_name\"')[0].value = ''");        
    }

    public function openTransferownerBusiness(){
        $iBusinessId = (int)$this->get('iBusinessId');
        $frontend = (int)$this->get('frontend');
        Phpfox::getBlock('directory.transferowner', array('iBusinessId' => $iBusinessId, 'frontend' => $frontend));
    }

    public function approveBusiness(){
        // Get Params
        $iBusinessId = (int)$this->get('iBusinessId');
        Phpfox::getUserParam('admincp.has_admin_access', true);
        if ($iBusinessId)
        {
            $status = Phpfox::getService('directory.helper')->getConst('business.status.approved');
            Phpfox::getService('directory.process')->updateBusinessStatus($iBusinessId, $status);
            Phpfox::getService('directory.process')->approveBusiness($iBusinessId, null);

            // send notification to owner 
            $aBusiness = Phpfox::getService('directory')->getQuickBusinessById($iBusinessId);
            Phpfox::getService('notification.process')->add('directory_approve_business', $iBusinessId, $aBusiness['user_id'], Phpfox::getUserId());

            // send email to owner
            $aUser = Phpfox::getService('user')->getUser($aBusiness['user_id']);
            $language_id = $aUser['language_id'] == null ? 'en' : $aUser['language_id'];
            $email = $aUser['email'];
            $aEmail = Phpfox::getService('directory.mail')->getEmailMessageFromTemplate(2 , $language_id , $iBusinessId, $aBusiness['user_id']);
            Phpfox::getService('directory.mail.send')->send($aEmail['subject'], $aEmail['message'], $email);
        }
        $this->call("window.location = window.location;");
    }

    public function denyBusiness(){
        // Get Params
        $iBusinessId = (int)$this->get('iBusinessId');
        Phpfox::getUserParam('admincp.has_admin_access', true);
        if ($iBusinessId)
        {
            Phpfox::getService('directory.process')->denyBusiness($iBusinessId);
        }
        $this->call("window.location = window.location;");
    }

    public function deleteBusiness(){
        // Get Params
        $iBusinessId = (int)$this->get('iBusinessId');
        $iDetail = $this->get('iDetail');
        if ($iBusinessId)
        {
            $aBusiness = Phpfox::getService('directory')->getQuickBusinessById($iBusinessId);
            if(Phpfox::getService('directory.permission')->canDeleteBusiness($aBusiness['user_id']))
            {
                Phpfox::getService('directory.process')->delete($iBusinessId);
            }
            else{
                return false;
            }
        }
        
        $this->call("$('.mfp-close-btn-in .mfp-close').trigger('click');");
        Phpfox::addMessage(_p('business_deleted_successfully'));
        if((int)$iDetail)
        {
            $this->call("window.location.href = '" . Phpfox_Url::instance()->makeUrl('directory') . "'");
        }
        else{
            $this->call('setTimeout(function() {window.location.href = window.location.href},500);');
        }

    }
    public function closeBusiness(){
        // Get Params
        $iBusinessId = (int)$this->get('iBusinessId');
        if ($iBusinessId)
        {
            Phpfox::getService('directory.process')->closeBusiness($iBusinessId);
        }

        $this->call('setTimeout(function() {window.location.href = window.location.href},500);');

    }
    public function openBusiness(){
        // Get Params
        $iBusinessId = (int)$this->get('iBusinessId');
        if ($iBusinessId)
        {
            Phpfox::getService('directory.process')->openBusiness($iBusinessId);
        }

        $this->call('setTimeout(function() {window.location.href = window.location.href},500);');

    }
    public function deleteManyBusiness(){

        // Get Params
        $aSetBusiness = $this->get('aSetBusiness');
        $aSetBusiness = explode(",", $aSetBusiness);
        Phpfox::getUserParam('directory.can_delete_others_business',true);
        if(count($aSetBusiness)){    
            foreach ($aSetBusiness as $key => $iBusinessId) {
                $aBusiness = Phpfox::getService('directory')->getQuickBusinessById($iBusinessId);

                if($aBusiness['user_id'] != Phpfox::getUserId()){
                    continue;
                }

                if ($iBusinessId)
                {
                    Phpfox::getService('directory.process')->delete($iBusinessId);
                }    
            }   
        }
        
        $this->call("$('.mfp-close-btn-in .mfp-close').trigger('click');");
        $this->call('setTimeout(function() {window.location.href = window.location.href},500);');

    }

    public function clickClaimBusinessButton(){
        $iBusinessId = $this->get('business_id');
        $aBusiness = Phpfox::getService('directory')->getQuickBusinessById($iBusinessId);
        if(isset($aBusiness['business_id']) == false){
            echo json_encode(array(
                'status' => 'FAILURE', 
                'message' => _p('unable_to_find_the_business'),
            ));
            return;
        }
        if(isset($aBusiness['type']) != 'claiming' || isset($aBusiness['business_status']) != Phpfox::getService('directory.helper')->getStatusCode('draft')
        ){
            echo json_encode(array(
                'status' => 'FAILURE', 
                'message' => _p('the_business_is_not_for_claiming_or_claimed'),
            ));
            return;
        }

        // note: 
        // claiming + draft = claiming business
        // business + pendingclaiming = claimed business and waiting approve
        // business + draft = it is similiar business + draft (not payment) but package info is empty, need to buy package, after buying package --> business + running (NO need to approve)

        // update business_status = pendingclaiming
        Phpfox::getService('directory.process')->updateTypeOfBusiness($iBusinessId, 'business');
        // send email to owner
        $aUser = Phpfox::getService('user')->getUser(Phpfox::getUserId());
        $language_id = $aUser['language_id'] == null ? 'en' : $aUser['language_id'];
        $email = $aUser['email'];
        $aEmail = Phpfox::getService('directory.mail')->getEmailMessageFromTemplate(1 , $language_id , $iBusinessId, Phpfox::getUserId());
        Phpfox::getService('directory.mail.send')->send($aEmail['subject'], $aEmail['message'], $email);

        // FAILURE/SUCCESS
        echo json_encode(array(
            'status' => 'SUCCESS', 
            'message' => _p('you_have_just_claimed_the_business_title_successfully_please_wait_for_approval_from_administrator', array('title' => $aBusiness['name'])),
        ));
    }

    public function compareGetInfoBusiness(){
        $iBusinessId = $this->get('business_id');
        $aCategory = Phpfox::getService('directory')->getLastChildCategoryIdOfBusiness($iBusinessId);
        // FAILURE/SUCCESS
        echo json_encode(array(
            'status' => 'SUCCESS', 
            'message' => '', 
            'aCategory' => ($aCategory), 
        ));
    }

    public function initCompareItemBlock()
    {
        $listOfBusinessIdToCompare = $this->get('listOfBusinessIdToCompare');
        $listOfBusinessIdToCompare = trim($listOfBusinessIdToCompare);
        $aCategory = array();
        if(strlen($listOfBusinessIdToCompare) > 0){
            $aListOfBusinessIdToCompare = explode(',', $listOfBusinessIdToCompare);
            foreach ($aListOfBusinessIdToCompare as $key => $iBusinessId) {
                if($category = Phpfox::getService('directory')->getLastChildCategoryIdOfBusiness($iBusinessId)){
                    $aBusiness = Phpfox::getService('directory')->getQuickBusinessById($iBusinessId);
                    if (!empty($aBusiness['logo_path'])) {
                        $aBusiness['logo_path'] = Phpfox::getLib('image.helper')->display(array(
                            'server_id' => $aBusiness['server_id'],
                            'path' => 'core.url_pic',
                            'file' => $aBusiness['logo_path'],
                            'suffix' => '_100',
                            'return_url' => true
                        ));
                    }else {
                        $aBusiness['logo_path'] = Phpfox::getLib('image.helper')->display(array(
                            'server_id' => $aBusiness['server_id'],
                            'path' => '',
                            'file' => Phpfox::getService('directory')->getStaticPath() . 'module/directory/static/image/default_ava.png',
                            'suffix' => '_100',
                            'return_url' => true
                        ));
                    }
                    $aBusiness['item_link'] = Phpfox::permalink('directory.detail', $aBusiness['business_id'], $aBusiness['name']);
                    if(isset($aCategory[$category['category_id']])){
                        $aCategory[$category['category_id']]['list_business'][] = $aBusiness;
                    } else {
                        $aCategory[$category['category_id']] = array(
                            'data' => $category, 
                            'list_business' => array($aBusiness), 
                        );                                            
                    }
                }
            }
        }
            
        $sCompareLink = Phpfox::permalink('directory.comparebusiness', null, null);
        // FAILURE/SUCCESS
        echo json_encode(array(
            'status' => 'SUCCESS', 
            'message' => '', 
            'aCategory' => ($aCategory), 
            'sCompareLink' => ($sCompareLink), 
        ));
    }

    public function addFeedComment()
    {
        Phpfox::isUser(true);
        
        $aVals = (array) $this->get('val'); 

        if (Phpfox::getLib('parse.format')->isEmpty($aVals['user_status']))
        {
            $this->alert(_p('user.add_some_text_to_share'));
            $this->call('$Core.activityFeedProcess(false);');
            return;         
        }       
        
        $aBusiness = Phpfox::getService('directory')->getBusinessById($aVals['callback_item_id'], true);
        
        if (!isset($aBusiness['business_id']))
        {
            $this->alert(_p('unable_to_find_the_business_you_are_trying_to_comment_on'));
            $this->call('$Core.activityFeedProcess(false);');
            return;
        }
        
        $sLink = Phpfox::getLib('url')->permalink('directory.detail', $aBusiness['business_id'], $aBusiness['name']);
        $aCallback = array(
            'module' => 'directory',
            'table_prefix' => 'directory_',
            'link' => $sLink,
            'email_user_id' => $aBusiness['user_id'],
            'subject' => _p('full_name_wrote_a_comment_on_your_business_title', array('full_name' => Phpfox::getUserBy('full_name'), 'title' => $aBusiness['name'])),
            'message' => _p('full_name_wrote_a_comment_on_your_business_a_href_link_title_a_to_see_the_comment_thread_follow_the_link_below_a_href_link_link_a', array('full_name' => Phpfox::getUserBy('full_name'), 'link' => $sLink, 'title' => $aBusiness['name'])),
            'notification' => 'directory_comment',
            'feed_id' => 'directory_comment',
            'item_id' => $aBusiness['business_id']
        );
        
        $aVals['parent_user_id'] = $aVals['callback_item_id'];
        if (!empty(!empty($aVals['location']['latlng']))) {
            $latlng = explode(',', $aVals['location']['latlng']);
            if (count($latlng) == 2) {
                $aVals['location_latlng'] = json_encode(array(
                    'latitude' => $latlng[0],
                    'longitude' => $latlng[1],
                ));
            }
        }
        $aVals['location_name'] = !empty($aVals['location']['name']) ? $aVals['location']['name'] : null;

        if (isset($aVals['user_status']) && ($iId = Phpfox::getService('feed.process')->callback($aCallback)->addComment($aVals)))
        {
            Phpfox::getLib('database')->updateCounter('directory_business', 'total_comment', 'business_id', $aBusiness['business_id']);
            Phpfox::getService('feed')->callback($aCallback)->processAjax($iId);
        }
        else 
        {
            $this->call('$Core.activityFeedProcess(false);');
        }       
    }   

    public function changeCouponListFilter()
    {
        $aVals = $this->get('val');
        Phpfox::getBlock('directory.detailcouponslist', array(
            'aQueryParam' => $aVals 
        ));
        $jsEvent = $this->get('custom_event');
        $sHtml = $this->getContent();
        $this->call("$(document).trigger('{$jsEvent}', '{$sHtml}');");
    }

    public function changeMarketplaceListFilter()
    {
        $aVals = $this->get('val');
        Phpfox::getBlock('directory.detailmarketplacelist', array(
            'aQueryParam' => $aVals 
        ));
        $jsEvent = $this->get('custom_event');
        $sHtml = $this->getContent();
        $this->call("$(document).trigger('{$jsEvent}', '{$sHtml}');");
    }

    public function changeJobListFilter()
    {
        $aVals = $this->get('val');
        Phpfox::getBlock('directory.detailjobslist', array(
            'aQueryParam' => $aVals 
        ));
        $jsEvent = $this->get('custom_event');
        $sHtml = $this->getContent();
        $this->call("$(document).trigger('{$jsEvent}', '{$sHtml}');");
    }

    public function changeEventListFilter()
    {
        $aVals = $this->get('val');
        Phpfox::getBlock('directory.detaileventslist', array(
            'aQueryParam' => $aVals 
        ));
        $jsEvent = $this->get('custom_event');
        $sHtml = $this->getContent();
        $this->call("$(document).trigger('{$jsEvent}', '{$sHtml}');");
    }

    public function changePollsListFilter()
    {
        $aVals = $this->get('val');
        Phpfox::getBlock('directory.detailpollslist', array(
            'aQueryParam' => $aVals 
        ));
        $jsEvent = $this->get('custom_event');
        $sHtml = $this->getContent();
        $this->call("$(document).trigger('{$jsEvent}', '{$sHtml}');");
    }

    public function changeBlogListFilter()
    {
        $aVals = $this->get('val');
        Phpfox::getBlock('directory.detailblogslist', array(
            'aQueryParam' => $aVals 
        ));
        $jsEvent = $this->get('custom_event');
        $sHtml = $this->getContent();
        $this->call("$(document).trigger('{$jsEvent}', '{$sHtml}');");
    }

    public function changeFollowerListFilter()
    {
        $aVals = $this->get('val');
        Phpfox::getBlock('directory.detailfollowerslists', array(
            'aQueryParam' => $aVals 
        ));
        $jsEvent = $this->get('custom_event');
        $sHtml = $this->getContent();
        $this->call("$(document).trigger('{$jsEvent}', '{$sHtml}');");
    }
    
    public function changeMemberListFilter()
    {
        $aVals = $this->get('val');
        Phpfox::getBlock('directory.detailmemberslists', array(
            'aQueryParam' => $aVals 
        ));
        $jsEvent = $this->get('custom_event');
        $sHtml = $this->getContent();
        $this->call("$(document).trigger('{$jsEvent}', '{$sHtml}');");
    }

    

    public function changeMusicListFilter()
    {
        $aVals = $this->get('val');
        Phpfox::getBlock('directory.detailmusicslist', array(
            'aQueryParam' => $aVals 
        ));
        $jsEvent = $this->get('custom_event');
        $sHtml = $this->getContent();
        $this->call("$(document).trigger('{$jsEvent}', '{$sHtml}');");
    }

    public function changeVideoListFilter()
    {
        $aVals = $this->get('val');
        Phpfox::getBlock('directory.detailvideoslist', array(
            'aQueryParam' => $aVals 
        ));
        $jsEvent = $this->get('custom_event');
        $sHtml = $this->getContent();
        $this->call("$(document).trigger('{$jsEvent}', '{$sHtml}');");
    }
    public function changeUltimateVideoListFilter()
    {
        $aVals = $this->get('val');
        Phpfox::getBlock('directory.detailultimatevideoslist', array(
            'aQueryParam' => $aVals 
        ));
        $jsEvent = $this->get('custom_event');
        $sHtml = $this->getContent();
        $this->call("$(document).trigger('{$jsEvent}', '{$sHtml}');");
    }
    public function changeVListFilter()
    {
        $aVals = $this->get('val');
        Phpfox::getBlock('directory.detailcorevideoslist', array(
            'aQueryParam' => $aVals
        ));
        $jsEvent = $this->get('custom_event');
        $sHtml = $this->getContent();
        $this->call("$(document).trigger('{$jsEvent}', '{$sHtml}');");
    }
    public function changePhotoListFilter()
    {
        $aVals = $this->get('val');
        Phpfox::getBlock('directory.detailphotoslist', array(
            'aQueryParam' => $aVals 
        ));
        $jsEvent = $this->get('custom_event');
        $sHtml = $this->getContent();
        $this->call("$(document).trigger('{$jsEvent}', '{$sHtml}');");
    }

    public function setBusinessSessionInDashboard(){
        $type = $this->get('type');
        $business_id = $this->get('business_id');
        Phpfox::getService('directory.helper')->setSessionBeforeAddItemFromSubmitForm($business_id, $type);
        $this->call("var link = $('#yndirectory_managemodules_" . $type . "').data('link'); if(link) window.location.href = link;");
    }

    public function setBusinessSession()
    {
        $type = $this->get('type');
        $business_id = $this->get('business_id');
        Phpfox::getService('directory.helper')->setSessionBeforeAddItemFromSubmitForm($business_id, $type);
        $this->call("var yndirectory_add_new_link = $('#yndirectory_add_new_item').attr('href'); if(yndirectory_add_new_link) window.location.fref = yndirectory_add_new_link;");
    }

    public function showPreivewNewBusiness()
    {
        $sText = $this->get('sText');
        $sText = Phpfox::getLib('parse.input')->prepare($sText);
        // FAILURE/SUCCESS
        echo json_encode(array(
            'status' => 'SUCCESS', 
            'sText' => $sText
        ));
    }

    public function previewNewBusiness()
    {
        Phpfox::getBlock('directory.previewnewbusiness', array('sText' => $this->get('text')));
    }

    public function changeCustomFieldByMainCategory()
    {
        $oRequest = Phpfox::getLib('request');
        $iMainCategoryId = $oRequest->get('iMainCategoryId');
        $aCustomFields = Phpfox::getService('directory')->getCustomFieldByCategoryId($iMainCategoryId);

        $keyCustomField = array();



        $aCustomData = array();
        if($this->get('iBusinessId')){
            $aCustomDataTemp = Phpfox::getService('directory.custom')->getCustomFieldByBusinessId($this->get('iBusinessId'));
            
                if(count($aCustomFields)){
                    foreach ($aCustomFields as $aField) {
                            foreach ($aCustomDataTemp as $aFieldValue) {
                                if($aField['field_id'] == $aFieldValue['field_id']){
                                    $aCustomData[] = $aFieldValue;
                                }
                            }
                    }
                }

        }

        if(count($aCustomData)){
            $aCustomFields  = $aCustomData; 
        }

        Phpfox::getBlock('directory.custom.form', array(
            'aCustomFields' => $aCustomFields, 
        ));
        // FAILURE/SUCCESS
        echo json_encode(array(
            'status' => 'SUCCESS', 
            'content' => $this->getContent(false)
        ));
    }

    public function gmap()
    {
        Phpfox::getBlock('directory.gmap');
    }

	public function showPopupCustomGroup(){
        $iCategoryId = $this->get('category_id');
        Phpfox::getBlock('directory.popup-customfield-category', array('category_id' => $iCategoryId));

	}

    public function showPopupChangeMemberRole(){
        $iUserId = $this->get('user_id');
        $iBusinessId = $this->get('business_id');
        $canChangeRoles = Phpfox::getService('directory.permission')->canChangeMemberRoleDashBoard($iBusinessId);
        if($canChangeRoles){
           Phpfox::getBlock('directory.popup-change-role-id', array('user_id' => $iUserId,'business_id' => $iBusinessId));        
        }
    }

    public function changeMemberRoleId(){

        $iUserId = $this->get('user_id');
        $iBusinessId = $this->get('business_id');
        $iNewRoleId = $this->get('new_role_id');
        $canChangeRoles = true;
        if($canChangeRoles){
            Phpfox::getService('directory.process')->updateUserMemberRole($iBusinessId,$iUserId,$iNewRoleId);
            $this->alert(_p('role_of_member_has_been_changed_successfully'));
            $this->call('setTimeout(function() {window.location.href = window.location.href},1000);');
        }
    }

    public function ratePopup()
    {
        $iBusinessId = $this->get('business_id');
        // echo $iPage;exit;
        phpfox::isUser(true);
        Phpfox::getBlock('directory.rate-review', array(
            "iBusinessId" => $iBusinessId,
        ));
    }

    public function editRatePopup()
    {
        $iBusinessId = $this->get('business_id');
        $iReviewId = $this->get('old_review_id');
        // echo $iPage;exit;
        phpfox::isUser(true);
        Phpfox::getBlock('directory.rate-review', array(
            "iBusinessId" => $iBusinessId,
            "iReviewId"   => $iReviewId

        ));
    }
    public function reviewBusiness(){
        $aRating =  $this->get('rating');
        $iBusinessId = $aRating['business_id'];
        $sTitle = isset($aRating['title'])?$aRating['title']:'';
        $sContent = isset($aRating['content'])?$aRating['content']:'';
        $iRating = isset($aRating['star'])?$aRating['star']:0;
        if($sTitle == ''){
            $this->call('$(\'.js_box_close\').parent().remove();');
            $this->alert(_p('you_have_to_input_title'));
            return false;

        }
        if($sContent == ''){
            $this->call('$(\'.js_box_close\').parent().remove();');
            $this->alert(_p('you_have_to_input_content'));
            return false;

        }
        if($iRating==0){
            $this->call('$(\'.js_box_close\').parent().remove();');
            $this->alert(_p('you_have_to_rate_this_business'));
            return false;
        }

        $iOwnerBusinessId = Phpfox::getService('directory')->getBusinessOwnerId($iBusinessId);

        $bCanRate = Phpfox::getService('directory.permission')->canReviewBusiness($iOwnerBusinessId,$iBusinessId);
        if($bCanRate){
            Phpfox::getService('directory.process')->addReviewForBusiness($iBusinessId,$sTitle,$sContent,$iRating);        
            $this->call('setTimeout(function() {window.location.reload();},1000);');
        }
    
    }

    public function editReviewBusiness(){

        $aRating =  $this->get('rating');

        $iBusinessId = $aRating['business_id'];
        $sTitle = isset($aRating['title'])?$aRating['title']:'';
        $sContent = isset($aRating['content'])?$aRating['content']:'';
        $iRating = isset($aRating['star'])?$aRating['star']:0;
        if($sTitle == ''){
            $this->call('$(\'.js_box_close\').parent().remove();');
            $this->alert(_p('you_have_to_input_title'));
            return false;

        }
        if($sContent == ''){
            $this->call('$(\'.js_box_close\').parent().remove();');
            $this->alert(_p('you_have_to_input_content'));
            return false;

        }
        if($iRating==0){
            $this->call('$(\'.js_box_close\').parent().remove();');
            $this->alert(_p('you_have_to_rate_this_business'));
            return false;
        }


        $bCanEditRate = Phpfox::getUserParam('directory.can_edit_own_review');
        if($bCanEditRate){
            Phpfox::getService('directory.process')->editReviewForBusiness($iBusinessId,$sTitle,$sContent,$iRating);        
            $this->call('setTimeout(function() {window.location.reload();},1000);');
        }
    
    }


	public function AdminAddCustomFieldBackEnd()
    {
        $iGroupId = $this->get('iGroupId');
        if (intval($this->get('id'))) {
            $this->setTitle(_p('edit_custom_field'));
        } else {
            $this->setTitle(_p('add_custom_field'));
        }
        Phpfox::getComponent('directory.admincp.customfield.add-field', array('iGroupId' => $iGroupId), 'controller');
    }


    public function addField()
    {
        Phpfox::isAdmin(true);
        $aVals = $this->get('val');

        list($iFieldId, $aOptions) = Phpfox::getService('directory.custom.process')->add($aVals);
        if(!empty($iFieldId))
        {
            $this->call("$('#js_add_field_loading').hide();");
            $this->call("$('#js_add_field_button').attr('disabled', false);");
            $this->call("location.reload();");
        }
        else{
            $this->call("$('#js_add_field_loading').hide();");
            $this->call("$('#js_add_field_button').attr('disabled', false);");
            $this->alert(_p('please_input_name_of_field'));
        }
        

    }

    public function updateField()
    {
        Phpfox::isAdmin(true);
        $aVals = $this->get('val');
        if(Phpfox::getService('directory.custom.process')->update($aVals['id'], $aVals))
        {
            $this->call("$('#js_add_field_loading').hide();");
            $this->call("$('#js_add_field_button').attr('disabled', false);");
            $this->call("location.reload();");
        }
        else{
            $this->call("$('#js_add_field_loading').hide();");
            $this->call("$('#js_add_field_button').attr('disabled', false);");
            $this->alert(_p('please_input_name_of_field'));
        }

    }

    public function deleteField()
    {
        Phpfox::isAdmin(true);
        $id = $this->get('id');
        if (Phpfox::getService('directory.custom.process')->delete($id))
        {
            $this->remove('#js_custom_field_'.$id);
        }
    }
    
    public function deleteOption()
    {
        Phpfox::isAdmin(true);
        $id = $this->get('id');

        if (Phpfox::getService('directory.custom.process')->deleteOption($id))
        {
            $aFields = Phpfox::getService('directory.custom')->getCustomField();
            $this->remove('#js_current_value_'.$id);
        }
        else
        {
            $this->alert(_p('could_not_delete'));
        }
    }

    public function toggleActiveGroup()
    {
        if (Phpfox::getService('directory.custom.group')->toggleActivity($this->get('id')))
        {
            $this->call('$Core.customgroup.toggleGroupActivity(' . $this->get('id') . ')');
        }       
    }

    public function deletepackage(){

        Phpfox::isAdmin(true);
        $id = $this->get('id');
        if(Phpfox::getService('directory.package.process')->delete($id))
        {
            $this->call('setTimeout(function() {window.location.href = window.location.href},100);');
        }
    }

    public function activepackage(){
        $active = $this->get('active');
        $id = $this->get('id');
        
        if(Phpfox::getService('directory.package.process')->activepackage($id, $active))
        {
            if($active==1)
            {
                $this->call("$('#showpackage_{$id}').show();");
                $this->call("$('#hidepackage_{$id}').hide();");
            }    
            else {
                $this->call("$('#showpackage_{$id}').hide();");
                $this->call("$('#hidepackage_{$id}').show();");
            }
        }
    }
    
    public function getBusinessCreator(){

        $aBCreatorAdded = Phpfox::getService('directory')->getBusinessCreator();
        
        $aBCreatorAddedSql = array();

        if(count($aBCreatorAdded) > 0){
            foreach ($aBCreatorAdded as $aCreator) {
                $aBCreatorAddedSql[] = $aCreator['user_id'];
            }
        }
        $sWhere = '';
        if(count($aBCreatorAdded) > 0){
            
            $sWhere = 'u.profile_page_id = 0 AND u.user_id != ' . Phpfox::getUserId().' AND u.user_id NOT IN('.implode(",", $aBCreatorAddedSql).')';

        }else{

            $sWhere = 'u.profile_page_id = 0 AND u.user_id != ' . Phpfox::getUserId();
        }

        $aRows = Phpfox::getLib("database")->select('u.*, ' . Phpfox::getUserField())
            ->from(Phpfox::getT('user'), 'u', 'u.user_id = ')
            ->where($sWhere)
            ->limit(Phpfox::getParam('friend.friend_cache_limit'))
            ->order('u.last_activity DESC')
            ->execute('getSlaveRows');

        foreach ($aRows as $iKey => $aRow)
        {       
            if (Phpfox::getUserId() == $aRow['user_id'])
            {
                unset($aRows[$iKey]);
                
                continue;
            }
            
            $aRows[$iKey]['full_name'] = html_entity_decode(Phpfox::getLib('parse.output')->split($aRow['full_name'], 20), null, 'UTF-8');                      
            $aRows[$iKey]['user_profile'] = ($aRow['profile_page_id'] ? Phpfox::getService('pages')->getUrl($aRow['profile_page_id'], '', $aRow['user_name']) : Phpfox::getLib('url')->makeUrl($aRow['user_name']));
            $aRows[$iKey]['is_page'] = ($aRow['profile_page_id'] ? true : false);
            $aRows[$iKey]['user_image'] = Phpfox::getLib('image.helper')->display(array(
                    'user' => $aRow,
                    'suffix' => '_50_square',
                    'max_height' => 32,
                    'max_width' => 32,
                    'return_url' => true
                )
            );
        }       
        $this->call('$Cache.friends = ' . json_encode($aRows) . ';');
        $this->call("$('input[id^=\"search_input_name\"')[0].value = ''");
    }

    public function fillEmailTemplate()
    {
        $iTypeId = $this->get('email_template_id');
        $iLanguageId = $this->get('language_id');

        if (empty($iTypeId)){
            $iTypeId = 0;
        }
        if (empty($iTypeId)){
            $iTypeId = 0;
        }
        $aEmail = Phpfox::getService('directory.mail')->getEmailTemplate($iTypeId,$iLanguageId);

        $aEmail['email_template'] = str_replace('"', '\"', $aEmail['email_template']);

        $aEmail['email_subject'] = Phpfox::getLib('parse.output')->parse($aEmail['email_subject']);
        $aEmail['email_template'] = Phpfox::getLib('parse.output')->parse($aEmail['email_template'], false);
        $content = html_entity_decode($aEmail['email_template'], ENT_QUOTES);

        $this->call('$("#email_subject").val("'.$aEmail['email_subject'].'");');
        $this->val('#email_template', $content);
        $this->call('fillEmailTemplate("' . $content . '");');
    }

    public function getBusHomepageAjax(){

        Phpfox::getBlock('directory.homepage', array('getAjax' => true,'page' => $this->get('page'),'viewHomepage' => $this->get('viewHomepage')));
        
        $this->call('$(\'#yndirectory_homepage #yndirectory_view_hompage \').html(\'' . $this->getContent() . '\');');        
    
    }

    public function subscribeBusiness(){
        $email = $this->get('email');
        $categories = $this->get('categories');
        $location_lat = $this->get('location_lat');
        $location_lng = $this->get('location_lng');
        $radius = $this->get('radius');

        if($email == ''){
            $this->alert(_p('you_have_to_fill_email_field'));
            return false;
        }

        $aData = array(
            'email'        => $email,
            'categories'   => $categories,
            'location_lat' => $location_lat,
            'location_lng' => $location_lng,
            'radius'       => $radius            
            );

        if(Phpfox::getService('directory.process')->subscribeBusiness($aData)){
            $this->alert(_p('subscribe_successfully'));
        }

    }

    public function addFollow()
    {
        $iItem = (int)$this->get('item_id');

        $iFollowId = phpfox::getLib('database')->select('follow_id')->from(phpfox::getT('directory_follow'))->where("business_id = {$iItem} and user_id =".phpfox::getUserId())->execute('getSlaveField');

        if (!$iFollowId)
        {
            Phpfox::getService('directory.process')->addFollow($iItem);
        }

        $this->alert(_p('the_business_was_added_to_your_following_list'));
        $this->call('setTimeout(function() {window.location.href = window.location.href},2000);');
    }


    public function deleteFollow()
    {
        $iItem = (int)$this->get('item_id');

        $iFollowId = phpfox::getLib('database')->select('follow_id')->from(phpfox::getT('directory_follow'))->where("business_id = {$iItem} and user_id =".phpfox::getUserId())->execute('getSlaveField');

        if ($iFollowId)
        {
            Phpfox::getService('directory.process')->deleteFollow($iFollowId);
        }

        $this->alert(_p('the_business_was_removed_from_your_following_list'));
        $this->call('setTimeout(function() {window.location.href = window.location.href},2000);');
        
    }

    public function addFavorite()
    {
        $iItem = (int)$this->get('item_id');

        $iFavoriteId = phpfox::getLib('database')->select('favorite_id')->from(phpfox::getT('directory_favorite'))->where("business_id = {$iItem} and user_id =".phpfox::getUserId())->execute('getSlaveField');

        if (!$iFavoriteId)
        {
            Phpfox::getService('directory.process')->addFavorite($iItem);
        }

        $this->alert(_p('the_business_was_added_to_your_favorite_list'));
        $this->call('setTimeout(function() {window.location.href = window.location.href},2000);');
   }


    public function deleteFavorite()
    {
        $iItem = (int)$this->get('item_id');

        $iFavoriteId = phpfox::getLib('database')->select('favorite_id')->from(phpfox::getT('directory_favorite'))->where("business_id = {$iItem} and user_id =".phpfox::getUserId())->execute('getSlaveField');

        if ($iFavoriteId)
        {
            Phpfox::getService('directory.process')->deleteFavorite($iFavoriteId);
        }

        $this->alert(_p('the_directory_removed_from_your_favorite_list'));
        $this->call('setTimeout(function() {window.location.href = window.location.href},2000);');
        
    }

    public function feature()
    {
        $iBusinessId = $this->get('iBusinessId');
        $iFeatured = $this->get('iFeatured') ? 1 : 0;

        Phpfox::getService('directory.process')->feature($iBusinessId, $iFeatured);

    }

    public function delete()
    {
        $iBusinessId = $this->get('iBusinessId');
        if ($iBusinessId)
        {
            $aBusiness = Phpfox::getService('directory')->getQuickBusinessById($iBusinessId);
            if(Phpfox::getService('directory.permission')->canDeleteBusiness($aBusiness['user_id'],true))
            {
                Phpfox::getService('directory.process')->delete($iBusinessId);
            }
            else{
                return false;
            }
        }
        $this->call("$('#js_business_entry".$this->get('iBusinessId')."').hide('slow'); $('#core_js_messages').message('"._p('business_deleted_successfully', array('phpfox_squote' => true))."', 'valid').fadeOut(5000);");
    }

    public function getExtraInfo(){
        $iBusinessId = $this->get('iBusinessId');
        $sType = $this->get('sType');

        Phpfox::getBlock('directory.business-extra-info', array('iBusinessId' => $iBusinessId,'sType' => $sType));

    }

    public function moderation()
    {
        Phpfox::isUser(true);

        switch ($this->get('action'))
        {
            case 'delete':
                Phpfox::getUserParam('directory.can_delete_own_business', true);
                foreach ((array )$this->get('item_moderate') as $iId)
                {
                    Phpfox::getService('directory.process')->delete($iId);
                    $this->slideUp('#js_directory_entry'.$iId);
                }
                $sMessage = _p('businesses_successfully_deleted');
                break;
        }

        $this->alert($sMessage, 'Moderation', 300, 150, true);
        $this->hide('.moderation_process');
        $this->call('setTimeout(function(){ window.location.href = window.location.href; },3000);');
    }

    public function loadAjaxMapView(){

        $iPage = 0;
        $sCondition = $this->get('sCondition','');
        $iPage = $this->get('yndirectory_menu_current_index_page', 0);
        $iLimit = $this->get('yndirectory_menu_display_page', NULL);

        $aConditions = json_decode(base64_decode($sCondition));

        $aBusinessMap = Phpfox::getService('directory')->getBussinessForMap($aConditions, $iPage, $iLimit);

        $aBusinessMap = Phpfox::getService('directory.process')->prePareDataForMap($aBusinessMap);
       
        echo json_encode(array(
            'status' => 'SUCCESS', 
            'sCorePath' => Phpfox::getParam('core.path'),
            'data' => $aBusinessMap
        ));             

    }

    public function loadAjaxMapDetail(){

        $iPage = 0;
        $iBusinessId = $this->get('iBusinessId',0);

        $aLocations = Phpfox::getService('directory')->getBusinessLocation($iBusinessId);
       
        echo json_encode(array(
            'status' => 'SUCCESS', 
            'sCorePath' => Phpfox::getParam('core.path'),
            'data' => $aLocations
        ));             

    }

    public function deleteImage(){
        $id = $this->get('id'); //image_id
        Phpfox::getService('directory.process')->deleteImage($id);
        $this->call('$("#js_photo_holder_' . $id . '").remove(); updateImages();');
    }

    public function editTitlePageBusinessBlock(){
        $data_id = $this->get('data_id');
        Phpfox::getBlock('directory.edit-title-page', array('data_id' => $data_id));
    }

    public function updateTitlePage(){
        $page_business_id = $this->get('page_business_id');
        $new_title = $this->get('new_title');

        if($new_title != ''){
            Phpfox::getService('directory.process')->updateTitlePage($new_title,$page_business_id);
            $this->call("location.reload();");
        }
        else{
            $this->call('$(\'#js_edit_title_page #message\').html(\''._p('please_enter_a_title').'\');');
        }

    }

    public function addNewPageBusinessBlock(){
        $business_id = $this->get('business_id');
        Phpfox::getBlock('directory.add-new-business-page', array('business_id' => $business_id));

    }

    public function addNewPageBusiness(){
        $business_id = $this->get('business_id');
        $new_page = $this->get('new_page');

        if($new_page != ''){
            Phpfox::getService('directory.process')->updateTitlePage($new_page, $business_id);
            $this->call("location.reload();");
        }
        else{
            $this->call('$(\'#js_edit_title_page #message\').html(\''._p('please_enter_a_title').'\');');
        }

    }

    public function AddFaqBusinessBlock(){ 
        $faq_id = $this->get('faq_id');
        $business_id = $this->get('business_id');
        Phpfox::getComponent('directory.add-faq', array('faq_id' => $faq_id,'business_id' => $business_id));
    }

    public function addFaq(){ 

        $faq_id = $this->get('faq_id');
        $business_id = $this->get('business_id');
        $answer = $this->get('answer');
        $question = trim($this->get('question'));

        if($answer != '' &&  $question != ''){
            Phpfox::getService('directory.process')->saveFAQForBusiness($answer,$question,$business_id,$faq_id);
            $this->call("location.reload();");
        }
        else{
            $this->call('$(\'#js_add_faq_page #message\').html(\''._p('please_fill_input_data').'\');');
        }
    }

    public function deleteFaq(){
        $faq_id = $this->get('faq_id');
            Phpfox::getService('directory.process')->deleteFaq($faq_id);
            $this->call("location.reload();");

    }

    public function addCustomFieldBlockContactUs(){

        Phpfox::getComponent('directory.add-field-contact-us', array(), 'controller');

    }

    public function updateFieldContact(){

        $aVals = $this->get('val');
        if(Phpfox::getService('directory.customcontactus.process')->update($aVals['id'], $aVals))
        {
            $this->call("$('#js_add_field_loading').hide();");
            $this->call("$('#js_add_field_button').attr('disabled', false);");
            $this->call("location.reload();");
        }
        else{
            $this->call("$('#js_add_field_loading').hide();");
            $this->call("$('#js_add_field_button').attr('disabled', false);");
        }

    }

    public function addFieldContact(){
        
        $aVals = $this->get('val');
        $iContactUsId = $aVals['contact_us_id'];
        list($iFieldId, $aOptions) = Phpfox::getService('directory.customcontactus.process')->add($aVals,$iContactUsId);
       
        if(!empty($iFieldId))
        {
            $this->call("$('#js_add_field_loading').hide();");
            $this->call("$('#js_add_field_button').attr('disabled', false);");
            $this->call("location.reload();");
        }
        else{
            $this->call("$('#js_add_field_loading').hide();");
            $this->call("$('#js_add_field_button').attr('disabled', false);");
        }
        

    }

    public function deleteOptionContactUs(){

        $id = $this->get('id');

        if (Phpfox::getService('directory.customcontactus.process')->deleteOption($id))
        {
            $aFields = Phpfox::getService('directory.customcontactus.custom')->getCustomField();
            $this->remove('#js_current_value_'.$id);
        }
        else
        {
            $this->alert(_p('could_not_delete'));
        }

    }

    public function deleteCustomFieldContactUs(){
        $customfield_id = $this->get('customfield_id');
        Phpfox::getService('directory.customcontactus.process')->delete($customfield_id);
        $this->call("location.reload();");
    }

    public function deleteCustomPage(){
        $custompage_id = $this->get('idcustom');
        Phpfox::getService('directory.process')->deleteCustomPage($custompage_id);
        $this->call("location.reload();");
    }

    public function addNewRoleBlock(){
        $role_id = $this->get('role_id');
        $business_id = $this->get('business_id');
        Phpfox::getBlock('directory.add-new-role', array('role_id' => $role_id,'business_id' => $business_id ));
    }

    public function addNewRole(){
        $role_id = $this->get('role_id');
        $business_id = $this->get('business_id');
        $role_title = $this->get('role_title');


        if($role_title != ''){
            Phpfox::getService('directory.process')->addMemberRole($role_title,$business_id,$role_id);
           $this->call("location.reload();");
        }
        else{
            $this->call('$(\'#js_add_new_role #message\').html(\''._p('please_enter_a_title').'\');');
        }

    }

    public function deleteRoleMember(){
        $role_id = $this->get('role_id');
		$business_id = $this->get('business_id');
        Phpfox::getService('directory.permission')->canEditBusiness(Phpfox::getUserId(), $business_id, true);
        if((int)$role_id){
            Phpfox::getService('directory.process')->deleteMemberRole($role_id, $business_id);
            $this->call("location.reload();");
        }

    }

    public function deleteAnnouncement(){
        $announcement_id = $this->get('announcement_id');
        if((int)$announcement_id){
            Phpfox::getService('directory.process')->deleteAnnouncement($announcement_id);
            $this->call("location.reload();");
        }

    }

    public function getChartData(){
       
       $iBusinessId = $this->get('iBusinessid');
       $type = $this->get('iTypeId')?$this->get('iTypeId'):'normal';
       $metric = $this->get('iMetricId')?$this->get('iMetricId'):'reviews';
       $duration = $this->get('iDuration')?$this->get('iDuration'):'today';

       $extraData = array();
       if($duration == 'range_of_dates'){
           $extraData['js_start__datepicker'] = $this->get('js_start__datepicker');
           $extraData['js_end__datepicker'] = $this->get('js_end__datepicker'); 
       }

       $aChart = Phpfox::getService('directory')->getChartData($iBusinessId,$type,$metric,$duration,$extraData);

       $data['data'] = $aChart;
       $data['title'] = $metric;

       echo json_encode($data);
    }

    public function markAsRead(){
       $iAnnouncementId = $this->get('item_id');

        if(Phpfox::getService('directory.process')->markAsRead($iAnnouncementId)){
            $this->alert(_p('mark_as_read_successfully'));
            $this->call('setTimeout(function() {window.location.href = window.location.href},2000);');
        }
    }

    public function addUserMemberRole(){
        
        Phpfox::isUser(true);

        $iBusinessId = $this->get('item_id');
    
        $iOwnerBusinessId = Phpfox::getService('directory')->getBusinessOwnerId($iBusinessId); 
        $aRole = array();
        if($iOwnerBusinessId == Phpfox::getUserId()){
            $aRole = Phpfox::getService('directory')->getRoleIdByBusinessId($iBusinessId,'admin');            
        }
        else{
            $aRole = Phpfox::getService('directory')->getRoleIdByBusinessId($iBusinessId,'member');
        }

        Phpfox::getService('directory.process')->updateUserMemberRole($iBusinessId,Phpfox::getUserId(),$aRole['role_id']);   

        $this->alert(_p('you_became_a_member_of_this_business'));
        $this->call('setTimeout(function() {window.location.href = window.location.href},2000);');
    

    }

    public function deleteUserMemberRole(){
        Phpfox::isUser(true);
        
         $iBusinessId = $this->get('business_id');
         $iUserId = $this->get('user_id');
    
        $aBusiness =  Phpfox::getService('directory')->getQuickBusinessById($iBusinessId);
        if($aBusiness['user_id'] ==  Phpfox::getUserId()){
            Phpfox::getService('directory.process')->deleteUserMemberRole($iBusinessId,$iUserId);  
            $this->alert(_p('you_deleted_member_successfully'));
            $this->call('setTimeout(function() {window.location.href = window.location.href},2000);'); 
        }
            
    }

    public function leaveBusiness(){
        Phpfox::isUser(true);
        
         $iBusinessId = $this->get('item_id');
         $iUserId = Phpfox::getUserId();
    
        $aBusiness =  Phpfox::getService('directory')->getQuickBusinessById($iBusinessId);
        if(Phpfox::getService('directory.process')->deleteUserMemberRole($iBusinessId,$iUserId)){
            $this->alert(_p('you_leaved_this_business'));
            $this->call('setTimeout(function() {window.location.href = window.location.href},2000);'); 
        } 
            
            
    }

    public function browselike()
    {
        Phpfox::getBlock('directory.likebrowse');
        

        $sTitle = _p('like.people_who_like_this');
        
        $this->setTitle($sTitle);
    }

    public function addLike(){

        Phpfox::isUser(true);

        if (Phpfox::getService('like.process')->add($this->get('type_id'), $this->get('item_id')))
        {
            Phpfox::getLib('database')->updateCount('like', 'type_id = \'directory\' AND item_id = ' . (int) ($this->get('item_id')) . '', 'total_like', 'directory_business', 'business_id = ' . (int) ($this->get('item_id'))); 
        }
        
        $this->call('setTimeout(function() {window.location.href = window.location.href},100);'); 

    }


    public function deleteLike(){

        Phpfox::isUser(true);
        
        if (Phpfox::getService('like.process')->delete($this->get('type_id'), $this->get('item_id'), (int) $this->get('force_user_id')))
        {
            Phpfox::getLib('database')->updateCount('like', 'type_id = \'directory\' AND item_id = ' . (int) ($this->get('item_id')) . '', 'total_like', 'directory_business', 'business_id = ' . (int) ($this->get('item_id'))); 
        }
    
        $this->call('setTimeout(function() {window.location.href = window.location.href},100);'); 


    }


    public function inviteBlock()
    {
        Phpfox::getBlock('directory.form-invite-friend', array(
            'id' => $this->get('id'),
            'url' => $this->get('url'),
            ));
        $this->setTitle(_p('invite_friends'));

        $this->call('<script>$Core.loadInit();</script>');
    }

    public function updateFeaturedBackEnd(){

        Phpfox::isAdmin(true);

    // Get Params
        $iBusinessId = (int)$this->get('iBusinessId');
        $iIsFeatured = (int)$this->get('iIsFeatured');
        $iIsFeatured = (int)!$iIsFeatured;

        $oDirectoryProcess = Phpfox::getService('directory.process');
        if ($iBusinessId)
        {
            $oDirectoryProcess->featureBusinessBackEnd($iBusinessId, $iIsFeatured);
        }

        if ($iIsFeatured)
        {
            $sLabel = '<img src="'.Phpfox::getParam('core.path').'theme/adminpanel/default/style/default/image/misc/bullet_green.png" alt="">';
        }
        else
        {
            $sLabel = '<img src="'.Phpfox::getParam('core.path').'theme/adminpanel/default/style/default/image/misc/bullet_red.png" alt="">';
        }

       // $this->html('#item_update_featured_'.$iBusinessId, '<a href="javascript:void(0);"onclick="managebusiness.confirmFeaturedBackEnd('.$iBusinessId.','.$iIsFeatured.');"><div style="width:50px;">'.$sLabel.'</div></a>');
    
        $this->call('setTimeout(function() {window.location.href = window.location.href},1000);');

    }

    public function getLiveSearchForTranferOwner()
    {
        // This function is called from friend.static.search.js::getFriends in response to a keyup event when is_mail is passed as true in building the template
        // parent_id we have to find the class "js_temp_friend_search_form" from its parents
        // search_for 
        $aUsers = Phpfox::getService('directory')->getUserForTransferOwner(false,$this->get('search_for'));
        
        if (empty($aUsers))
        {
            return false;
        }
        // The next block is copied and modified from friend.static.search.js::getFriends
        $sHtml = '';
        $iFound = 0;
        $sStoreUser = '';
        foreach ($aUsers as $aUser)
        {
            $iFound++;
			
			if (substr($aUser['user_image'], 0, 4) == 'http') {
				$aUser['user_image'] = '<img style="width:50px" src="' . $aUser['user_image'] . '">';
			}
			$sHtml .= '<li><div rel="' . $aUser['user_id'] . '" class="js_friend_search_link ' . (($iFound == 1) ? 'js_temp_friend_search_form_holder_focus' : '') . '" href="#" onclick="return $Core.searchFriendsInput.processClick(this, \'' . $aUser['user_id'] . '\');"><span class="img-wrapper">' . $aUser['user_image'] . '</span><span class="user">' . $aUser['full_name'] . '</span></div></li>';
			$sStoreUser .= '$Core.searchFriendsInput.storeUser('.$aUser['user_id'].', JSON.parse('. json_encode(json_encode($aUser)) .'));';
			
            if ($iFound > $this->get('total_search'))
            {
                break;
            }
        }
        $sHtml = '<div class="js_temp_friend_search_form_holder" style="width:' . $this->get('width') . ';"><ul>' . $sHtml . '</ul></div>';
        $this->call($sStoreUser);
        $this->call('$("#'.$this->get('parent_id') . '").parent().find(".js_temp_friend_search_form").html(\''. str_replace("'", "\\'",$sHtml) .'\').show();');
    }

    public function getLiveSearchForBusinessCreator(){

        // This function is called from friend.static.search.js::getFriends in response to a keyup event when is_mail is passed as true in building the template
        // parent_id we have to find the class "js_temp_friend_search_form" from its parents
        // search_for 
        $aUsers = Phpfox::getService('directory')->getLiveSearchForBusinessCreator(false,$this->get('search_for'));
        
        if (empty($aUsers))
        {
            return false;
        }
        // The next block is copied and modified from friend.static.search.js::getFriends
        $sHtml = '';
        $iFound = 0;
        $sStoreUser = '';
        foreach ($aUsers as $aUser)
        {
            $iFound++;
			
           	if (substr($aUser['user_image'], 0, 4) == 'http') {
				$aUser['user_image'] = '<img style="width:50px" src="' . $aUser['user_image'] . '">';
			}
			$sHtml .= '<li><div rel="' . $aUser['user_id'] . '" class="js_friend_search_link ' . (($iFound == 1) ? 'js_temp_friend_search_form_holder_focus' : '') . '" href="#" onclick="return $Core.searchFriendsInput.processClick(this, \'' . $aUser['user_id'] . '\');"><span class="img-wrapper">' . $aUser['user_image'] . '</span><span class="user">' . $aUser['full_name'] . '</span></div></li>';
			$sStoreUser .= '$Core.searchFriendsInput.storeUser('.$aUser['user_id'].', JSON.parse('. json_encode(json_encode($aUser)) .'));';
            
            if ($iFound > $this->get('total_search'))
            {
                break;
            }
        }
        // find('.js_temp_friend_search_form')
        $sHtml = '<div class="js_temp_friend_search_form_holder" style="width:' . $this->get('width') . ';"><ul>' . $sHtml . '</ul></div>';
        $this->call($sStoreUser);
        $this->call('$("#'.$this->get('parent_id') . '").parent().find(".js_temp_friend_search_form").html(\''. str_replace("'", "\\'",$sHtml) .'\').show();');
    
    }

    public function updateActivity()
    {
        if (Phpfox::getService('directory.category.process')->updateActivity($this->get('id'), $this->get('active')))
        {

        }
    }

    public function categoryOrdering()
    {
        Phpfox::isAdmin(true);
        $aVals = $this->get('val');
        Phpfox::getService('core.process')->updateOrdering(array(
                'table' => 'directory_category',
                'key' => 'category_id',
                'values' => $aVals['ordering']
            )
        );

        Phpfox::getLib('cache')->remove('directory_category', 'substr');
    }

}
?>