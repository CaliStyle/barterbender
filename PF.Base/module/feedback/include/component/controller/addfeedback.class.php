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
class FeedBack_Component_Controller_Addfeedback extends Phpfox_Component {
	public function process()
	{

		$login = phpfox::getLib('url')->makeURL('user.login');
		$visitor = _p('feedback.post_a_feedback_visitor', array('login'=>$login));
		$feedback = Phpfox::getLib('url')->makeURL('feedback');
		if(($aVals = $this->request()->getArray('val')))
		{
			if(!phpfox::isUser())
			{
				$aVals['privacy'] = 1;
			}
            $oFilter = Phpfox::getLib('parse.input');
            $aVals['title'] = $oFilter->clean(strip_tags($aVals['title']), 255);
            $aVals['description'] = $oFilter->clean(strip_tags($aVals['description']));
			$iId = Phpfox::getService('feedback.process')->add($aVals);
			$feedback_id = $iId[0];
			$iId[1] = '"'.$iId[1].'"';
			if(Phpfox::getParam('feedback.is_send_mail'))
			{
				$isSend = Phpfox::getService('feedback')-> sendMailAdmin($feedback_id);
			}
			if(Phpfox::isUser())
			{
				return $this->url()->send('feedback.view_my',null,_p('your_feedback_title_was_created_successfully', array('title'=>$iId[1])).' '.Phpfox::getParam('feedback.thank_you_message'));
			}
			else
			{
                return $this->url()->send('feedback',null,_p('your_feedback_title_was_created_successfully', array('title'=>$iId[1])).' '.Phpfox::getParam('feedback.thank_you_message'));
			}
		}
		$this->template()
		->setHeader(array(
							'quick_edit.js' => 'static_script',	
							'pager.css' => 'style_css',
							'switch_legend.js' => 'static_script',
							'switch_menu.js' => 'static_script',
							'feed.js' => 'module_feed',
							'country.js' => 'module_core',
		))
		->setTitle('Post Feed Back')
		->assign(array(
                'core_path' => Phpfox::getParam('core.path'),
                'visitor' => $visitor,
                'feedback' => $feedback,
                'aFeedback' => $aVals              
		));
	}
}
?>
