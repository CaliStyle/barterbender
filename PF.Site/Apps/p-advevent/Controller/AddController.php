<?php

namespace Apps\P_AdvEvent\Controller;

use Phpfox;
use Phpfox_Component;
use Phpfox_Plugin;
use Phpfox_Error;
use Phpfox_Url;
use Phpfox_Request;

class AddController extends Phpfox_Component
{
    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
        Phpfox::isUser(true);

        $oHelper = Phpfox::getService('fevent.helper');

        $bIsEdit = false;
        $bIsSetup = ($this->request()->get('req4') == 'setup' ? true : false);
        $sAction = $this->request()->get('tab');
        $aCallback = false;
        $sModule = $this->request()->get('module', false);
        $iItem = $this->request()->getInt('item', false);

        $aValidation = array(
            'title' => _p('provide_a_name_for_this_event'),
            'location' => _p('provide_a_location_for_this_event')
        );
        $until = "";

        if ($iEditId = $this->request()->get('id')) {
            if ($aEvent = Phpfox::getService('fevent')->getForEdit($iEditId)) {
                if (!Phpfox::getService('fevent.helper')->canEditEvent($aEvent)) {
                    return Phpfox_Error::display(_p('unable_to_edit_this_event'));
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

                $this->template()->setHeader(array(
                    '<script type="text/javascript">$Behavior.eventEditCategory = function(){  var aCategories = explode(\',\', \'' . $aEvent['categories'] . '\'); for (i in aCategories) { $(\'#js_mp_holder_\' + aCategories[i]).show(); $(\'#js_mp_category_item_\' + aCategories[i]).attr(\'selected\', true); } }</script>'
                ))->assign(array(
                        'aForms' => $aEvent,
                        'aEvent' => $aEvent,
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
                        for ($i = 0; $i < count($aCategories); $i++) {
                            if ($aCategories[$i] == 0) {
                                break;
                            }
                        }

                        if (isset($aCategories[$i - 1])) {
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
        } else {
            Phpfox::getUserParam('fevent.can_create_event', true);
            $aValidation['temp_file'] = array(
                'def' => 'int:required',
                'min' => '1',
                'title' => _p('fevent.featured_photo_is_required')
            );
        }

        $this->template()->assign(array(
            'until' => $until,
        ));
        if ($sModule && $iItem && Phpfox::hasCallback($sModule, 'viewEvent')) {
            $aCallback = Phpfox::callback($sModule . '.viewEvent', $iItem);
            $aCallback['url_home_pages'] = $aCallback['url_home'] . 'fevent/';
            $this->template()->setBreadcrumb($aCallback['breadcrumb_title'], $aCallback['breadcrumb_home']);
            $this->template()->setBreadcrumb($aCallback['title'], $aCallback['url_home']);
            if ($sModule == 'pages' && !Phpfox::getService('pages')->hasPerm($iItem, 'fevent.share_events')) {
                return Phpfox_Error::display(_p('unable_to_view_this_item_due_to_privacy_settings'));
            }
        }

        $isCreating = $bIsEdit ? ($this->request()->get('creating') ? 1 : 0) : 1;

        Phpfox::getService('fevent.helper')->buildSectionMenu();

        if ($aVals = $this->request()->get('val')) {
            $oFilter = Phpfox::getLib('parse.input');
            $aVals['description'] = $oFilter->prepare(htmlspecialchars_decode($aVals['description']));

            if ($aVals['isrepeat'] != '-1' && $aVals['repeat_section_end_repeat'] == 'after_number_event') {
                $fevent_max_instance_repeat_event = (int)Phpfox::getParam('fevent.fevent_max_instance_repeat_event');
                $aValidation['repeat_section_after_number_event'] = array(
                    'def' => 'int:required',
                    'min' => '1',
                    'max' => '50',
                    'title' => _p('number_of_repeat_events_must_be_between_one_and_max', array('max' => $fevent_max_instance_repeat_event))
                );
            }

            $oValidator = Phpfox::getLib('validator')->set(array(
                    'sFormName' => 'js_event_form',
                    'aParams' => $aValidation
                )
            );

            if ($oValidator->isValid($aVals) && $this->__verify($aVals, $bIsEdit)) {
                if (!empty($aVals['current_tab'])) {
                    $sAction = $aVals['current_tab'];
                }
                //	VALIDATE INPUT DATA
                $bAllowed = false;
                if ($bIsEdit && !Phpfox::getParam('fevent.allow_change_time_recurrent_event') && isset($aEvent) && $aEvent['event_type'] == 'repeat') {
                    // output repeat time or not
                }

                $aVals['event_id'] = $iEditId;

                $this->template()->assign(array('aForms' => $aVals, 'aEvent' => $aVals));

                if ($bIsEdit) {
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
                                    $this->url()->send('fevent.add.invite.setup', array('id' => $iEditId, 'tab' => 'invite', 'creating' => $isCreating), _p('successfully_added_a_photo_to_your_event'));
                                    break;
                                case 'invite':
                                    $this->url()->send('fevent.add', array('id' => $iEditId, 'tab' => 'invite', 'creating' => $isCreating), _p('successfully_invited_guests_to_this_event'));
                                    break;
                                case 'admins':
                                    $this->url()->send('fevent.add', array('id' => $iEditId, 'tab' => 'admins'), _p('fevent_admins_successfully_updated'));
                                    break;
                                default:
                                    $this->url()->send('fevent.add', array('id' => $iEditId), _p('event_successfully_updated'));
                            }
                        }
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
                    $aFile = Phpfox::getService('core.temp-file')->get($aVals['temp_file']);

                    if (empty($aFile)) {
                        Phpfox_Error::set(_p('image_file_not_found'));
                    } else {
                        Phpfox::getService('core.temp-file')->delete($aVals['temp_file']);
                    }
                    $aVals['user_id'] = Phpfox::getUserId();
                    $aVals['image_path'] = $aFile['path'];
                    $aVals['server_id'] = $aFile['server_id'];

                    if ($iId = Phpfox::getService('fevent.process')->add($aVals, ($aCallback !== false ? $sModule : 'fevent'), ($aCallback !== false ? $iItem : 0))) {
                        $aEvent = Phpfox::getService('fevent')->getForEdit($iId);
                        $this->url()->send('fevent.add', array(
                            'id' => $iId,
                            'tab' => 'customize',
                            'creating' => 1,
                        ), _p('your_event_is_created_successfully'));
                    }
                }
            }

            $this->template()->assign('aForms', $aVals);
        } else {
            $oValidator = Phpfox::getLib('validator')->set(array(
                    'sFormName' => 'js_event_form',
                    'aParams' => $aValidation
                )
            );
        }

        $iTotalImage = 0;
        if ($bIsEdit) {
            $iTotalImage = Phpfox::getService('fevent')->countImages($iEditId);
            if ($isCreating) { // fake creating
                $eventId = (int)$aEvent['event_id'];
                $totalInvites = db()->select('COUNT(*)')
                    ->from(Phpfox::getT('fevent_invite'))
                    ->where('invited_user_id <> ' . Phpfox::getUserId() . ' AND event_id = ' . $eventId)
                    ->execute('getSlaveField');

                $aMenus = array(
                    'detail' => array(
                        'title' => _p('event_info'),
                        'finished' => 1,
                        'enabled' => 1
                    ),
                    'customize' => array(
                        'title' => _p('photos'),
                        'finished' => $iTotalImage > 1 ? 1 : 0,
                        'enabled' => 1
                    ),
                    'invite' => array(
                        'title' => _p('invite_guests'),
                        'finished' => $totalInvites > 0 ? 1 : 0,
                        'enabled' => 1
                    ),
                );
                $this->buildStepsMenu('js_event_block', $aMenus);
            } else {
                $aMenus = array(
                    'detail' => _p('event_details'),
                    'customize' => _p('photos'),
                    'invite' => _p('invite_guests'),
                    'manage' => _p('manage_guest_list'),
                    'email' => _p('mass_email'),
                    'admins' => _p('t_addadmins')
                );
                $this->template()->buildPageMenu('js_event_block', $aMenus, array(
                        'link' => $this->url()->permalink('fevent', $aEvent['event_id'], $aEvent['title']),
                        'phrase' => _p('view_this_event')
                    )
                );
            }

            if (isset($aVals) && is_array($aVals) && count($aVals) > 0) {
                $this->template()->assign('aForms', array_merge($aEvent, $aVals));
            }
        } else {
            $aMenus = array(
                'detail' => array(
                    'title' => _p('event_info'),
                    'finished' => 0,
                    'enabled' => 1
                ),
                'customize' => array(
                    'title' => _p('photos'),
                    'finished' => 0,
                    'enabled' => 0
                ),
                'invite' => array(
                    'title' => _p('invite_guests'),
                    'finished' => 0,
                    'enabled' => 0
                ),
            );

            $this->buildStepsMenu('js_event_block', $aMenus);
        }

        $sTab = $this->request()->get('tab');
        if ($sTab == 'photo' && $aErrors = Phpfox::getLib('session')->get('aErrors')) {
            foreach ($aErrors as $sError) {
                Phpfox_Error::set($sError);
            }
            Phpfox::getLib('session')->remove('aErrors');
        }
        $bCanAddMap = true;

        $fevent_max_instance_repeat_event = Phpfox::getParam('fevent.fevent_max_instance_repeat_event');

        $this->template()->setTitle((($bIsEdit && !$isCreating) ? _p('managing_event') . ': ' . $aEvent['title'] : _p('create_an_event')))
            ->setBreadcrumb(_p('events'), ($aCallback === false ? $this->url()->makeUrl('fevent') : $this->url()->makeUrl($aCallback['url_home_pages'])))
            ->setBreadcrumb(($bIsEdit && !$isCreating ? _p('managing_event') . ': ' . $aEvent['title'] : _p('create_event')), ($bIsEdit ? $this->url()->makeUrl('fevent.add', array('id' => $aEvent['event_id'])) : $this->url()->makeUrl('fevent.add')), true)
            ->setPhrase(array(
                'core.select_a_file_to_upload'
            ))->setHeader('cache', array(
                'jscript/fevent.js' => 'app_p-advevent',
                'jscript/add.js' => 'app_p-advevent',
                'jscript/map.js' => 'app_p-advevent',
                'pager.css' => 'style_css',
                'progress.js' => 'static_script',
                'country.js' => 'module_core',
                'jscript/jquery.magnific-popup.js' => 'app_p-advevent',
                'jscript/picktim.js' => 'app_p-advevent',
            ))->setHeader(array(
                '<script type="text/javascript">$Behavior.eventProgressBarSettings = function(){ if ($Core.exists(\'#js_event_block_customize_holder\')) { oProgressBar = {holder: \'#js_event_block_customize_holder\', progress_id: \'#js_progress_bar\', uploader: \'#js_progress_uploader\', add_more: false, max_upload: 6, total: 1, frame_id: \'js_upload_frame\', file_id: \'image[]\'}; $Core.progressBarInit(); } }</script>'
            ))->assign(array(
                'sCreateJs' => $oValidator->createJS(),
                'valToken' => Phpfox::getService('log.session')->getToken(),
                'sGetJsForm' => $oValidator->getJsForm(false),
                'bCanAddMap' => $bCanAddMap,
                'fevent_max_instance_repeat_event' => $fevent_max_instance_repeat_event,
                'bIsEdit' => $bIsEdit,
                'iTotalImage' => $iTotalImage,
                'iRemainUpload' => Phpfox::getUserParam('fevent.max_upload_image_event') - $iTotalImage,
                'isCreating' => $isCreating,
                'sTab' => $sTab,
                'bIsSetup' => $bIsSetup,
                'categories' => Phpfox::getService('fevent.category')->getTree(0),
                'sModule' => ($aCallback !== false ? $sModule : ''),
                'iItem' => ($aCallback !== false ? $iItem : ''),
                'aCallback' => $aCallback,
                'iMaxFileSize' => (Phpfox::getUserParam('fevent.max_upload_size_event') === 0 ? null : Phpfox::getLib('phpfox.file')->filesize((Phpfox::getUserParam('fevent.max_upload_size_event') / 1024) * 1048576)),
                'bCanSendEmails' => ($bIsEdit ? Phpfox::getService('fevent')->canSendEmails($aEvent['event_id']) : false),
                'iCanSendEmailsTime' => ($bIsEdit ? Phpfox::getService('fevent')->getTimeLeft($aEvent['event_id']) : false),
                'sTimeSeparator' => _p('time_separator'),
                'apiKey' => Phpfox::getParam('core.google_api_key'),
                'sAction' => $sAction
            ));

        //if(false)
        if (Phpfox::isModule('attachment') && Phpfox::getUserParam('fevent.can_attach_on_event')) {
            $this->setParam('attachment_share', array(
                'type' => 'fevent',
                'id' => 'js_event_form',
                'edit_id' => ($bIsEdit ? $iEditId : 0)
            ));
        }
        $this->template()->setPhrase(array(
            "fevent.the_field_field_name_is_required",
            'fevent.h_from_1_to_27',
            'fevent.h_from_1_to_28',
            'fevent.h_from_1_to_30',
            'fevent.from_1_to_23',
            'fevent.h_from_1_to_29',
            'fevent.from_1_to_6',
            'fevent.duration',
            'fevent.h_from_1_to_number',
            'fevent.from_0_to_23',
            'fevent.h_from_0_to_29',
            'fevent.from_0_to_6',
            'fevent.h_from_0_to_number',
            'fevent.h_from_0_to_30',
            'fevent.edit_apply_for',
            'fevent.please_choose_the_type_of_event_to_edit',
            'fevent.only_this_event',
            'fevent.all_events_uppercase',
            'fevent.following_events',
            'fevent.confirm',
            'fevent.cancel',
            'fevent.note_apply_only_data_in_event_details_tab'
        ));

        return null;
    }

    /**
     * Return true - there are errors
     *            false - there are not errors
     */
    private function __verify($aVals, $bIsEdit = false, $iEditId = 0)
    {
        list($start, $end) = Phpfox::getService('fevent.helper')->parseStartEndTime($aVals);

        if (!($end > $start)) {
            Phpfox_Error::set(_p('end_time_has_to_be_greater_than_start_time'));
        }

        if ($aVals['isrepeat'] != '-1') {
            if ($aVals['repeat_section_end_repeat'] == 'repeat_until' && !$bIsEdit) { // not require validation when editing
                $repeat_section_repeatuntil = Phpfox::getLib('date')->mktime(23, 59, 59, $aVals['repeat_section_repeatuntil_month'], $aVals['repeat_section_repeatuntil_day'], $aVals['repeat_section_repeatuntil_year']);
                if ($repeat_section_repeatuntil < $start) {
                    Phpfox_Error::set(_p('End repeat event time has to be greater than or equal to start event time'));
                }
            }
        }

        if ($aVals['repeat_section_end_repeat'] == 'after_number_event') {
            if ((int)$aVals['repeat_section_after_number_event'] > (int)Phpfox::getParam('fevent.fevent_max_instance_repeat_event')) {
                Phpfox_Error::set(_p('end_repeat_allow_maximum_number_event_s',
                    array(
                        'number' => Phpfox::getParam('fevent.fevent_max_instance_repeat_event')
                    )
                ));
            }
        }

        if(!empty($aVals['category'])) {
            $customFields = Phpfox::getService('fevent.custom')->getFieldsByCateId($aVals['category']);
            foreach($customFields as $customField) {
                if($customField['is_required'] && empty($aVals['custom'][$customField['field_id']])) {
                    Phpfox_Error::set(_p('fevent.custom_field_is_required', ['name' => _p($customField['phrase_var_name'])]));
                }
            }
        }

        return Phpfox_Error::isPassed();
    }

    public function buildStepsMenu($sName, $aMenu, $aLink = null, $bIsFullLink = false)
    {
        // current url
        $sPageCurrentUrl = Phpfox_Url::instance()->makeUrl('current');
        // current tab
        $sCurrentTab = Phpfox_Request::instance()->get('tab');
        // check active tab
        foreach ($aMenu as $sTabId => $sTabName) {
            if (($bIsFullLink && ($sTabId == $sPageCurrentUrl)) ||
                (!$bIsFullLink && $sCurrentTab && $sTabId == $sCurrentTab)
            ) {
                $sActiveTab = $sTabId;
            }
        }

        if (!isset($sActiveTab) && !$bIsFullLink) {
            // set first menu as active
            $sActiveTab = key($aMenu);
        }

        $menuKey = array_keys($aMenu);

        $this->template()->assign(array(
                'aPageStepMenu' => $aMenu,
                'sPageStepMenuName' => $sName,
                'aPageExtraLink' => $aLink,
                'bPageIsFullLink' => $bIsFullLink,
                'sActiveTab' => $sActiveTab,
                'currentStep' => array_search($sActiveTab, $menuKey) + 1,
                'totalSteps' => count($menuKey)
            )
        );
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('fevent.component_controller_add_clean')) ? eval($sPlugin) : false);
    }
}
