<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

class Fundraising_Component_Controller_Add extends Phpfox_Component {

	private $_iMinPredefined = 2;
	private $_iMaxPredefined = 5;
	private $_sFriendMessageTemplate = '';
	private $_bIsEdit = false;
	private $_bCanEditPersonalData = true;
	private $_aCallback = false;
	private $_sModule = null;
	private $_iItem = null;
	private $_iMaxUpload = null;
	private $_iDefaultFundraising = null;
	private $_iDefaultMinFundraising = null;
	private $_iEditedCampaignId = null;
	private $_aImages = array();
	private $_aEditedCampaign = array();

	private function _initVariables() {
		$this->_iMinPredefined = 2;
		$this->_iMaxPredefined = 5;
		$this->_sFriendMessageTemplate = '';
		$this->_bIsEdit = false;
		$this->_bCanEditPersonalData = true;
		$this->_aCallback = false;
		$this->_sModule = $this->request()->get('module', false);
		$this->_iItem = $this->request()->getInt('item', false);
		$this->_iMaxUpload = Phpfox::getUserParam('fundraising.total_photo_upload_limit');
		$this->_iDefaultFundraising = Phpfox::getParam('fundraising.default_signature_goal');
		$this->_iDefaultMinFundraising = Phpfox::getParam('fundraising.default_min_fundraising');
	}

	private function _checkIsInPageAndPagePermission() {
		if ($this->_sModule !== false && $this->_iItem !== false) {
			/*
			 * @todo: implement below callback later
			 */
			if (($this->_aCallback = Phpfox::callback('fundraising.getFundraisingDetails', array('item_id' => $this->_iItem)))) {
				$this->template()->setBreadcrumb($this->_aCallback['breadcrumb_title'], $this->_aCallback['breadcrumb_home']);
				$this->template()->setBreadcrumb($this->_aCallback['title'], $this->_aCallback['url_home']);
				if ($this->_sModule == 'pages' && !Phpfox::getService('pages')->hasPerm($this->_iItem, 'fundraising.share_campaigns')) {
					return Phpfox_Error::display(_p('unable_to_view_this_item_due_to_privacy_settings'));
				}
			}
		}
	}

	private function _checkIsInEditCampaign() {
		if ($this->request()->getInt('id') && !isset($_POST['val']['add'])) {
			$this->_iEditedCampaignId = $this->request()->getInt('id');
			return true;
		} else {
			return false;
		}
	}

	/**
	 * this function will get and prepare neccesary data for edit form
	 * @by minhta
	 */
	private function _prepareEditForm() {
		$oCampaignService = Phpfox::getService('fundraising.campaign')->callback($this->_aCallback);
		if ($this->_aEditedCampaign = $oCampaignService->getCampaignForEdit($this->_iEditedCampaignId)) {

			if ($this->_aEditedCampaign['module_id'] != 'fundraising') {
				$this->_sModule = $this->_aEditedCampaign['module_id'];
				$this->_iItem = $this->_aEditedCampaign['item_id'];
			}

			// get tags of this campaign, currently fund rasising doesn't support tags
			if (Phpfox::isModule('tag')) {
				$aTags = Phpfox::getService('tag')->getTagsById('fundraising', $this->_aEditedCampaign['campaign_id']);
				if (isset($aTags[$this->_aEditedCampaign['campaign_id']])) {
					$this->_aEditedCampaign['tag_list'] = '';
					foreach ($aTags[$this->_aEditedCampaign['campaign_id']] as $aTag) {
						$this->_aEditedCampaign['tag_list'] .= ' ' . $aTag['tag_text'] . ',';
					}
					$this->_aEditedCampaign['tag_list'] = trim(trim($this->_aEditedCampaign['tag_list'], ','));
				}
			}


			// check permission to edit
			if (!(Phpfox::getService('fundraising.campaign')->hasAccess($this->_aEditedCampaign['campaign_id'], 'edit_own_campaign', 'edit_user_campaign'))) {
				//@todo: remove later
				//return false;
			}

			if (Phpfox::getUserParam('fundraising.edit_user_campaign') && Phpfox::getUserId() != $this->_aEditedCampaign['user_id']) {
				$this->_bCanEditPersonalData = false;
			}

			$this->_bIsEdit = true;

			if($this->_aEditedCampaign['end_time'])
			{
				$this->_aEditedCampaign['expired_time_month'] = date('n', $this->_aEditedCampaign['end_time']);
				$this->_aEditedCampaign['expired_time_day'] = date('j', $this->_aEditedCampaign['end_time']);
				$this->_aEditedCampaign['expired_time_year'] = date('Y', $this->_aEditedCampaign['end_time']);
			}
			else
			{
				$this->_aEditedCampaign['expired_time_month'] = date('n', PHPFOX_TIME);
				$this->_aEditedCampaign['expired_time_day'] = date('j', PHPFOX_TIME);
				$this->_aEditedCampaign['expired_time_year'] = date('Y', PHPFOX_TIME);
			}
            $this->_aEditedCampaign['financial_goal'] = ($this->_aEditedCampaign['financial_goal'] == null) ? 0 : $this->_aEditedCampaign['financial_goal'];

			$sTab = $this->request()->get('tab');

			$this->_aImages = Phpfox::getService('fundraising.image')->getImagesOfCampaign($this->_aEditedCampaign['campaign_id'], 100);
			$this->_iMaxUpload = $this->_iMaxUpload - count($this->_aImages);

			$this->template()->assign(array(
                'aParamsUpload' => array(
                    'id' => $this->_iEditedCampaignId,
                    'remain_upload' => $this->_iMaxUpload,
                ),
                'sMainImage' => !empty($this->_aEditedCampaign['image_path']) ? $this->_aEditedCampaign['image_path'] : '',
                'aImages' => $this->_aImages,
				'iTotalImage' => count($this->_aImages),
                'aForms' => $this->_aEditedCampaign,
				'sTab' => $sTab))
					->setHeader('cache', array(
						'<script type="text/javascript">$Behavior.funraisingEditCategory = function(){var aCategories = explode(\',\', \'' . $this->_aEditedCampaign['categories'] . '\'); for (i in aCategories) { $(\'#js_mp_holder_\' + aCategories[i]).show(); $(\'#js_mp_category_item_\' + aCategories[i]).attr(\'selected\', true); }}</script>'
						));

			(($sPlugin = Phpfox_Plugin::get('fundraising.component_controller_add_process_edit')) ? eval($sPlugin) : false);
		} else {
			Phpfox_Error::set(_p('unable_to_find_the_fundraising_you_are_trying_to_edit'));
		}
	}

	private function _checkIfHavingPermissionToAddOrEditCampaign() {
		if ($this->_sModule && $this->_iItem && Phpfox::hasCallback($this->_sModule, 'viewFundraising')) {
			$this->_aCallback = Phpfox::callback($this->_sModule . '.viewFundraising', $this->_iItem);
			$this->template()->setBreadcrumb($this->_aCallback['breadcrumb_title'], $this->_aCallback['breadcrumb_home']);
			$this->template()->setBreadcrumb($this->_aCallback['title'], $this->_aCallback['url_home']);
			if ($this->_sModule == 'pages' && !Phpfox::getService('pages')->hasPerm($this->_iItem, 'fundraising.share_campaigns')) {
				return false;
			}
		}

		return true;
	}

	private function _getValidationParams($aVals = array()) {
            $aParam = array(
                'title' => array(
                    'def' => 'required',
                    'title' => _p('fill_title_for_fundraising'),
                ),
                'short_description' => array(
                    'def' => 'required',
                    'title' => _p('add_description_to_fundraising'),
                ),
                'financial_goal' => array(
                    'def' => 'money',
                    'title' => _p('fill_in_a_fundraising_goal_for_your_fundraising'),
                ),
                'minimum_amount' => array(
                    'def' => 'money',
                    'title' =>  _p('minimum_donation'),
                ),
                'location_venue' => array(
                    'def' => 'required',
                    'title' => _p('fill_in_campaign_location'),
                ),
            );

        if(isset($aVals['publish_video'])
            || isset($aVals['submit_sponsor_levels']) || isset($aVals['publish_sponsor_levels'])
			|| isset($aVals['submit_video'])
			|| isset($aVals['submit_gallery']) || isset($aVals['publish_gallery']) || isset($aVals['gallery_next'])
            || isset($aVals['submit_contact_information']) || isset($aVals['publish_contact_information'])
            || isset($aVals['submit_email_conditions']) || isset($aVals['publish_email_conditions'])
			||  isset($aVals['submit_invite'])) {
            $aParam = array();
        }

		return $aParam;
	}

	private function _checkIfSubmittingAForm() {
		if ($this->request()->getArray('val')) {
			return true;
		} else {
			return false;
		}
	}

	private function _checkIfHavingSpamActionAndHandleIt() {
		if (($iFlood = Phpfox::getUserParam('fundraising.flood_control_fundraising')) !== 0 && !$this->_bIsEdit) {
			$aFlood = array(
				'action' => 'last_post', // The SPAM action
				'params' => array(
					'field' => 'time_stamp', // The time stamp field
					'table' => Phpfox::getT('fundraising_campaign'), // Database table we plan to check
					'condition' => 'user_id = ' . Phpfox::getUserId(), // Database WHERE query
					'time_stamp' => $iFlood * 60 // Seconds);	
				)
			);

			// actually check if flooding
			if (Phpfox::getLib('spam')->check($aFlood)) {
				Phpfox_Error::set(_p('your_are_posting_a_little_too_soon') . ' ' . Phpfox::getLib('spam')->getWaitTime());
			}
		}
	}

	/**
	 * after creating a campaign, we need to navigate user to next step, step by step
	 *
	 * @by minhta
	 * @param array $aVals form varible
	 * @return
	 */
	private function _checkJustAddACampaignToNavigateStepByStep($aVals,$iId) {

		if ($this->request()->get('req3') == 'setup') {
			$sMessage = "";
            if (isset($aVals['update']) || isset($aVals['draft_update'])) {
                $sMessage = _p('fundraising_updated');
            } else if (isset($aVals['submit_video'])) {
				$sMessage = _p('successfully_added_video_to_your_campaign');
			} else if (isset($aVals['submit_gallery'])) {
				$sMessage = _p('photos_and_video_successfully_updated');
			} else if (isset($aVals['submit_sponsor_levels'])) {
				$sMessage = _p('sponsor_level_successfully_updated_2');
			} else if (isset($aVals['submit_contact_information'])) {
				$sMessage = _p('contact_information_successfully_updated');
			} else if (isset($aVals['submit_email_conditions'])) {
                $sMessage = _p('email_conditions_successfully_updated');
            } else if (isset($aVals['submit_invite'])){
                $sMessage = _p('successfully_invited_users');
            }

            $aSendParam['id'] = $iId;
            if (isset($aVals['update']) || isset($aVals['draft_update'])) {
                $aSendParam['tab'] = 'gallery';

            } else if (isset($aVals['submit_video']) || isset($aVals['submit_gallery'])) {
                $aSendParam['tab'] = 'gallery';

            } else if (isset($aVals['gallery_next'])) {
				$aSendParam['tab'] = 'contact_information';

			} else if (isset($aVals['submit_sponsor_levels'])) {
                $aSendParam['tab'] = 'contact_information';

            } else if (isset($aVals['submit_contact_information'])) {
                $aSendParam['tab'] = 'email_conditions';

            } if (isset($aVals['submit_email_conditions'])) {
                $aSendParam['tab'] = 'invite_friends';

            } else if (isset($aVals['submit_invite'])) {
                    $aSendParam['tab'] = 'invite_friends';
            }

			if($this->_sModule == 'pages')
			{
				$aSendParam['module'] = $this->_sModule;
				$aSendParam['item'] = $this->_iItem;
			}
			$this->url()->send('fundraising.add.setup', $aSendParam, $sMessage);
		}
	}

	private function _updateCampaign($aVals) {
		//	check inactive fundraising
		if ((isset($aVals['draft_publish'])
					|| isset($aVals['publish_video'])
					|| isset($aVals['publish_sponsor_levels'])
					|| isset($aVals['publish_contact_information'])
					|| isset($aVals['publish_email_conditions'])
				)
				&& isset($this->_aEditedCampaign['is_active']) && $this->_aEditedCampaign['is_active'] == '0') {
			$this->url()->send('fundraising.error', array('status' => Phpfox::getService('fundraising')->getErrorStatusNumber('cannot_publish_because_inactive')));
		}


        $iId = 0;
		// choose action base on the value of submit button

		if (isset($aVals['update']) || isset($aVals['draft_update']) || isset($aVals['draft_publish'])) {

			$iId = Phpfox::getService('fundraising.campaign.process')->update($aVals, $this->_aEditedCampaign);

		} else if (isset($aVals['submit_gallery']) || isset($aVals['submit_video']) || isset($aVals['publish_video']) || isset($aVals['gallery_next'])) {

			$iId = Phpfox::getService('fundraising.campaign.process')->updateImagesAndVideos($aVals, $this->_aEditedCampaign);
			$aSendParam['tab'] = 'gallery';
			
		} else if (isset($aVals['submit_sponsor_levels']) || isset($aVals['publish_sponsor_levels'])) {

			$iId = Phpfox::getService('fundraising.campaign.process')->updateSponsorLevels($aVals, $this->_aEditedCampaign);
			$aSendParam['tab'] = 'sponsor_levels';

		} else if (isset($aVals['submit_contact_information']) || isset($aVals['publish_contact_information'])) {
			
			$iId = Phpfox::getService('fundraising.campaign.process')->updateContactInformation($aVals, $this->_aEditedCampaign);
			$aSendParam['tab'] = 'contact_information';

		} else if (isset($aVals['submit_email_conditions']) || isset($aVals['publish_email_conditions'])) {
			if(empty($aVals['term_condition']))
			{
				return Phpfox_Error::set(_p('add_terms_and_conditions_to_your_campaign'));
			}
            $iId = Phpfox::getService('fundraising.campaign.process')->updateEmailConditions($aVals, $this->_aEditedCampaign);
            $aSendParam['tab'] = 'email_conditions';

        } else if (isset($aVals['submit_financial_configuration'])) {

			$iId = Phpfox::getService('fundraising.campaign.process')->updateFinancialConfiguration($aVals, $this->_aEditedCampaign);
			$aSendParam['tab'] = 'financial_configuration';

		} else if (isset($aVals['submit_invite'])) {
			$iId = Phpfox::getService('fundraising.campaign.process')->inviteFriends($aVals, $this->_aEditedCampaign);
			$aSendParam['tab'] = 'invite_friends';

		}

        if(isset($aVals['draft_publish']) || isset($aVals['publish_video'])
            || isset($aVals['publish_sponsor_levels']) || isset($aVals['publish_contact_information'])
            || isset($aVals['publish_email_conditions']))
            $this->url()->send('fundraising', array( $this->_aEditedCampaign['campaign_id'], $this->_aEditedCampaign['title']));

		if ($iId) {
			$aSendParam['id'] = $iId;
			if($this->_sModule == 'pages')
			{
				$aSendParam['module'] = $this->_sModule;
				$aSendParam['item'] = $this->_iItem;
			}
           	$sMessage = _p('fundraising_updated');

			$this->_checkJustAddACampaignToNavigateStepByStep($aVals,$iId);

			$this->url()->send('fundraising.add', $aSendParam, $sMessage);
		} else {
			$this->_iMaxUpload = Phpfox::getUserParam('fundraising.total_photo_upload_limit');
			$this->_aImages = Phpfox::getService('fundraising')->getImages($this->_aEditedCampaign['campaign_id'], 1000);
			$this->_iMaxUpload = $this->_iMaxUpload - count($this->_aImages);
		}
	}

	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process() {
		Phpfox::getService('fundraising')->buildMenu();
		Phpfox::isUser(true);
		$this->_initVariables();
		$this->_checkIsInPageAndPagePermission();
		if ($this->_checkIsInEditCampaign()) {
			//check edit permission here
			if(!Phpfox::getService('fundraising.permission')->canEditCampaign($this->_iEditedCampaignId, Phpfox::getUserId()))
			{
				$this->url()->send('fundraising.error', array('status' => Phpfox::getService('fundraising')->getErrorStatusNumber('invalid_permission')));
			}
			$this->_prepareEditForm();
		} else {
			Phpfox::getUserParam('fundraising.add_new_campaign', true);
		}

		if (!$this->_checkIfHavingPermissionToAddOrEditCampaign()) {
			return Phpfox_Error::display(_p('unable_to_view_this_item_due_to_privacy_settings'));
		}

        $aValidationParam = $this->_getValidationParams();

        $oValid = Phpfox::getLib('validator')->set(array(
                'sFormName' => 'ynfr_edit_campaign_form',
                'aParams' => $aValidationParam
            )
        );

		if ($this->_checkIfSubmittingAForm()) {

			$aVals = $this->request()->getArray('val');
			$aVals['invite'] = $this->request()->getArray('friend');
			$this->_iDefaultFundraising = isset($aVals['fundraising_goal']) ? $aVals['fundraising_goal'] : 0;
			$this->_iDefaultMinFundraising = isset($aVals['minimum']) ? $aVals['minimum'] : 0;

            $aValidationParam = $this->_getValidationParams($aVals);

            $oValid = Phpfox::getLib('validator')->set(array(
                    'sFormName' => 'ynfr_edit_campaign_form',
                    'aParams' => $aValidationParam
                )
            );

			if ($oValid->isValid($aVals)) {
				// Add the new fundraising

				if (isset($aVals['victory'])) {
					$aVals['campaign_status'] = 3;
					$sMessage = _p('fundraising_successfully_saved');
				} else if (isset($aVals['closed'])) {
					$aVals['campaign_status'] = 1;
					$sMessage = _p('your_fundraising_has_been_closed');
				} else {
					$sMessage = _p('your_fundraising_has_been_added');
				}

				$this->_checkIfHavingSpamActionAndHandleIt();

				if (Phpfox_Error::isPassed()) {
					if ($this->_sModule && $this->_iItem && !$this->_bIsEdit) {
						$aVals['module_id'] = $this->_sModule;
						$aVals['item_id'] = $this->_iItem;
					}

					$aSendParam = array();
					if ($this->_sModule)
						$aSendParam['module'] = $this->_sModule;
					if ($this->_iItem)
						$aSendParam['item'] = $this->_iItem;
					// Update a fundraising
					if ($this->_bIsEdit) {
						// Update the fundraising
						$this->_updateCampaign($aVals);
						
					} else if ($iId = Phpfox::getService('fundraising.campaign.process')->add($aVals)) { //Add new fundraising
						//resend to this controller and set tab to photos
						$aSendParam['id'] = $iId;
						$aSendParam['tab'] = 'gallery';
						$this->url()->send('fundraising.add.setup', $aSendParam, _p('your_fundraising_has_been_added'));
					}
				}
			}
		}
		$aCategories = Phpfox::getService('fundraising.category')->getCategories();

		$this->template()->setTitle(($this->_bIsEdit ? _p('editing_fundraising') . ': ' . $this->_aEditedCampaign['title'] : _p('adding_a_new_fundraising')))
				->setBreadcrumb(_p('fundraisings'), ($this->_aCallback === false ? $this->url()->makeUrl('fundraising') : $this->url()->makeUrl($this->_aCallback['url_home_pages'])))
				->setBreadcrumb(($this->_bIsEdit ? _p('editing_fundraising') . ': ' . Phpfox::getLib('parse.output')->shorten($this->_aEditedCampaign['title'], Phpfox::getService('core')->getEditTitleSize(), '...') : _p('adding_a_new_fundraising')), ($this->_iEditedCampaignId > 0 ? ($this->_aCallback == false ? $this->url()->makeUrl('fundraising', array('add', 'id' => $this->_iEditedCampaignId)) : $this->url()->makeUrl('fundraising', array('add', 'id' => $this->_iEditedCampaignId, 'module' => $this->_aCallback['module_id'], 'item' => $this->_aCallback['item_id'])) ) : ($this->_aCallback == false ? $this->url()->makeUrl('fundraising', array('add')) : $this->url()->makeUrl('fundraising', array('add', 'module' => $this->_aCallback['module_id'], 'item' => $this->_aCallback['item_id'])))), true)
				->assign(array(
					'sCreateJs' => $oValid->createJS(),
					'sGetJsForm' => $oValid->getJsForm(),
					'sModule' => ($this->_aCallback !== false ? $this->_sModule : ''),
					'iItem' => ($this->_aCallback !== false ? $this->_iItem : ''),
					'bIsEdit' => $this->_bIsEdit,
					'bCanEditPersonalData' => $this->_bCanEditPersonalData,
					'aCategories' => $aCategories,
					'iMaxUpload' => $this->_iMaxUpload,
					'iMaxPredefined' => $this ->_iMaxPredefined,
					'iMinPredefined' => $this -> _iMinPredefined,
					'iDefaultFundraising' => $this->_iDefaultFundraising,
					'iDefaultMinFundraising' => $this->_iDefaultMinFundraising,
					'aTempPredefined' => array('', ''),
                    'aCurrentCurrencies' => Phpfox::getService('fundraising')->getCurrentCurrencies('paypal', $this->_bIsEdit ? $this->_aEditedCampaign['currency'] : ''),
					'iMaxFileSize' => (Phpfox::getUserParam('fundraising.max_upload_size_fundraising') === 0 ? null : Phpfox::getLib('phpfox.file')->filesize((Phpfox::getUserParam('fundraising.max_upload_size_fundraising') / 1024) * 1048576)),
					'googleApiKey' => Phpfox::getParam('core.google_api_key')
						)
				)
				->setEditor(array('wysiwyg' => true))
				->setHeader('cache', array(
					'map.js' => 'module_fundraising',
					'add.js' => 'module_fundraising',
					'jquery/plugin/jquery.highlightFade.js' => 'static_script',
					'switch_legend.js' => 'static_script',
					'switch_menu.js' => 'static_script',
					'quick_edit.js' => 'static_script',
					'pager.css' => 'style_css',
					'progress.js' => 'static_script',
					'country.js' => 'module_core',
					'ynfundraising.js' => 'module_fundraising',
					'jquery.validate.js' => 'module_fundraising'
				)
		);

		$this->template()->setPhrase(array(
			'you_reach_the_maximum_of_total_predefined',
			'you_must_have_a_minimum_of_total_predefined',
			'signature_goal_must_be_a_integer_number',
			'provide_a_valid_email_address',
			'this_field_is_required',
			'please_enter_a_valid_number',
			'please_enter_a_valid_email',
			'please_enter_a_valid_url',
            'select_all',
            'un_select_all'
		));
		if (Phpfox::isModule('attachment')) {
           $this->setParam('attachment_share', array(
					'type' => 'fundraising',
					'id' => 'ynfr_edit_campaign_form',
					'edit_id' => ($this->_bIsEdit ? $this->_iEditedCampaignId : 0),
				)
			);
        }
		if($this->_bIsEdit)
		{
			// Build menus
            $aMenus = array(
                'main' => _p('main_info'),
                'gallery' => _p('gallery'),
                'contact_information' => _p('contact_information'),
                'email_conditions' => _p('email_and_conditions'),
                'invite_friends' => _p('invite_friends'),
            );
            $this->template()->buildPageMenu('js_fundraising_block',
                $aMenus,
                array(
                    'link' => $this->url()->permalink('fundraising', $this->_aEditedCampaign['campaign_id'], $this->_aEditedCampaign['title']),
                    'phrase' => _p('view_this_campaign')
                )
            );

			$this->template()->assign(array(
                'iCampaignId' => $this->_iEditedCampaignId
			));
			$this -> setParam('iCampaignId', $this->_iEditedCampaignId);
			$this -> setParam('setup', $this->request()->get('req3'));
		}
		(($sPlugin = Phpfox_Plugin::get('fundraising.component_controller_add_process')) ? eval($sPlugin) : false);
	}

	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean() {
		$this->template()->clean(array(
			'bIsEdit',
			'aCategories'
				)
		);
		(($sPlugin = Phpfox_Plugin::get('fundraising.component_controller_add_clean')) ? eval($sPlugin) : false);
	}

}

?>
