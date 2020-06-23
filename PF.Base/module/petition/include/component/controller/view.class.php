<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Petition_Component_Controller_View extends Phpfox_Component 
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{         
		if ($this->request()->getInt('id'))
		{
			return Phpfox::getLib('module')->setController('error.404');
		}
            
		if (Phpfox::isUser() && Phpfox::isModule('notification'))
		{
			Phpfox::getService('notification.process')->delete('comment_petition', $this->request()->getInt('req2'), Phpfox::getUserId());
			Phpfox::getService('notification.process')->delete('petition_like', $this->request()->getInt('req2'), Phpfox::getUserId());
		}
		
		Phpfox::getUserParam('petition.view_petitions', true);
		$aCallback = $this->getParam('aCallback', false);
            
            $iPetition = $this->request()->getInt(($aCallback !== false ? $aCallback['request'] : 'req2'));
            
		(($sPlugin = Phpfox_Plugin::get('petition.component_controller_view_process_start')) ? eval($sPlugin) : false);	
		
		$bIsProfile = $this->getParam('bIsProfile');		
		if ($bIsProfile === true)
		{
			$this->setParam(array(
					'bViewProfilePetition' => true,
					'sTagType' => 'petition'
				)
			);
		}
            
		$aItem = Phpfox::getService('petition')->callback($aCallback)->getPetition($iPetition);
                
		if (!isset($aItem['petition_id']))
		{			
			return Phpfox_Error::display(Phpfox::getPhrase('petition.petition_not_found'));
		}
            
            if (!empty($aItem['module_id']) && $aItem['module_id'] != 'petition')
		{			
			if ($aCallback = Phpfox::callback('petition.getPetitionDetails', $aItem))
			{
				$this->template()->setBreadcrumb($aCallback['breadcrumb_title'], $aCallback['breadcrumb_home']);
				$this->template()->setBreadcrumb($aCallback['title'], $aCallback['url_home']);
				if ($aItem['module_id'] == 'pages' && !Phpfox::getService('pages')->hasPerm($aCallback['item_id'], 'petition.view_browse_petitions'))
				{
					return Phpfox_Error::display(Phpfox::getPhrase('petition.unable_to_view_this_item_due_to_privacy_settings'));
				}		
			}
		}
            		
		if (Phpfox::getUserId() == $aItem['user_id'] && Phpfox::isModule('notification'))
		{
			Phpfox::getService('notification.process')->delete('petition_approved', $this->request()->getInt('req2'), Phpfox::getUserId());
		}
		
		if (Phpfox::isModule('privacy'))
		{
			Phpfox::getService('privacy')->check('petition', $aItem['petition_id'], $aItem['user_id'], $aItem['privacy'], $aItem['is_friend']);
		}
				
		if (!Phpfox::getUserParam('petition.can_approve_petitions'))
		{
			if ($aItem['is_approved'] != '1' && $aItem['user_id'] != Phpfox::getUserId())
			{
				return Phpfox_Error::display(Phpfox::getPhrase('petition.petition_not_found'));
			}
		}
		
		if (Phpfox::isModule('track') && Phpfox::isUser() && Phpfox::getUserId() != $aItem['user_id'] && !$aItem['is_viewed'])
		{
			Phpfox::getService('track.process')->add('petition', $aItem['petition_id']);
			Phpfox::getService('petition.process')->updateView($aItem['petition_id']);
		}
		
		if (Phpfox::isUser() && Phpfox::isModule('track') && Phpfox::getUserId() != $aItem['user_id'] && $aItem['is_viewed'] && !Phpfox::getUserBy('is_invisible'))
		{
			Phpfox::getService('track.process')->update('petition_track', $aItem['petition_id']);	
		}		
		
		// Define params for "review views" block
		$this->setParam(array(
				'sTrackType' => 'petition',
				'iTrackId' => $aItem['petition_id'],
				'iTrackUserId' => $aItem['user_id']
			)
		);
		
		$aCategories = Phpfox::getService('petition.category')->getCategoriesById($aItem['petition_id']);
		
		if (Phpfox::isModule('tag'))
		{
			$aTags = Phpfox::getService('tag')->getTagsById('petition', $aItem['petition_id']);	
			if (isset($aTags[$aItem['petition_id']]))
			{
				$aItem['tag_list'] = $aTags[$aItem['petition_id']];
			}
		}

		if (isset($aCategories[$aItem['petition_id']]))
		{
			$sCategories = '';
			foreach ($aCategories[$aItem['petition_id']] as $iKey => $aCategory)
			{
				$sCategories .= ($iKey != 0 ? ',' : '') . ' <a href="' . ($aCategory['user_id'] ? $this->url()->permalink($aItem['user_name'] . '.petition.category', $aCategory['category_id'], $aCategory['category_name']) : $this->url()->permalink('petition.category', $aCategory['category_id'], $aCategory['category_name'])) . '">' . Phpfox::getLib('locale')->convert(Phpfox::getLib('parse.output')->clean($aCategory['category_name'])) . '</a>';
				
				$this->template()->setMeta('keywords', $aCategory['category_name']);
			}
		}

		if (isset($sCategories))
		{
			$aItem['info'] = Phpfox::getPhrase('petition.posted_x_by_x_in_x', array('date' => Phpfox::getTime(Phpfox::getParam('petition.petition_time_stamp'), $aItem['time_stamp']), 'link' => Phpfox::getLib('url')->makeUrl('profile', array($aItem['user_name'])), 'user' => $aItem, 'categories' => $sCategories));
		}
		else 
		{
			$aItem['info'] = Phpfox::getPhrase('petition.posted_x_by_x', array('date' => Phpfox::getTime(Phpfox::getParam('petition.petition_time_stamp'), $aItem['time_stamp']), 'link' => Phpfox::getLib('url')->makeUrl('profile', array($aItem['user_name'])), 'user' => $aItem));
		}		
		
		$aItem['bookmark_url'] = Phpfox::permalink('petition', $aItem['petition_id'], $aItem['title']);

		(($sPlugin = Phpfox_Plugin::get('petition.component_controller_view_process_middle')) ? eval($sPlugin) : false);
		
		// Add tags to meta keywords
		if (!empty($aItem['tag_list']) && $aItem['tag_list'] && Phpfox::isModule('tag'))
		{
			$this->template()->setMeta('keywords', Phpfox::getService('tag')->getKeywords($aItem['tag_list']));
		}	
		
		$this->setParam('aFeed', array(				
				'comment_type_id' => 'petition',
				'privacy' => $aItem['privacy'],
				'comment_privacy' => $aItem['privacy_comment'],
				'like_type_id' => 'petition',
				'feed_is_liked' => isset($aItem['is_liked']) ? $aItem['is_liked'] : false,
				'feed_is_friend' => $aItem['is_friend'],
				'item_id' => $aItem['petition_id'],
				'user_id' => $aItem['user_id'],
				'total_comment' => $aItem['total_comment'],
				'total_like' => $aItem['total_like'],
				'feed_link' => $aItem['bookmark_url'],
				'feed_title' => $aItem['title'],
				'feed_display' => 'view',
				'feed_total_like' => $aItem['total_like'],
				'report_module' => 'petition',
				'report_phrase' => Phpfox::getPhrase('petition.report_this_petition'),
				'time_stamp' => $aItem['time_stamp']
			)
		);
		
		$this->setParam('aPetition',$aItem);
		$this->template()->setTitle($aItem['title'])
		 	->setBreadCrumb(Phpfox::getPhrase('petition.petitions_title'), $aItem['module_id'] == 'petition' ? $this->url()->makeUrl('petition') : $this->url()->permalink('pages', $aItem['item_id'], 'petition') )			
		 	->setBreadCrumb($aItem['title'], $this->url()->permalink('petition', $aItem['petition_id'], $aItem['title']), true)
			->setMeta('description', $aItem['title'] . '.')
			->setMeta('description', $aItem['description'] . '.')
			->setMeta('description', $aItem['info'] . '.')
			->setMeta('keywords', $this->template()->getKeywords($aItem['title']))	
			->assign(array(
					'current_url' =>  $this->url()->permalink('petition', $aItem['petition_id'], ""),
					'aItem' => $aItem,
					'bPetitionView' => true,
					'bIsProfile' => $bIsProfile,
					'sTagType' => ($bIsProfile === true ? 'petition_profile' : 'petition'),
					'iShorten' => Phpfox::getParam('petition.preview_length_in_index'),
					'corepath' => Phpfox::getParam('core.path'),
					'isPages' => $this->request()->get('req1'),
				)
			)
			->setPhrase(array(
				'you_cannot_write_more_then_limit_characters',
				'you_have_limit_character_s_left'
				)
			)
			->setHeader('cache', array(
				'petition.js'  => 'module_petition',
				'jquery/plugin/jquery.highlightFade.js' => 'static_script',
				'jquery/plugin/jquery.limitTextarea.js' => 'static_script',
				'jquery/plugin/jquery.scrollTo.js' => 'static_script',
				'quick_edit.js' => 'static_script',
				'switch_menu.js' => 'static_script',
				'comment.css' => 'style_css',
				'pager.css' => 'style_css',
				'feed.js' => 'module_feed',
				'view.css' => 'module_petition',
				'pager.css' => 'style_css',

			)
		);
		
		if (Phpfox::getUserId())
		{
			$this->template()->setEditor(array(
					'load' => 'simple',
					'wysiwyg' => ((Phpfox::isModule('comment')))
				)
			);
		}		
		
		if (Phpfox::getParam('petition.petition_digg_integration'))
		{
			//$this->template()->setHeader('<script type="text/javascript">$Behavior.loadPetitionButtonJs = function() {$(function() {var s = document.createElement(\'SCRIPT\'), s1 = document.getElementsByTagName(\'SCRIPT\')[0];s.type = \'text/javascript\';s.async = true;s.src = \'http://widgets.digg.com/buttons.js\';s1.parentNode.insertBefore(s, s1);});}</script>');
		}
		
		if ($this->request()->get('req4') == 'comment')
		{
			$this->template()->setHeader('<script type="text/javascript">var $bScrollToPetitionComment = false; $Behavior.scrollToPetitionComment = function () { if ($bScrollToPetitionComment) { return; } $bScrollToPetitionComment = true; if ($(\'#js_feed_comment_pager_' . $aItem['petition_id'] . '\').length > 0) { $.scrollTo(\'#js_feed_comment_pager_' . $aItem['petition_id'] . '\', 800); } }</script>');
		}
		
		if ($this->request()->get('req4') == 'add-comment')
		{
			$this->template()->setHeader('<script type="text/javascript">var $bScrollToPetitionComment = false; $Behavior.scrollToPetitionComment = function () { if ($bScrollToPetitionComment) { return; } $bScrollToPetitionComment = true; if ($(\'#js_feed_comment_form_' . $aItem['petition_id'] . '\').length > 0) { $.scrollTo(\'#js_feed_comment_form_' . $aItem['petition_id'] . '\', 800); $Core.commentFeedTextareaClick($(\'.js_comment_feed_textarea\')); $(\'.js_comment_feed_textarea\').focus(); } }</script>');
		}
		$aFilterMenu = array();

		if (!defined('PHPFOX_IS_USER_PROFILE') && !defined('PHPFOX_IS_PAGES_VIEW'))
		{
			$aFilterMenu = array(
				Phpfox::getPhrase('petition.all_petitions') => '',
				Phpfox::getPhrase('petition.my_petitions') => 'my',
			);

			if (!Phpfox::getParam('core.friends_only_community') && Phpfox::isModule('friend'))
			{
				$aFilterMenu[Phpfox::getPhrase('petition.friends_petitions')] = 'friend';
			}
			/*
        list($iTotalFeatured, $aFeatured) = Phpfox::getService('petition')->getFeatured();
        if ($iTotalFeatured)
        {
            $aFilterMenu[Phpfox::getPhrase('petition.featured_petitions') . '<span class="pending">' . $iTotalFeatured . '</span>'] = 'featured';
        }
        */
			if (Phpfox::getUserParam('petition.can_approve_petitions'))
			{
				$iPendingTotal = Phpfox::getService('petition')->getPendingTotal();

				if ($iPendingTotal)
				{
					$aFilterMenu[Phpfox::getPhrase('petition.pending_petitions') . (Phpfox::getUserParam('petition.can_approve_petitions') ? '<span class="pending">' . $iPendingTotal . '</span>' : 0)] = 'pending';
				}
			}
			$aFilterMenu[Phpfox::getPhrase('petition.help')] =  'petition.help.view_help';

			$this->template()->buildSectionMenu('petition', $aFilterMenu);
		}
		(($sPlugin = Phpfox_Plugin::get('petition.component_controller_view_process_end')) ? eval($sPlugin) : false);
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('petition.component_controller_view_clean')) ? eval($sPlugin) : false);
	}
}

?>