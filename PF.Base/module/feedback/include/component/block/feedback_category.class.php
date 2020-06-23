<?php
class FeedBack_Component_Block_Feedback_Category extends Phpfox_Component
{
	public function process()
	{
		$category_id = $this->getParam('category_id');
		if($category_id > 0)
		{
			$where = " (fb.privacy = 1 AND fb.is_approved = 1 AND fb.feedback_category_id = ".$category_id.")";
		}
		else if ($category_id == 0)
		{
			$where = " (fb.privacy = 1 AND fb.is_approved = 1)";
		}
		else
		{
			$where = " (fb.privacy = 1 AND fb.is_approved = 1 AND fb.feedback_category_id = 0)";
		}
		$link = Phpfox::getLib('url')->makeURL('feedback');
		$iCatIds = Phpfox::getLib('database')->select('category_id')
											->from(Phpfox::getT('feedback_category'))
											->execute('getSlaveFields');

			$votedFeedBacks = Phpfox::getLib('database')
						->select('fb.*,fs.name as status,fs.colour as color, fser.name as feedback_servertity_name, fser.colour as feedback_serverity_color')
						->from(Phpfox::getT('feedback'),'fb')
						->leftjoin(Phpfox::getT('feedback_status'),'fs','fb.feedback_status_id=fs.status_id')
						->leftjoin(Phpfox::getT('feedback_serverity'), 'fser', 'fser.serverity_id = fb.	feedback_serverity_id')
						->where($where)
						->order('fb.total_vote DESC')
						->limit(0,4)
						->execute('getRows');

		foreach($votedFeedBacks as $iKey => $aItem)
		{
			$votedFeedBacks[$iKey] = Phpfox::getService('feedback')->getFeedBackDetailById($votedFeedBacks[$iKey]['feedback_id']);
			if(empty($votedFeedBacks[$iKey]['full_name']))
			{
				$votedFeedBacks[$iKey]['info'] = 'Posted '.Phpfox::getTime(Phpfox::getParam('core.global_update_time'), $votedFeedBacks[$iKey]['time_stamp']).' by '.$votedFeedBacks[$iKey]['visitor'];
			}
			else
			{
				$votedFeedBacks[$iKey]['info'] = 'Posted '.Phpfox::getTime(Phpfox::getParam('core.global_update_time'), $votedFeedBacks[$iKey]['time_stamp']).' by <a title="'.$votedFeedBacks[$iKey]['full_name'].'" href="'.phpfox::getLib('url')->makeURL($votedFeedBacks[$iKey]['user_name']).'">'.$votedFeedBacks[$iKey]['full_name'].'</a> ';
			}
		}
		Phpfox::getService('feedback')->getFeedbackVoted($votedFeedBacks);
		$this->template()->assign(
		array(
			'topVotedFeedBacks'=>$votedFeedBacks,
			'user_id' =>phpfox::getUserId(),
			'core_path' => Phpfox::getParam('core.path'),
		)
		);
	}
}
?>