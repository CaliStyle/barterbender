<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

class Fundraising_Component_Controller_View extends Phpfox_Component {

	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process() {

        // check if form invite is submit here , get value continue after take this detail campaign
        if ($this->request()->getArray('val')) {
            $aVals = $this->request()->getArray('val');
        }

		if ($this->request()->getInt('id')) {
			return Phpfox::getLib('module')->setController('error.404');
		}

		if (Phpfox::isUser() && Phpfox::isModule('notification')) {
			Phpfox::getService('notification.process')->delete('comment_fundraising', $this->request()->getInt('req2'), Phpfox::getUserId());
			Phpfox::getService('notification.process')->delete('fundraising_notice_follower', $this->request()->getInt('req2'), Phpfox::getUserId());
			Phpfox::getService('notification.process')->delete('fundraising_invited', $this->request()->getInt('req2'), Phpfox::getUserId());
			Phpfox::getService('notification.process')->delete('fundraising_like', $this->request()->getInt('req2'), Phpfox::getUserId());
		}
		$aCallback = $this->getParam('aCallback', false);
		$iCampaignId = $this->request()->getInt(($aCallback !== false ? $aCallback['request'] : 'req2'));
		Phpfox::getService('fundraising.campaign.process')->checkAndUpdateStatusOfACampaign($iCampaignId);
		if(!Phpfox::getService('fundraising.permission')->canViewBrowseCampaign($iCampaignId, Phpfox::getUserId()))
		{
			$this->url()->send('fundraising.error', array('status' => Phpfox::getService('fundraising')->getErrorStatusNumber('invalid_permission')));
		}

		(($sPlugin = Phpfox_Plugin::get('fundraising.component_controller_view_process_start')) ? eval($sPlugin) : false);

		$bIsProfile = $this->getParam('bIsProfile');
		if ($bIsProfile === true) {
			$this->setParam(array(
				'bViewProfileFundraising' => true,
				'sTagType' => 'fundraising'
					)
			);
		}

		$aItem = Phpfox::getService('fundraising.campaign')->callback($aCallback)->getCampaignById($iCampaignId);
		if (Phpfox::getUserParam('fundraising.can_active_campaign') == false 
				&& isset($aItem['user_id']) && $aItem['user_id'] != Phpfox::getUserId()
				&& isset($aItem['is_active']) && $aItem['is_active'] == '0') {
			$this->url()->send('fundraising.error', array('status' => Phpfox::getService('fundraising')->getErrorStatusNumber('cannot_view_because_inactive')));
		}

		$aItem = Phpfox::getService('fundraising.campaign')->retrieveMoreInfoFromCampaign($aItem,  $bRetrievePermission = true);

        //begin invite friend after get this detail campaign
        if (isset($aVals['submit_invite'])) {
            Phpfox::getService('fundraising.campaign.process')->inviteFriends($aVals, $aItem);
        }

		Phpfox::getService('fundraising.campaign.process')->updateViewCounter($aItem['campaign_id']);

		if (!isset($aItem['campaign_id'])) {
			return Phpfox_Error::display(_p('fundraising_not_found'));
		}


		if (Phpfox::getUserId() == $aItem['user_id'] && Phpfox::isModule('notification')) {
			Phpfox::getService('notification.process')->delete('fundraising_approved', $this->request()->getInt('req2'), Phpfox::getUserId());
		}

		if (Phpfox::isModule('privacy')) {
			Phpfox::getService('privacy')->check('fundraising', $aItem['campaign_id'], $aItem['user_id'], $aItem['privacy'], $aItem['is_friend']);
		}

		if (!Phpfox::getUserParam('fundraising.can_approve_campaigns')) {
			if ($aItem['is_approved'] != '1' && $aItem['user_id'] != Phpfox::getUserId()) {
				return Phpfox_Error::display(_p('fundraising_not_found'));
			}
		}

        $aTitleLabel = [
            'type_id' => 'fundraising'
        ];

        if (!empty($aItem['is_featured'])) {
            $aTitleLabel['label']['featured'] =[
                'title' => '',
                'title_class' => 'flag-style-arrow',
                'icon_class'  => 'diamond'

            ];
        }
        if (!empty($aItem['is_sponsor'])) {
            $aTitleLabel['label']['sponsored'] = [
                'title' => '',
                'title_class' => 'flag-style-arrow',
                'icon_class'  => 'sponsor'

            ];
        }
        if (!$aItem['is_approved']) {
            $aTitleLabel['label']['pending'] = [
                'title' => '',
                'title_class' => 'flag-style-arrow',
                'icon_class' => 'clock-o'

            ];
            $aPendingItem = [
                'message' => _p('this_fundraising_is_pending_an_admins_approval'),
                'actions' => []
            ];
            if (Phpfox::getUserParam('fundraising.can_approve_campaigns')) {
                $aPendingItem['actions']['approve'] = [
                    'is_ajax' => true,
                    'label' => _p('approve'),
                    'action' => '$.ajaxCall(\'fundraising.approve\', \'inline=true&id=' . $aItem['campaign_id'] . '\')'
                ];
            }

            $this->template()->assign([
                'aPendingItem' => $aPendingItem
            ]);
        }
        $aTitleLabel['total_label'] = isset($aTitleLabel['label']) ? count($aTitleLabel['label']) : 0;

		// Define params for "review views" block
		$this->setParam(array(
			'sTrackType' => 'fundraising',
			'iTrackId' => $aItem['campaign_id'],
			'iTrackUserId' => $aItem['user_id']
				)
		);

		$aCategories = Phpfox::getService('fundraising.category')->getCategoriesByCampaignId($aItem['campaign_id']);
		$aLastCategory = array();
		if($aCategories)
		{
			$aLastCategory = end($aCategories);
			$aItem['info'] = _p('posted_x_by_x_in_x', array('date' => Phpfox::getTime(Phpfox::getParam('core.global_update_time'), $aItem['time_stamp']), 'link' => Phpfox::getLib('url')->makeUrl('profile', array($aItem['user_name'])), 'user' => $aItem, 'categories' => $aLastCategory[0]));
		}
		else
		{
			$aItem['info'] = _p('posted_x_by_x', array('date' => Phpfox::getTime(Phpfox::getParam('core.global_update_time'), $aItem['time_stamp']), 'link' => Phpfox::getLib('url')->makeUrl('profile', array($aItem['user_name'])), 'user' => $aItem));
		}

		$aItem['bookmark_url'] = Phpfox::permalink('fundraising', $aItem['campaign_id'], $aItem['title']);

		(($sPlugin = Phpfox_Plugin::get('fundraising.component_controller_view_process_middle')) ? eval($sPlugin) : false);

		// Add tags to meta keywords
		if (!empty($aItem['tag_list']) && $aItem['tag_list'] && Phpfox::isModule('tag')) {
			$this->template()->setMeta('keywords', Phpfox::getService('tag')->getKeywords($aItem['tag_list']));
		}

		$this->setParam('aFeed', array(
			'comment_type_id' => 'fundraising',
			'privacy' => $aItem['privacy'],
			'comment_privacy' => $aItem['privacy_comment'],
			'like_type_id' => 'fundraising',
			'feed_is_liked' => isset($aItem['is_liked']) ? $aItem['is_liked'] : false,
			'feed_is_friend' => $aItem['is_friend'],
			'item_id' => $aItem['campaign_id'],
			'user_id' => $aItem['user_id'],
			'total_comment' => $aItem['total_comment'],
			'total_like' => $aItem['total_like'],
			'feed_link' => $aItem['bookmark_url'],
			'feed_title' => $aItem['title'],
			'feed_display' => 'view',
			'feed_total_like' => $aItem['total_like'],
			'report_module' => 'fundraising',
			'report_phrase' => _p('report_this_fundraising'),
			'time_stamp' => $aItem['time_stamp']
				)
		);


		if ($aItem['module_id'] != 'fundraising' && ($aCallback = Phpfox::callback('fundraising.getFundraisingDetails', array('item_id' => $aItem['item_id'])))) {
			$this->template()->setBreadcrumb($aCallback['breadcrumb_title'], $aCallback['breadcrumb_home']);
			$this->template()->setBreadcrumb($aCallback['title'], $aCallback['url_home']);
		}
			
		$this->setParam('aFrCampaign', $aItem);
		$this->template()->setTitle($aItem['title'])
				->setBreadCrumb(_p('fundraisings'), $aItem['module_id'] == 'fundraising' ? $this->url()->makeUrl('fundraising') : $this->url()->permalink('pages', $aItem['item_id'], 'fundraising') )
				->setBreadCrumb($aItem['title'], $this->url()->permalink('fundraising', $aItem['campaign_id'], $aItem['title']), true)
				->setMeta('description', $aItem['title'] . '.')
				->setMeta('description', $aItem['description'] . '.')
				->setMeta('description', $aItem['info'] . '.')
				->setMeta('keywords', $this->template()->getKeywords($aItem['title']))
				->setMeta('keywords', Phpfox::getParam('fundraising.fundraising_meta_keywords'))
				->setMeta('description', Phpfox::getParam('fundraising.fundraising_meta_description'))
				->assign(array(
					'aCampaign' => $aItem,
					'bFundraisingView' => true,
					'bIsProfile' => $bIsProfile,
					'sTagType' => ($bIsProfile === true ? 'fundraising_profile' : 'fundraising'),
					'corepath' => Phpfox::getParam('core.path'),
					'aCampaignStatus' => Phpfox::getService('fundraising.campaign')->getAllStatus(),
					'aLastCategory' => $aLastCategory,
						'googleApiKey' => Phpfox::getParam('core.google_api_key')
						)
				)->setHeader('cache', array(
					'jquery/plugin/jquery.highlightFade.js' => 'static_script',
					'jquery/plugin/jquery.scrollTo.js' => 'static_script',
					'quick_edit.js' => 'static_script',
					'switch_menu.js' => 'static_script',
					'comment.css' => 'style_css',
					'pager.css' => 'style_css',
					'feed.js' => 'module_feed',
				)
		);

		$this->template()->setHeader(
				array(
					'owl.carousel.min.js' => 'module_fundraising',
					'homepageslider/slides.min.jquery.js' => 'module_fundraising',
					'map.js' => 'module_fundraising',
                    'ynfundraising.js' => 'module_fundraising',
				)
		);

		// add support responsiveclean template
        if ( $this->template()->getThemeFolder() == 'ynresponsiveclean' ) {
        	$this->template()->setHeader(
				array(
					'flexslider.css' => 'module_fundraising',
					'jquery.flexslider.js' => 'module_fundraising',
				)
			);            
        }

		$this->_buildSubsectionMenu();

		//to make facebook know the image
        $sImageUrl = str_replace('%s', '',  Phpfox::getParam('core.path') . 'file' . PHPFOX_DS . 'pic' . PHPFOX_DS .  $aItem['image_path']);
        $this->template()->setHeader(array('<meta property="og:image" content="'. $sImageUrl . '" />'));
        $this->template()->setHeader(array('<link rel="image_src" href="'. $sImageUrl . '" />'));
			
        $this->template()->setPhrase(array(
            'select_all',
            'un_select_all',
        ));

		if ($this->request()->get('req4') == 'comment') {
			$this->template()->setHeader('<script type="text/javascript">var $bScrollToFundraisingComment = false; $Behavior.scrollToFundraisingComment = function () { if ($bScrollToFundraisingComment) { return; } $bScrollToFundraisingComment = true; if ($(\'#js_feed_comment_pager_' . $aItem['campaign_id'] . '\').length > 0) { $.scrollTo(\'#js_feed_comment_pager_' . $aItem['campaign_id'] . '\', 800); } }</script>');
		}

		if ($this->request()->get('req4') == 'add-comment') {
			$this->template()->setHeader('<script type="text/javascript">var $bScrollToFundraisingComment = false; $Behavior.scrollToFundraisingComment = function () { if ($bScrollToFundraisingComment) { return; } $bScrollToFundraisingComment = true; if ($(\'#js_feed_comment_form_' . $aItem['campaign_id'] . '\').length > 0) { $.scrollTo(\'#js_feed_comment_form_' . $aItem['campaign_id'] . '\', 800); $Core.commentFeedTextareaClick($(\'.js_comment_feed_textarea\')); $(\'.js_comment_feed_textarea\').focus(); } }</script>');
		}

		(($sPlugin = Phpfox_Plugin::get('fundraising.component_controller_view_process_end')) ? eval($sPlugin) : false);
	}
 
	private function _buildSubsectionMenu() {
			Phpfox::getService('fundraising')->buildMenu();
	}

	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean() {
		(($sPlugin = Phpfox_Plugin::get('fundraising.component_controller_view_clean')) ? eval($sPlugin) : false);
	}

}

?>