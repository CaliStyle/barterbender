<?php
/*
 * @copyright        [YouNet_COPYRIGHT]
 * @author           YouNet Company
 * @package          Module_FeedBack
 * @version          2.01
 *
 */
defined('PHPFOX') or exit('NO DICE!');

class FeedBack_Component_Block_FeedbackStatistics extends Phpfox_Component
{
    public function process()
    {
    	$sView = $this->request()->get('view');
    	if($sView != '')
    	{
    		return false;
    	}
         $aFeedBack = Phpfox::getLib('database')
                    ->select('Count(fb.feedback_id) as feedbacks, SUM(fb.total_comment) as comments,SUM(fb.total_vote) as votes')
                    ->from(Phpfox::getT('feedback'),'fb')
                    ->where('fb.is_approved = 1')
                    ->execute('getRow');
        $total_feedback_private = Phpfox::getLib('database')
                    ->select('Count(fb.feedback_id) as feedbacks_private')
                    ->from(Phpfox::getT('feedback'),'fb')
                    ->where('fb.privacy=2 and fb.is_approved = 1')
                    ->execute('getSlaveField');
        $total_feedback_public = Phpfox::getLib('database')
                    ->select('Count(fb.feedback_id) as feedbacks_public')
                    ->from(Phpfox::getT('feedback'),'fb')
                    ->where('fb.privacy=1 and fb.is_approved = 1')
                    ->execute('getSlaveField');
        $total_anonymous_feedbacks = Phpfox::getLib('database')
                    ->select('Count(fb.feedback_id) as anynomyous_feedbacks')
                    ->from(Phpfox::getT('feedback'),'fb')
                    ->where('fb.user_id=0 and fb.is_approved = 1')
                    ->execute('getSlaveField');
        $numbers_feedbacks = (isset($aFeedBack['feedbacks'])?$aFeedBack['feedbacks']:'0');
        $total_comments = isset($aFeedBack['comments'])?$aFeedBack['comments']:'0';
        $total_votes = isset($aFeedBack['votes'])?$aFeedBack['votes']:'0';
        $this->template()->assign(array(
            'sHeader' => _p('feedback.feedback_statistics'),
            'total_feedbacks' => $numbers_feedbacks,
            'total_public_feedbacks' => $total_feedback_public,
            'total_private_feedbacks' => $total_feedback_private,
            'total_anonymous_feedbacks'=>$total_anonymous_feedbacks,
            'total_comments'=>isset($total_comments)?$total_comments:'0',
            'total_votes' => isset($total_votes)?$total_votes:'0'
        )
    );

     return 'block';
    }
}