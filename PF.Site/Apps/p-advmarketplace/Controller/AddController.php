<?php

namespace Apps\P_AdvMarketplace\Controller;

defined('PHPFOX') or exit('NO DICE!');

use Phpfox;
use Phpfox_Component;
use Phpfox_Error;
use Phpfox_Plugin;
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
        $bIsEdit = false;
        $sModule = $this->request()->get('module');
        $iItemId = $this->request()->getInt('item');

        $cfInfors = Phpfox::getService("advancedmarketplace")->backend_getcustomfieldinfos();
        $aListing = array();

        $aValidation = array(
            'title' => _p('advancedmarketplace.provide_a_name_for_this_listing'),
            'country_iso' => _p('advancedmarketplace.provide_a_location_for_this_listing'),
            'category' => array(
                'def' => 'int:required',
                'min' => 1,
                'title' => _p('provide_a_category_this_listing_will_belong_to')
            ),
            'price' => array(
                'def' => 'float',
                'min' => 0,
                'title' => _p('provide_a_valid_price')
            ),
        );

        $paymentGateways = Phpfox::getService('advancedmarketplace.helper')->getPaymentGateways();

        if ($iEditId = $this->request()->get('id')) {
            if (($aListing = Phpfox::getService('advancedmarketplace')->getForEdit($iEditId, true))) {
                $bIsEdit = true;
                if (Phpfox::isModule('tag') && isset($aListing['listing_id'])) {
                    $aTags = Phpfox::getService('tag')->getTagsById('advancedmarketplace', $aListing['listing_id']);
                    if (isset($aTags[$aListing['listing_id']])) {
                        $aListing['tag_list'] = '';
                        foreach ($aTags[$aListing['listing_id']] as $aTag) {
                            $aListing['tag_list'] .= ' ' . $aTag['tag_text'] . ',';
                        }
                        $aListing['tag_list'] = trim(trim($aListing['tag_list'], ','));
                    }
                }

                if (!Phpfox::getService('advancedmarketplace.helper')->canEdit($aListing)) {
                    return Phpfox_Error::display(_p('unable_to_edit_this_listing'));
                }

                $this->setParam('aListing', $aListing);
                $this->setParam(array(
                        'country_child_value' => $aListing['country_iso'],
                        'country_child_id' => $aListing['country_child_id']
                    )
                );
                // custom field
                $iCatId = $aListing["category"]["category_id"];
                $iListingId = $aListing["listing_id"];
                $aCustomFields = Phpfox::getService("advancedmarketplace.customfield.advancedmarketplace")->frontend_loadCustomFields($iCatId,
                    $iListingId);

                $this->template()->assign(array(
                        'aForms' => $aListing,
                        'aCustomFields' => $aCustomFields,
                        'cfInfors' => $cfInfors,
                    )
                );
            }
            $sModule = $aListing['module_id'];
            $iItemId = $aListing['item_id'];

            $listingPaymentMethods = Phpfox::getService('advancedmarketplace.process')->getListingPaymentMethods($iEditId);

            foreach ($paymentGateways as $key => $paymentGateway) {
                if ($listingPaymentMethods == false || in_array($paymentGateway['gateway_id'], $listingPaymentMethods)) {
                    $paymentGateways[$key]['checked'] = 1;
                }
            }
        } else {
            Phpfox::getUserParam('advancedmarketplace.can_create_listing', true);
            $aValidation['temp_file'] = array(
                'def' => 'int:required',
                'min' => '1',
                'title' => _p('featured_photo_is_required')
            );

            if (!Phpfox::getParam('tag.enable_tag_support')) {
                $this->template()->assign(array(
                        'sTagType' => 'advancedmarketplace',
                    )
                );
            }
            $this->template()
                ->assign(array(
                        'aCustomFields' => array(),
                        'cfInfors' => $cfInfors,
                        'aForms' => array(),
                    )
                );
        }

        $isCreating = $bIsEdit ? ($this->request()->get('creating') ? 1 : 0) : 1;

        $oValidator = Phpfox::getLib('validator')->set(array(
                'sFormName' => 'js_advancedmarketplace_form',
                'aParams' => $aValidation
            )
        );

        if (!empty($sModule) && Phpfox::hasCallback($sModule, 'getItem')) {
            $aCallback = Phpfox::callback($sModule . '.getItem', $iItemId);
            if ($aCallback === false) {
                return Phpfox_Error::display(_p('Cannot find the parent item.'));
            }
            $bCheckParentPrivacy = true;
            if (!$bIsEdit && Phpfox::hasCallback($sModule, 'checkPermission')) {
                $bCheckParentPrivacy = Phpfox::callback($sModule . '.checkPermission', $iItemId, 'advancedmarketplace.share_advancedmarketplace');
            }

            if (!$bCheckParentPrivacy) {
                return Phpfox_Error::display(_p('unable_to_view_this_item_due_to_privacy_settings'));
            }
        }

        if (!empty($sModule) && !empty($iItemId)) {
            $this->template()->assign([
                'sModule' => $sModule,
                'iItem' => $iItemId
            ]);
        }

        if ($aVals = $this->request()->getArray('val')) {
            $integrateParams = array();
            if ($module_id = $this->request()->get('module', false)) {
                $integrateParams['module_id'] = $module_id;
            }
            if ($item_id = $this->request()->get('item', false)) {
                $integrateParams['item_id'] = $item_id;
            }
            if (count($integrateParams)) {
                $aVals = array_merge($aVals, $integrateParams);
            }

            if (!empty($aVals['is_sell'])) {
                $aValidation['payment_methods'] = array(
                    'def' => 'required',
                    'title' => _p('payment_method_is_required')
                );
                $oValidator = Phpfox::getLib('validator')->set(array(
                        'sFormName' => 'js_advancedmarketplace_form',
                        'aParams' => $aValidation
                    )
                );
            }

            $aCustomFields = $this->request()->get('customfield');

            // valid for custom field...
            $aCustomFieldsReq = $this->request()->get('customfield_req');
            if (!$aCustomFieldsReq) {
                $aCustomFieldsReq = array();
            }
            $aCusValidation = array();
            foreach ($aCustomFieldsReq as $key => $aReq) {
                $aCusValidation[$key] = _p("advancedmarketplace.afield_is_required", array("afield" => _p($aReq)));
            }
            // bad way to valid... :(
            $oCusValidator = clone Phpfox::getLib('validator');
            $oCusValidator = $oCusValidator->set(array(
                    'sFormName' => 'js_advancedmarketplace_form',
                    'aParams' => $aCusValidation
                )
            );

            $cFieldValid = $oCusValidator->isValid($aCustomFields);

            $submittedMethods = $aVals['payment_methods'];
            foreach ($paymentGateways as $key => $paymentGateway) {
                if (in_array($paymentGateway['gateway_id'], $submittedMethods)) {
                    $paymentGateways[$key]['checked'] = 1;
                } else {
                    $paymentGateways[$key]['checked'] = 0;
                }
            }

            ///valid for custom field...
            if ($cFieldValid && $oValidator->isValid($aVals)) {
                if ($bIsEdit) {
                    if (isset($aVals['draft_publish'])) {
                        $aVals['post_status'] = 1;
                    } else {
                        $aVals['time_stamp'] = PHPFOX_TIME;
                        $aVals['update_timestamp'] = PHPFOX_TIME;
                    }
                    if ($aListing['view_id'] == 1) {
                        unset($aVals['view_id']); // prevent cheating
                    }
                    if (Phpfox::getService('advancedmarketplace.process')->update($aListing['listing_id'], $aVals,
                        $aListing['user_id'], $aListing)) {
                        if ($aCustomFields) {
                            Phpfox::getService('advancedmarketplace.customfield.process')->frontend_updateCustomFieldData($aCustomFields,
                                $aListing['listing_id']);
                        }
                        $aCustom = $this->request()->get('custom');
                        if (!empty($aCustom)) {
                            phpfox::getService('advancedmarketplace.custom.process')->addCustomListing($aListing['listing_id'],
                                $aCustom);
                        }

                        (($sPlugin = Phpfox_Plugin::get('advancedmarketplace.component_controller_add_process_update_complete')) ? eval($sPlugin) : false);

                        switch ($this->request()->get('page_section_menu')) {
                            case 'js_mp_block_customize':
                                $params = array(
                                    'id' => $aListing['listing_id'],
                                    'tab' => 'customize'
                                );
                                if ($isCreating) {
                                    $params['tab'] = 'invite';
                                    $params['creating'] = 1;
                                }
                                $message = _p('successfully_uploaded_images');
                                break;
                            case 'js_mp_block_invite':
                                $params = array(
                                    'id' => $aListing['listing_id'],
                                    'tab' => 'invite'
                                );
                                if ($isCreating) {
                                    $params['tab'] = 'invite';
                                    $params['creating'] = 1;
                                }
                                $message = _p('successfully_invited_users');
                                break;
                            default:
                                $params = array(
                                    'id' => $aListing['listing_id'],
                                    'tab' => 'detail'
                                );
                                if ($isCreating) {
                                    $params['tab'] = 'customize';
                                    $params['creating'] = 1;
                                }
                                $message = _p('listing_successfully_updated');
                        }
                        $this->url()->send('advancedmarketplace.add', $params, $message);
                    }
                } else {
                    if (($iFlood = Phpfox::getUserParam('advancedmarketplace.flood_control_advancedmarketplace')) !== 0) {
                        $aFlood = array(
                            'action' => 'last_post', // The SPAM action
                            'params' => array(
                                'field' => 'time_stamp', // The time stamp field
                                'table' => Phpfox::getT('advancedmarketplace'), // Database table we plan to check
                                'condition' => 'user_id = ' . Phpfox::getUserId(), // Database WHERE query
                                'time_stamp' => $iFlood * 60 // Seconds);
                            )
                        );

                        // actually check if flooding
                        if (Phpfox::getLib('spam')->check($aFlood)) {
                            Phpfox_Error::set(_p('advancedmarketplace.you_are_creating_a_listing_a_little_too_soon') . ' ' . Phpfox::getLib('spam')->getWaitTime());
                        }
                    }

                    if (Phpfox_Error::isPassed()) {
                        if (isset($aVals['draft'])) {
                            $aVals['post_status'] = 2;
                        }

                        $aVals['user_id'] = Phpfox::getUserId();
                        $aFile = Phpfox::getService('core.temp-file')->get($aVals['temp_file']);

                        if (empty($aFile)) {
                            Phpfox_Error::set(_p('error_uploading_photo'));
                        } else {
                            if (!Phpfox::getService('user.space')->isAllowedToUpload($aVals['user_id'], $aFile['size'])) {
                                Phpfox::getService('core.temp-file')->delete($aVals['temp_file'], true);
                                Phpfox_Error::set(_p('you_are_out_of_space_to_upload_photo'));
                            }
                            Phpfox::getService('core.temp-file')->delete($aVals['temp_file']);
                            $aVals['image_path'] = $aFile['path'];
                            $aVals['server_id'] = $aFile['server_id'];
                        }

                        if ($iId = Phpfox::getService('advancedmarketplace.process')->add($aVals)) {
                            if ($aCustomFields) {
                                Phpfox::getService('advancedmarketplace.customfield.process')->frontend_updateCustomFieldData($aCustomFields, $iId);
                            }
                            $aCustom = $this->request()->get('custom');
                            if (!empty($aCustom)) {
                                phpfox::getService('advancedmarketplace.custom.process')->addCustomListing($iId, $aCustom);
                            }

                            $message = isset($aVals['draft']) ? _p('your_listing_is_created_as_a_draft_successfully') : _p('your_listing_is_created_successfully');

                            $this->url()->send('advancedmarketplace.add', array(
                                'id' => $iId,
                                'tab' => 'customize',
                                'creating' => 1,
                            ), $message);
                        }
                    }
                }
            }
        }

        $this->template()->assign(array(
            'payment_gateways' => $paymentGateways
        ));

        $aCurrencies = Phpfox::getService('core.currency')->get();
        foreach ($aCurrencies as $iKey => $aCurrency) {
            $aCurrencies[$iKey]['is_default'] = '0';

            if (Phpfox::getService('core.currency')->getDefault() == $iKey) {
                $aCurrencies[$iKey]['is_default'] = '1';
            }
        }

        $iTotalImage = 0;

        if ($bIsEdit) {
            $iTotalImage = Phpfox::getService('advancedmarketplace')->countImages($aListing['listing_id']);
            if ($isCreating) {
                $totalInvites = db()->select('COUNT(*)')
                    ->from(Phpfox::getT('advancedmarketplace_invite'))
                    ->where('listing_id = ' . (int)$aListing['listing_id'])
                    ->execute('getSlaveField');

                $aMenus = array(
                    'detail' => array(
                        'title' => _p('listing_info'),
                        'finished' => 1,
                        'enabled' => 1
                    ),
                    'customize' => array(
                        'title' => _p('manage_photos'),
                        'finished' => $iTotalImage > 1 ? 1 : 0,
                        'enabled' => 1
                    ),
                    'invite' => array(
                        'title' => _p('send_invites'),
                        'finished' => $totalInvites > 0 ? 1 : 0,
                        'enabled' => 1
                    ),
                );
                $this->buildStepsMenu('js_mp_block', $aMenus);
            } else {
                $aMenus['detail'] = _p('listing_info');
                $aMenus['customize'] = _p('manage_photos');
                $aMenus['invite'] = _p('send_invites');

                $this->template()->buildPageMenu('js_mp_block',
                    $aMenus,
                    array(
                        'link' => $this->url()->permalink('advancedmarketplace.detail',
                            isset($aListing['listing_id']) ? $aListing['listing_id'] : "", $aListing['title']),
                        'phrase' => _p('view_this_listing')
                    )
                );
            }
        } else {
            $aMenus = array(
                'detail' => array(
                    'title' => _p('listing_info'),
                    'finished' => 0,
                    'enabled' => 1
                ),
                'customize' => array(
                    'title' => _p('manage_photos'),
                    'finished' => 0,
                    'enabled' => 0
                ),
                'invite' => array(
                    'title' => _p('send_invites'),
                    'finished' => 0,
                    'enabled' => 0
                ),
            );

            $this->buildStepsMenu('js_mp_block', $aMenus);
        }

        $this->template()
            ->setPhrase(array(
                    'advancedmarketplace.you_can_upload_a_jpg_gif_or_png_file',
                    'core.name',
                    'core.status',
                    'core.in_queue',
                    'core.upload_failed_your_file_size_is_larger_then_our_limit_file_size',
                    'core.more_queued_than_allowed'
                )
            );

        $this->template()->setTitle((($bIsEdit && !$isCreating) ? _p('advancedmarketplace.editing_listing') . ': ' . $aListing['title'] : _p('advancedmarketplace.create_new_listing')))
            ->setBreadcrumb(_p('advancedmarketplace.advancedmarketplace'), $this->url()->makeUrl('advancedmarketplace'))
            ->setBreadcrumb((($bIsEdit && !$isCreating) ? _p('advancedmarketplace.editing_listing') . ': ' . $aListing['title'] : _p('advancedmarketplace.create_new_listing')),
                $this->url()->makeUrl('advancedmarketplace.add',
                    array('id' => isset($aListing['listing_id']) ? $aListing['listing_id'] : "")), true)
            ->setEditor()
            ->setPhrase(array(
                    'core.select_a_file_to_upload'
                )
            )
            ->setHeader(array(
                    'jscript/add.js' => 'app_p-advmarketplace',
                    'progress.js' => 'static_script',
                    '<script type="text/javascript"> var googleApiKey = "' . Phpfox::getParam('core.google_api_key') . '"; $Behavior.marketplaceProgressBarSettings = function(){ if ($Core.exists(\'#js_marketplace_form_holder\')) { oProgressBar = {holder: \'#js_marketplace_form_holder\', progress_id: \'#js_progress_bar\', uploader: \'#js_progress_uploader\', add_more: true, max_upload: ' . (int)Phpfox::getUserParam('advancedmarketplace.total_photo_upload_limit') . ', total: 1, frame_id: \'js_upload_frame\', file_id: \'image[]\'}; $Core.progressBarInit(); } }</script>',
                    'jscript/map.js' => 'app_p-advmarketplace',
                    'add.css' => 'app_p-advmarketplace',
                    'pager.css' => 'style_css',
                    'country.js' => 'module_core'
                )
            )
            ->assign(array(
                    'sType' => $this->request()->get('req3'),
                    'sType1' => $this->request()->get('req3'),
                    'sMyEmail' => Phpfox::getUserBy('email'),
                    'sCreateJs' => $oValidator->createJS(),
                    'sGetJsForm' => $oValidator->getJsForm(false),
                    'bIsEdit' => $bIsEdit,
                    'isCreating' => $isCreating,
                    'iMaxFileSize' => (Phpfox::getUserParam('advancedmarketplace.max_upload_size_listing') === 0 ? null : ((Phpfox::getUserParam('advancedmarketplace.max_upload_size_listing') / 1024) * 1048576)),
                    'iTotalImage' => $iTotalImage,
                    'iTotalImageLimit' => Phpfox::getUserParam('advancedmarketplace.total_photo_upload_limit'),
                    'iRemainUpload' => Phpfox::getUserParam('advancedmarketplace.total_photo_upload_limit') - $iTotalImage,
                    'aParamsUpload' => array(
                        'total_image' => $iTotalImage,
                        'total_image_limit' => Phpfox::getUserParam('advancedmarketplace.total_photo_upload_limit'),
                        'remain_upload' => Phpfox::getUserParam('advancedmarketplace.total_photo_upload_limit') - $iTotalImage
                    ),
                    'aCurrencies' => $aCurrencies,
                    'sUserSettingLink' => $this->url()->makeUrl('user.setting'),
                    'googleApiKey' => Phpfox::getParam('core.google_api_key'),
                    'categories' => Phpfox::getService('advancedmarketplace.category')->getTree(0),
                    'currentTab' => $this->request()->get('tab')
                )
            );
        (($sPlugin = Phpfox_Plugin::get('advancedmarketplace.component_controller_add_process')) ? eval($sPlugin) : false);
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
        (($sPlugin = Phpfox_Plugin::get('advancedmarketplace.component_controller_add_clean')) ? eval($sPlugin) : false);
    }
}
