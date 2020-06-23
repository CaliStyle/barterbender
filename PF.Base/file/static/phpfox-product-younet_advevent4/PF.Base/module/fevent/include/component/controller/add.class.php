<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');


/**
 * 
 * 
 * @copyright       [YOUNET_COPYRIGHT]
 * @author          YouNet Company
 * @package         YouNet_Event
 */
class Fevent_Component_Controller_Add extends Phpfox_Component {

    /**
     * Class process method wnich is used to execute this component.
     */
    public function process() {

        Phpfox::isUser(true);

        Phpfox::getUserParam('fevent.can_create_event', true);

        $oHelper = Phpfox::getService('fevent.helper');

        $bIsEdit = false;
        $bIsSetup = ($this->request()->get('req4') == 'setup' ? true : false);
        $sAction = $this->request()->get('tab');
        $aCallback = false;
        $sModule = $this->request()->get('module', false);
        $iItem = $this->request()->getInt('item', false);

        $until = "";
        if ($iEditId = $this->request()->get('id')) {

            if (($aEvent = Phpfox::getService('fevent')->getForEdit($iEditId))) {
                $content_repeat = "";

                if ($aEvent['isrepeat'] == 0) {
                    $content_repeat = _p('daily');
                } else if ($aEvent['isrepeat'] == 1) {
                    $content_repeat = _p('weekly');
                } else if ($aEvent['isrepeat'] == 2) {
                    $content_repeat = _p('monthly');
                }
                if ($content_repeat != "") {
                    if ($aEvent['timerepeat'] != 0) {
                        $sDefault = null;
                        $until = Phpfox::getTime("m/d/Y", $aEvent['timerepeat']);
                        $content_repeat .= ", " . _p('until') . " " . $until;
                    }
                }
                $bIsEdit = true;

                if ($aEvent['isrepeat'] >= 0) {
                    $isRepeat = 0;
                    if ($iEditId = $this->request()->get('id')) {
                        if (($aEvent = Phpfox::getService('fevent')->getForEdit($iEditId))) {
                            if ($aEvent['isrepeat'] > -1) {
                                $isRepeat = 1;
                            }
                        }
                    } else {
                        return false;
                    }
                    if (!$isRepeat) {
                        return false;
                    }
                    $this->template()->assign(array(
                        'isRepeat' => $isRepeat,
                    ));
                }

                $this->setParam('aEvent', $aEvent);
                $this->setParam(array(
                    'country_child_value' => $aEvent['country_iso'],
                    'country_child_id' => $aEvent['country_child_id']
                        )
                );

                //	get permission
                $canEditStartTime = $oHelper->canEditStartTimeByEventID($iEditId);
                $canEditEndTime = $oHelper->canEditEndTimeByEventID($iEditId);
                $canEditDuration = $oHelper->canEditDurationByEventID($iEditId);

                $this->template()->setHeader(array(
                            '<script type="text/javascript">$Behavior.eventEditCategory = function(){  var aCategories = explode(\',\', \'' . $aEvent['categories'] . '\'); for (i in aCategories) { $(\'#js_mp_holder_\' + aCategories[i]).show(); $(\'#js_mp_category_item_\' + aCategories[i]).attr(\'selected\', true); } }</script>'
                                )
                        )
                        ->assign(array(
                            'aForms' => $aEvent,
                            'aEvent' => $aEvent,
                            'canEditStartTime' => $canEditStartTime,
                            'canEditEndTime' => $canEditEndTime,
                            'canEditDuration' => $canEditDuration,
                            'content_repeat' => $content_repeat,
                                )
                );

                if ($aEvent['module_id'] != 'fevent') {
                    $sModule = $aEvent['module_id'];
                    $iItem = $aEvent['item_id'];
                }

                if ($aCustomFields = Phpfox::getService('fevent.custom')->getCustomFieldsForEdit($iEditId)) {
                    
                    $aCategories = explode(",", $aEvent['categories']);
                    
                    $oCustomService = PHpfox::getService('fevent.custom');
                    if (count($aCategories) > 0 && $aCategories[0] != "") {
                        $i = 0;
                        for($i=0;$i<count($aCategories);$i++)
                        {
                            if($aCategories[$i]==0){
                                break;
                            }
                        }
                        
                        if(isset($aCategories[$i]))
                        {
                            $aCustomDefault = $oCustomService->getFieldsByCateId($aCategories[$i]);
    
                            foreach ($aCustomDefault as $key => $aCustom) {
    
                                if (!$oCustomService->checkKeyCustomFields($aCustomFields, $aCustom['field_id'])) {
                                    $aCustomFields[] = array_merge($aCustom, $aCustomFields);
                                }
                            }
                        }
                    }

                    $this->template()->assign(array(
                        'aCustomFields' => $aCustomFields
                    ));
                }
            }
        }
        $this->template()->assign(array(
            'until' => $until,
        ));
        if ($sModule && $iItem && Phpfox::hasCallback($sModule, 'viewEvent')) {
            $aCallback = Phpfox::callback($sModule . '.viewEvent', $iItem);
			$aCallback['url_home_pages'] = $aCallback['url_home'].'fevent/';
            $this->template()->setBreadcrumb($aCallback['breadcrumb_title'], $aCallback['breadcrumb_home']);
            $this->template()->setBreadcrumb($aCallback['title'], $aCallback['url_home']);
            if ($sModule == 'pages' && !Phpfox::getService('pages')->hasPerm($iItem, 'fevent.share_events')) {
                return Phpfox_Error::display(_p('unable_to_view_this_item_due_to_privacy_settings'));
            }
        }

        $aValidation = array(
            'title' => _p('provide_a_name_for_this_event'),
            // 'country_iso' => _p('provide_a_country_location_for_this_event'),			
            'location' => _p('provide_a_location_for_this_event')
        );

        $oValidator = Phpfox::getLib('validator')->set(array(
            'sFormName' => 'js_event_form',
            'aParams' => $aValidation
                )
        );

        if ($aVals = $this->request()->get('val')) {
            $oFilter = Phpfox::getLib('parse.input');
            $aVals['description'] = $oFilter->prepare(htmlspecialchars_decode($aVals['description']));

            if ($oValidator->isValid($aVals)) {
                if (!empty($aVals['current_tab'])) {
                    $sAction = $aVals['current_tab'];
                }
                //	VALIDATE INPUT DATA
                $bAllowed = false;
                if($bIsEdit && !Phpfox::getParam('fevent.allow_change_time_recurrent_event') && isset($aEvent) && $aEvent['event_type'] == 'repeat'){
                    $aVals['repeat_section_start_hour'] =  $aEvent['repeat_section_start_hour'];
                    $aVals['repeat_section_start_minute'] =  $aEvent['repeat_section_start_minute'];
                    $aVals['repeat_section_end_hour'] =  $aEvent['repeat_section_end_hour'];
                    $aVals['repeat_section_end_minute'] =  $aEvent['repeat_section_end_minute'];
                }
                $verifyError = $this->__verify($aVals, $bIsEdit, $iEditId);
                if ($verifyError == true) {
                    $bAllowed = true;
                }

                $aVals['event_id'] = $iEditId;
                $this->template()->assign(array('aForms' => $aVals, 'aEvent' => $aVals));

                if ($bIsEdit && !$bAllowed) {
                    if (Phpfox::getService('fevent.process')->update($iEditId, $aVals, $aEvent)) {
                        if (isset($aVals['update_detail'])) {
                            if ('admins' == $sAction) {
                                $this->url()->send('fevent.add.admins', array('id' => $iEditId), _p('admins_updated'));
                            } else {
                                $this->url()->send('fevent.add', array('id' => $iEditId), _p('event_successfully_updated'));
                            }
                        } elseif (isset($aVals['upload_photo'])) {
                            if ($aErrors = phpfox_error::get()) {
                                Phpfox::getLib('session')->set('aErrors', $aErrors);
                                $this->url()->send('fevent.add/tab_customize', array('id' => $iEditId), _p('some_of_images_haven_t_been_uploaded'));
                            } else {
                                $this->url()->send('fevent.add/tab_customize', array('id' => $iEditId), _p('successfully_added_photo_s_to_your_event'));
                            }
                        } elseif (isset($aVals['send_invitations'])) {
                            $this->url()->send('fevent.add.invite', array('id' => $iEditId), _p('successfully_invited_guests_to_this_event'));
                        } else {
                            switch ($sAction) {
                                case 'customize':
                                    $this->url()->send('fevent.add.invite.setup', array('id' => $iEditId, 'tab' => 'invite'), _p('successfully_added_a_photo_to_your_event'));
                                    break;
                                case 'invite':
                                    $this->url()->send('fevent.add', array('id' => $iEditId, 'tab' => 'invite'), _p('successfully_invited_guests_to_this_event'));
                                    break;
                                case 'admins':
                                    $this->url()->send('fevent.add.admins', array('id' => $iEditId, 'tab' => 'admins'), _p('event_admins_successfully_updated'));
                                    break;
                                default:
                                    $this->url()->send('fevent.add', array('id' => $iEditId), _p('event_successfully_updated'));
                            }
                        }
                    } else {
                    }
                } else {
                    if (($iFlood = Phpfox::getUserParam('fevent.flood_control_events')) !== 0) {
                        $aFlood = array(
                            'action' => 'last_post', // The SPAM action
                            'params' => array(
                                'field' => 'time_stamp', // The time stamp field
                                'table' => Phpfox::getT('fevent'), // Database table we plan to check
                                'condition' => 'user_id = ' . Phpfox::getUserId(), // Database WHERE query
                                'time_stamp' => $iFlood * 60 // Seconds);	
                            )
                        );

                        // actually check if flooding
                        if (Phpfox::getLib('spam')->check($aFlood)) {
                            Phpfox_Error::set(_p('you_are_creating_an_event_a_little_too_soon') . ' ' . Phpfox::getLib('spam')->getWaitTime());
                        }
                    }

                    if (Phpfox_Error::isPassed()) {
                        if ($iId = Phpfox::getService('fevent.process')->add($aVals, ($aCallback !== false ? $sModule : 'fevent'), ($aCallback !== false ? $iItem : 0))) {
                            $aEvent = Phpfox::getService('fevent')->getForEdit($iId);
                            $this->url()->send('fevent.add', array('tab' => 'photo', 'id' => $aEvent['event_id']), _p('event_successfully_added'));
                        }
                    }
                }
            }

            $this->template()->assign('aForms', $aVals);
        }
        if ($bIsEdit) {
            $aMenus = array(
                'detail' => _p('event_details'),
                'customize' => _p('photo'),
                'invite' => _p('invite_guests'),
                'manage' => _p('manage_guest_list'),
                'email' =>  _p('mass_email'),
                'admins' => _p('t_addadmins')
            );
            $this->template()->buildPageMenu('js_event_block', $aMenus, array(
                'link' => $this->url()->permalink('fevent', $aEvent['event_id'], $aEvent['title']),
                'phrase' => _p('view_this_event')
                    )
            );

            if(isset($aVals) && is_array($aVals) && count($aVals) > 0){
                $this->template()->assign('aForms', array_merge($aEvent, $aVals));
            }
        }

        $sTab = $this->request()->get('tab');
        if ($sTab == 'photo' && $aErrors = Phpfox::getLib('session')->get('aErrors')) {
            foreach ($aErrors as $sError) {
                Phpfox_Error::set($sError);
            }
            Phpfox::getLib('session')->remove('aErrors');
        }
        $bCanAddMap = Phpfox::getUserParam('fevent.can_add_gmap');

        $fevent_max_instance_repeat_event = Phpfox::getParam('fevent.fevent_max_instance_repeat_event');

        $this->template()->setTitle(($bIsEdit ? _p('managing_event') . ': ' . $aEvent['title'] : _p('create_an_event')))
                ->setBreadcrumb(_p('events'), ($aCallback === false ? $this->url()->makeUrl('fevent') : $this->url()->makeUrl($aCallback['url_home_pages'])))
                ->setBreadcrumb(($bIsEdit ? _p('managing_event') . ': ' . $aEvent['title'] : _p('create_new_event')), ($bIsEdit ? $this->url()->makeUrl('fevent.add', array('id' => $aEvent['event_id'])) : $this->url()->makeUrl('fevent.add')), true)
                ->setEditor(array('wysiwyg' => Phpfox::getUserParam('fevent.can_use_editor_on_event')))
                ->setPhrase(array(
                    'core.select_a_file_to_upload'
                        )
                )
                ->setHeader('cache', array(
                    'fevent.js' => 'module_fevent',
                    'add.js' => 'module_fevent',
                    'map.js' => 'module_fevent',
                    'pager.css' => 'style_css',
                    'progress.js' => 'static_script',
                    'country.js' => 'module_core', 
                    'jquery.magnific-popup.js'  => 'module_fevent',
                    )
                )
                ->setHeader(array(
                    '<script type="text/javascript">$Behavior.eventProgressBarSettings = function(){ if ($Core.exists(\'#js_event_block_customize_holder\')) { oProgressBar = {holder: \'#js_event_block_customize_holder\', progress_id: \'#js_progress_bar\', uploader: \'#js_progress_uploader\', add_more: false, max_upload: 6, total: 1, frame_id: \'js_upload_frame\', file_id: \'image[]\'}; $Core.progressBarInit(); } }</script>'
                        )
                )
                ->assign(array(
                    'sCreateJs' => $oValidator->createJS(),
                    'valToken' => Phpfox::getService('log.session')->getToken(),
                    'sGetJsForm' => $oValidator->getJsForm(false),
                    'bCanAddMap' => $bCanAddMap,
                    'fevent_max_instance_repeat_event' => $fevent_max_instance_repeat_event,
                    'bIsEdit' => $bIsEdit,
                    'sTab' => $sTab,
                    'bIsSetup' => $bIsSetup,
                    'sCategories' => Phpfox::getService('fevent.category')->get(),
                    'sModule' => ($aCallback !== false ? $sModule : ''),
                    'iItem' => ($aCallback !== false ? $iItem : ''),
                    'aCallback' => $aCallback,
                    'iMaxFileSize' => (Phpfox::getUserParam('fevent.max_upload_size_event') === 0 ? null : Phpfox::getLib('phpfox.file')->filesize((Phpfox::getUserParam('fevent.max_upload_size_event') / 1024) * 1048576)),
                    'bCanSendEmails' => ($bIsEdit ? Phpfox::getService('fevent')->canSendEmails($aEvent['event_id']) : false),
                    'iCanSendEmailsTime' => ($bIsEdit ? Phpfox::getService('fevent')->getTimeLeft($aEvent['event_id']) : false),
                    'sTimeSeparator' => _p('time_separator'),
                    'apiKey' => Phpfox::getParam('core.google_api_key'),
                    'sAction' => $sAction
                        )
        );

        //if(false)            
        if (Phpfox::isModule('attachment') && Phpfox::getUserParam('fevent.can_attach_on_event')) {
            $this->setParam('attachment_share', array(
                'type' => 'fevent',
                'id' => 'js_event_form',
                'edit_id' => ($bIsEdit ? $iEditId : 0)
                    )
            );
        }
        $this->template()->setPhrase(array(
            "fevent.the_field_field_name_is_required"
            , "fevent.h_from_1_to_27"
            , "fevent.h_from_1_to_28"
            , "fevent.h_from_1_to_30"
            , "fevent.from_1_to_23"
            , "fevent.h_from_1_to_29"
            , "fevent.from_1_to_6"
            , 'fevent.duration'
            , 'fevent.h_from_1_to_number'
            , 'fevent.from_0_to_23'
            , 'fevent.h_from_0_to_29'
            , 'fevent.from_0_to_6'
            , 'fevent.h_from_0_to_number'
            , 'fevent.h_from_0_to_30'
            , 'fevent.edit_apply_for'
            , 'fevent.please_choose_the_type_of_event_to_edit'
            , 'fevent.only_this_event'
            , 'fevent.all_events_uppercase'
            , 'fevent.following_events'
            , 'fevent.confirm'
            , 'fevent.cancel'
            , 'fevent.note_apply_only_data_in_event_details_tab'
        ));

    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean() {
        (($sPlugin = Phpfox_Plugin::get('fevent.component_controller_add_clean')) ? eval($sPlugin) : false);
    }

    /**
     * Return true - there are errors
     * 			false - there are not errors
     */
    private function __verify($aVals, $bIsEdit = false, $iEditId) {
        // if($bIsEdit == false)
        {
            switch ($aVals['event_type'])    {
                case 'one_time':
                    break;
                
                case 'repeat':
                    // start time has to be less than  end time
                    // repeat until: NOT check because we will calculate in order to generate rigth number of instances 
                    $repeat_section_start_month = $aVals['repeat_section_start_month'];
                    $repeat_section_start_day = $aVals['repeat_section_start_day'];
                    $repeat_section_start_year = $aVals['repeat_section_start_year'];
                    $repeat_section_start_hour = $aVals['repeat_section_start_hour'];
                    $repeat_section_start_minute = $aVals['repeat_section_start_minute'];
                    $repeat_section_start = Phpfox::getLib('date')->mktime($repeat_section_start_hour, $repeat_section_start_minute, 0, $repeat_section_start_month, $repeat_section_start_day, $repeat_section_start_year);

                    $repeat_section_end_month = $aVals['repeat_section_end_month'];
                    $repeat_section_end_day = $aVals['repeat_section_end_day'];
                    $repeat_section_end_year = $aVals['repeat_section_end_year'];
                    $repeat_section_end_hour = $aVals['repeat_section_end_hour'];
                    $repeat_section_end_minute = $aVals['repeat_section_end_minute'];

                    $repeat_section_end = Phpfox::getLib('date')->mktime($repeat_section_end_hour, $repeat_section_end_minute, 0, $repeat_section_end_month, $repeat_section_end_day, $repeat_section_end_year);

                    if($repeat_section_end < $repeat_section_start){
                        if($bIsEdit){
                            Phpfox_Error::set(_p('end_time_has_to_be_greater_than_start_time'));
                        } else {
                            Phpfox_Error::set(_p('end_event_time_has_to_be_greater_than_start_event_time'));
                        }
                        return true;
                    }
                    elseif($aVals['repeat_section_end_repeat'] == 'repeat_until' && !$bIsEdit){
                        $repeat_section_repeatuntil = Phpfox::getLib('date')->mktime(23, 59, 59, $aVals['repeat_section_repeatuntil_month'], $aVals['repeat_section_repeatuntil_day'], $aVals['repeat_section_repeatuntil_year']);
                        if($repeat_section_repeatuntil < $repeat_section_start)
                        {
                            Phpfox_Error::set(_p('End repeat event time has to be greater than or equal to start event time'));
                        }
                        return true;
                    }

                    if($aVals['repeat_section_end_repeat'] == 'after_number_event'){
                        if((int)$aVals['repeat_section_after_number_event'] > (int)Phpfox::getParam('fevent.fevent_max_instance_repeat_event')){
                            Phpfox_Error::set(_p('end_repeat_allow_maximum_number_event_s', array('number' => Phpfox::getParam('fevent.fevent_max_instance_repeat_event'))));
                            return true;
                        }
                    }
                    break;
            }
        }

        return false;
    }

}

?>