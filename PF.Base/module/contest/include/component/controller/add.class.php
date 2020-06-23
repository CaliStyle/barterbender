<?php

defined('PHPFOX') or exit('NO DICE!');

class Contest_Component_Controller_Add extends Phpfox_Component{


	private function _checkIfSubmittingAForm() {
		if ($this->request()->getArray('val')) {
			return true;
		} else {
			return false;
		}
	}

	private function _checkIsInEditContest() {
		if ($this->request()->getInt('id')) {
			$iEditedContestId = $this->request()->getInt('id');
			return $iEditedContestId;
		} else {
			return null;
		}
	}

	private function _adaptContestDataWithFormData($aContest)
	{
		$aContest['yn_contest_add_description'] = $aContest['description'];
		$aContest['award'] = $aContest['award_description'];
		$aContest['maximum_entry'] = $aContest['number_entry_max'];
        $aContest['num_winning_entry'] = $aContest['number_winning_entry_max'];
        $aContest['vote_without_join'] = ($aContest['vote_without_join']==1) ? 1 : -1;

        $aTimeLine = Phpfox::getService('contest.constant')->getTimeLine();
        
        foreach ($aTimeLine as $k => $sTimeLine)
        {
            $aContest[$sTimeLine] = Phpfox::getService('contest.helper')->convertToUserTimeZone($aContest[$sTimeLine]);
            
            $aContest[$sTimeLine.'_day'] = date('j', $aContest[$sTimeLine]);
    		$aContest[$sTimeLine.'_month'] = date('n', $aContest[$sTimeLine]);
    		$aContest[$sTimeLine.'_year'] = date('Y', $aContest[$sTimeLine]);
    		$aContest[$sTimeLine.'_hour'] = date('H', $aContest[$sTimeLine]);
    		$aContest[$sTimeLine.'_minute'] = date('i', $aContest[$sTimeLine]);
        }
        
		return $aContest;
	}

	private function _adaptSubmitValsWithFormData($aVals)
	{
        $aVals['type'] = Phpfox::getService('contest.constant')->getContestTypeIdByTypeName($aVals['contest_type']);
        $aVals['vote_without_join'] = isset($aVals['vote_without_join']) ? 1 : -1;
        $aVals['is_auto_approve'] = isset($aVals['automatic_approve']) ? 1 : 0;
        
		return $aVals;
	}

	public function _buildTabMenu($aEditedContest)
	{
		$aMenus = array(
			'main' => _p('contest.main_info'),
			'email_conditions' => _p('contest.email'),
			'invite_friends' => _p('contest.invite_friends'),
			// 'settings' => _p('contest.settings')
			);

		$this->template()->buildPageMenu('js_contest_block', $aMenus, array(
				'link' => $this->url()->permalink('contest', $aEditedContest['contest_id'], $aEditedContest['contest_name']),
				'phrase' => _p('contest.view_this_contest')
					)
			);

	}

	public function _checkIfPublishingAContest($aVals)
	{
		
	}

	private function navigateAfterSubmitAContest($aVals,$iId) {

		if ($this->request()->get('req3') == 'setup') {

            if (isset($aVals['update']) || isset($aVals['draft_update'])) {
                $sMessage = _p('fundraising.fundraising_updated');
            } else if (isset($aVals['submit_video'])) {
				$sMessage = _p('fundraising.successfully_added_video_to_your_campaign');
			} else if (isset($aVals['submit_gallery'])) {
				$sMessage = _p('fundraising.photos_and_video_successfully_updated');
			} else if (isset($aVals['submit_sponsor_levels'])) {
				$sMessage = _p('fundraising.sponsor_level_successfully_updated_2');
			} else if (isset($aVals['submit_contact_information'])) {
				$sMessage = _p('fundraising.contact_information_successfully_updated');
			} else if (isset($aVals['submit_email_conditions'])) {
                $sMessage = _p('fundraising.email_conditions_successfully_updated');
            } else if (isset($aVals['submit_invite'])){
                $sMessage = _p('fundraising.successfully_invited_users');
            }

            $aSendParam['id'] = $iId;
            if (isset($aVals['update']) || isset($aVals['draft_update'])) {
                $aSendParam['tab'] = 'gallery';

            } else if (isset($aVals['submit_video']) || isset($aVals['submit_gallery'])) {
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

	private function _navigateAfterUpdateAContest($aVals, $iContestId)
	{
		$sMessage = '';
		if(isset($aVals['save_email_condition']))
		{
			$aSendParam['tab'] = 'email_conditions';
			$sMessage = _p('email_successfully_updated');
		}

		if(isset($aVals['save_as_draft']) || isset($aVals['save']))
		{
			$aSendParam['tab'] = 'main';
			$sMessage = _p('main_information_successfully_updated');
		}

		if(isset($aVals['submit_invite']))
		{
			$aSendParam['tab'] = 'invite_friends';
			$sMessage = _p('friend_invitations_successfully_sent');
		}

		if(isset($aVals['save_settings']))
		{
			$aSendParam['tab'] = 'settings';
			$sMessage = _p('setting_successfully_updated');
		}
		
		$aSendParam['id'] = $iContestId;

		$this->url()->send('contest.add', $aSendParam, $sMessage);
	}

	private function _checkIfHavingPermissionToAddOrEditContest() {
		if($iEditedId = $this->_checkIsInEditContest())
		{
			return Phpfox::getService('contest.permission')->canEditContest($iEditedId, Phpfox::getUserId());
		}
		else
		{
			return Phpfox::getService('contest.permission')->canCreateContest();
		}	
	}		


	private function _checkIfAFormWithPublishRequestAndNavigate($aVals, $iContestId)
	{
		// if publish button is pressed and this contest was already published 
		// it means we need to navigate it too publish successfully page with proper register popup
		
		
		if(isset($aVals['publish_contest']) && $aVals['publish_contest'])
		{
			$this->url()->send('contest.add', array('id' => $iContestId, 'publish' => 1));
		}
	}

	public function process ()
	{

		if(!Phpfox::isModule('blog') && !Phpfox::isModule('v') && !Phpfox::isModule('videochannel') && !Phpfox::isModule('photo') && !Phpfox::isModule('advancedphoto') && !Phpfox::isModule('ultimatevideo'))
		{
			$this->url()->send('contest', NULL, 'Please turn on at least blog, video or photo module to add a contest' );
		}

		//check permission
		if (!$this->_checkIfHavingPermissionToAddOrEditContest()) {
			if($iTempContestId = $this->_checkIsInEditContest())
			{
				$aContest = Phpfox::getService('contest.contest')->getContestById($iTempContestId);
				if($aContest['contest_status'] == Phpfox::getService('contest.constant')->getContestStatusIdByStatusName('pending'))
				{
					$this->url()->send('contest.error', array('status' => Phpfox::getService('contest.constant')->getErrorStatusNumber('contest_pending')));
				}
			}

			$this->url()->send('contest.error', array('status' => Phpfox::getService('contest.constant')->getErrorStatusNumber('invalid_permission')));

			
		}

		$bIsEdit = false;
		$bShouldDisable = false;

		// Handle submit form
		if($this->_checkIfSubmittingAForm())
		{
			$aVals = $this->request()->getArray('val');
			if($iEditedContestId = $this->_checkIsInEditContest())
			{
				if($iId = Phpfox::getService('contest.contest.process')->update($aVals, $iEditedContestId)) { 
					// nivigate to main info form if is is publish request
					$this->_checkIfAFormWithPublishRequestAndNavigate($aVals, $iEditedContestId);

					//navigate to expected tabs
					$this->_navigateAfterUpdateAContest($aVals, $iEditedContestId);
				}
			}
			else
			{

				if($iId = Phpfox::getService('contest.contest.process')->add($aVals)) { //Add new contest
				//resend to this controller and set tab to photos
					$this->_checkIfAFormWithPublishRequestAndNavigate($aVals, $iId);
					$this->url()->send('contest', $iId,'');
				}
            	else
                {
                    $this->template()->assign('aForms', $this->_adaptSubmitValsWithFormData($aVals));
                    if (!empty($aVals['category']))
                    {
                        $sCategories = implode(',', $aVals['category']);
                        $this->template()->setHeader('cache', array(
    						'<script type="text/javascript">$Behavior.fcontestAdd = function() { var aCategories = explode("," , "' . $sCategories . '"); for (i in aCategories) { $("#js_mp_holder_" + aCategories[i]).show(); $("#js_mp_category_item_" + aCategories[i]).attr("selected", true); } }</script>'
    				    ));
                    }
                }
			}
		}


		// Handle status of add/edit action
		if($iEditedContestId = $this->_checkIsInEditContest())
		{
			$bIsEdit = true;
			$aEditedContest = Phpfox::getService('contest.contest')->getContestById($iEditedContestId);

			$this->_buildTabMenu($aEditedContest);

			if($aEditedContest['contest_status'] != Phpfox::getService('contest.constant')->getContestStatusIdByStatusName('draft') && $aEditedContest['contest_status'] != Phpfox::getService('contest.constant')->getContestStatusIdByStatusName('denied'))
			{
				$bShouldDisable = true;
			}

			$sTab = $this->request()->get('tab');

			$this->template()->assign(
					array(
						'aForms' => $this->_adaptContestDataWithFormData($aEditedContest),
						'sTab' => $sTab
						)
					)
				->setHeader('cache', array(
						'<script type="text/javascript">$Behavior.contestEditCategory = function(){var aCategories = explode(\',\', \'' . $aEditedContest['categories'] . '\'); for (i in aCategories) { $(\'#js_mp_holder_\' + aCategories[i]).show(); $(\'#js_mp_category_item_\' + aCategories[i]).attr(\'selected\', true); }}</script>'
				    ));

			$this->template()->setTitle(_p('contest.editing_contest'). ': ' . $aEditedContest['contest_name'] )
			->setBreadcrumb(_p('contest.editing_contest') . ': ' . Phpfox::getLib('parse.output')->shorten($aEditedContest['contest_name'], Phpfox::getService('core')->getEditTitleSize(), '...') ,  $this->url()->makeUrl('contest', array('add', 'id' => $iEditedContestId)) , true);
		}
		else
		{

			$this->template()->setTitle( _p('contest.start_a_contest'))
		->setBreadcrumb(_p('contest.start_a_contest'), $this->url()->makeUrl('contest', array('add')), true);
		}

		
		$aCategories = Phpfox::getService('contest.category')->getCategories();

		// do some general things here
		$this->template()->setBreadcrumb(_p('contest.contests'), $this->url()->makeUrl('contest'));
		

		$this->template()->assign(
			array(
				'sMaxFileSize' => Phpfox::getService('contest.helper')->getMaxImageFileSize(),
				'bIsEdit' => $bIsEdit,
				'aCategories' => $aCategories,
				'sCategories' => Phpfox::getService('contest.category')->get(),
				'aContestStatus' => Phpfox::getService('contest.constant')->getAllContestStatus(),
				'bShouldDisable' => $bShouldDisable,
				'aContestTypes' => Phpfox::getService('contest.constant')->getAllContestTypes(),
				'bIsHavingPublish' => $this->request()->get('publish') ? true : false
				)
			);

		$this->template()->setEditor()->setHeader(
			array(
				'yncontest.js' => 'module_contest',
				'jquery.validate.js' => 'module_contest',
				'magnific-popup.css' => 'module_contest',
                'jquery.magnific-popup.js' => 'module_contest',
				)
			);

		// set validator js phrases
		$aValidatorPhrases = Phpfox::getService('contest.helper')->getPhrasesForValidator();
		$this->template()->setPhrase($aValidatorPhrases);
		$this->template()->setPhrase(['contest.warning_before_publishing','core.yes','core.no']);
		if (Phpfox::isModule('attachment'))
		{
			$this->setParam('attachment_share', array(
				'type' => 'contest',
				'id' => 'yncontest_main_info_form',
				'edit_id' => ($iEditedContestId ? $iEditedContestId : 0)
				)
			);
		}

       	Phpfox::getService('contest.helper')->buildMenu();

	}
}