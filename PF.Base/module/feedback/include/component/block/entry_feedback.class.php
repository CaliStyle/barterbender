<?php
class FeedBack_Component_Block_Entry_Feedback extends Phpfox_Component
{
	public function process()
	{
		$feedback_id = $this->getParam('feedback_id');
		$aFeedBack = phpfox::getService('feedback')->getFeedBackDetailById($feedback_id);
		if(!empty($aFeedBack['category_name']))
		{
			$sCategory = ' <a href="' . Phpfox::getLib('url')->makeUrl('feedback', array('category', $aFeedBack['feedback_category_id'], $aFeedBack['category_url'])) . '">' . $aFeedBack['category_name'] . '</a>';
			$aFeedBack['category_url'] = $sCategory;
		}
		if(Phpfox::isModule('track') && phpfox::isUser() && count($aFeedBack) > 0 && !$aFeedBack['is_viewed'])
		{
			Phpfox::getService('track.process')->add('feedback', $aFeedBack['feedback_id']);
			Phpfox::getService('feedback.process')->updateView($aFeedBack['feedback_id']);
		}
		$link = Phpfox::getLib('url')->makeURL('feedback');
		if(empty($aFeedBack['full_name']))
		{
			$aFeedBack['info'] = 'Posted '.Phpfox::getTime(Phpfox::getParam('core.global_update_time'), $aFeedBack['time_stamp']).' by '.$aFeedBack['visitor'];
		}
		else
		{
			$aFeedBack['info'] = 'Posted '.Phpfox::getTime(Phpfox::getParam('core.global_update_time'), $aFeedBack['time_stamp']).' by <a title="'.$aFeedBack['full_name'].'" href="'.phpfox::getLib('url')->makeURL($aFeedBack['user_name']).'">'.$aFeedBack['full_name'].'</a> ';
		}
		
		$aFeedBack['privacy'] = 0;
		$aFeedBack['aFeed'] = array(
				'feed_display' => 'mini',	
				'comment_type_id' => 'feedback',
				'privacy' => $aFeedBack['privacy'],
				'comment_privacy' => 0,
				'like_type_id' => 'feedback',				
				'feed_is_liked' => (isset($aFeedBack['is_liked']) ? $aFeedBack['is_liked'] : false),
				'feed_is_friend' => (isset($aFeedBack['is_friend']) ? $aFeedBack['is_friend'] : false),
				'item_id' => $aFeedBack['feedback_id'],
				'user_id' => $aFeedBack['user_id'],
				'total_comment' => $aFeedBack['total_comment'],
				'feed_total_like' => $aFeedBack['total_like'],
				'total_like' => $aFeedBack['total_like'],
				'feed_link' => $this->url()->makeUrl('feedback.detail', $aFeedBack['title_url']),
				'feed_title' => $aFeedBack['title'],
				'time_stamp' => $aFeedBack['time_stamp']
		);
		$this->setParam('aFeed', array(
				'feed_display' => 'mini',	
				'comment_type_id' => 'feedback',
				'privacy' => $aFeedBack['privacy'],
				'comment_privacy' => 0,
				'like_type_id' => 'feedback',				
				'feed_is_liked' => (isset($aFeedBack['is_liked']) ? $aFeedBack['is_liked'] : false),
				'feed_is_friend' => (isset($aFeedBack['is_friend']) ? $aFeedBack['is_friend'] : false),
				'item_id' => $aFeedBack['feedback_id'],
				'user_id' => $aFeedBack['user_id'],
				'total_comment' => $aFeedBack['total_comment'],
				'feed_total_like' => $aFeedBack['total_like'],
				'total_like' => $aFeedBack['total_like'],
				'feed_link' => $this->url()->makeUrl('feedback.detail', $aFeedBack['title_url']),
				'feed_title' => $aFeedBack['title'],
				'time_stamp' => $aFeedBack['time_stamp']
		));
		$this->template()->assign(array(
					'aFeedBack' => $aFeedBack,
					'core_path'=>phpfox::getParam('core.path')
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
		$this->template()->setHeader(array(
							'jquery/plugin/jquery.highlightFade.js' => 'static_script',				
							'quick_edit.js' => 'static_script',	
							'pager.css' => 'style_css',
							'switch_legend.js' => 'static_script',
							'switch_menu.js' => 'static_script',
		                    'feedback.js' => 'module_feedback',
							'feed.js' => 'module_feed',
							'country.js' => 'module_core',
					));
					return 'block';
	}
}
?>