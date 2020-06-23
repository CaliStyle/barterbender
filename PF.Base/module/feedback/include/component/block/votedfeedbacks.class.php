<?php
/*
 * @copyright        [YouNet_COPYRIGHT]
 * @author           YouNet Company
 * @package          Module_FeedBack
 * @version          2.01
 *
 */
defined('PHPFOX') or exit('NO DICE!');
?>

<?php
class FeedBack_Component_Block_VotedFeedBacks extends Phpfox_Component
{
    public function process()
    {
    $sView = $this->request()->get('view');
    	if($sView != '')
    	{
    		return false;
    	}
        $core_url= Phpfox::getParam('core.path');
        $votedFeedBacks = Phpfox::getLib('database')
                    ->select('*')
                    ->from(Phpfox::getT('feedback'),'fb')
                    ->where('privacy = 1 and is_approved = 1')
                    ->order('fb.total_vote DESC')
                    ->limit(0,5)
                    ->execute('getRows');
        foreach ($votedFeedBacks as &$aFeedBack)
        {
            if($aFeedBack['user_id'] != 0)
            {
                $user = Phpfox::getLib('database')
                    ->select(Phpfox::getUserField())
                    ->from(Phpfox::getT('user'),'u')
                    ->where('u.user_id ='.$aFeedBack['user_id'])
                    ->execute('getRow');
                $aFeedBack = array_merge($aFeedBack,$user);
            }
            else {
                $aFeedBack['user_image'] = Phpfox::getParam('core.path')."module/feedback/static/image/guestAvatar.png";
            }
        }
        $iCnt = Phpfox::getLib('database')
                    ->select('Count(*)')
                    ->from(Phpfox::getT('feedback'),'fb')
                    ->where(1)
                    ->execute('getSlaveField');       
        $this->template()
                ->assign(array(
                 'sHeader' => _p('feedback.most_voted_feedbacks'),
                 'core_url' => $core_url,
                 'votedFeedBacks'=>$votedFeedBacks,
                 'iCnt'=>$iCnt
                ));
         $this->template()->setHeader(array(
					'welcome.css' => 'style_css',
					'announcement.css' => 'style_css',
					'quick_edit.js' => 'static_script',
					'jquery/plugin/jquery.highlightFade.js' => 'static_script',
					'jquery/plugin/jquery.scrollTo.js' => 'static_script'
				)
			);
     return 'block';
    }
}
?>
