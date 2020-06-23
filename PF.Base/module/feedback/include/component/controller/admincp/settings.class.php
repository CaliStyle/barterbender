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
class FeedBack_Component_Controller_Admincp_Settings extends Phpfox_Component
{
	public function process()
	{
		/*    $is_allowed_post = Phpfox::getLib('database')->select('param_values')
		 ->from(Phpfox::getT('feedback_settings'))
		 ->where('settings_type="is_allowed"')
		 ->execute('getRow');
		 if(!empty ($is_allowed_post))
		 {
		 $is_allowed = $is_allowed_post['param_values'];
		 }
		 else {
		 $is_allowed = 0;
		 }

		 $is_send_email = Phpfox::getLib('database')->select('param_values')
		 ->from(Phpfox::getT('feedback_settings'))
		 ->where('settings_type="is_email"')
		 ->execute('getRow');
		 if(!empty ($is_send_email))
		 {
		 $is_email = $is_send_email['param_values'];
		 }
		 else {
		 $is_email = 0;
		 }
		 $this->template()->assign(array(
		 'is_allowed' => $is_allowed,
		 'is_email' => $is_email
		 ));

		 if(isset($_POST['save_global_settings']))
		 {
		 $aVals = $this->request()->get('val');
		 $aSettings = Phpfox::getLib('database')->select('param_values')
		 ->from(Phpfox::getT('feedback_settings'))
		 ->where('settings_type IN ("is_allowed","is_email")')
		 ->execute('getRows');

		 if(empty($aSettings))
		 {
		 phpfox::getLib('database') ->insert(phpfox::getT('feedback_settings'),array(
		 'settings_id' => NULL,
		 'settings_type'=>'is_allowed',
		 'param_values' => (int)$aVal['is_allowed']
		 ));

		 Phpfox::getLib('database') ->insert(phpfox::getT('feedback_settings'),array(
		 'settings_id' => NULL,
		 'settings_type'=>'is_email',
		 'param_values' => (int)$aVal['is_email']
		 ));
		 }
		 else
		 {
		 phpfox::getLib('database') ->update(phpfox::getT('feedback_settings'),array(
		 'param_values' => (int)$aVals['is_allowed']),
		 'settings_type="is_allowed"');
		 phpfox::getLib('database') ->update(phpfox::getT('feedback_settings'),array(
		 'param_values' => (int)$aVals['is_email']),
		 'settings_type="is_email"');
		 }

		 $this->url()->send('current',null,'Global Settings were updated successfully.');
		 }
		 
		$send_mail_url =  phpfox::getLib('phpfox.database')->select('*')
			->from(Phpfox::getT('feedback_settings'))
			->where('settings_type = "send_mail_to_none_user"')
			->execute('getRow');
		if($send_mail_url == null)
		{
			$is_send_mail_to_none_user = 0;
		}
		else
		{
			$is_send_mail_to_none_user = $send_mail_url['param_values'];
		}
		$this->template()->assign(array('is_send_mail_to_none_user'=>$is_send_mail_to_none_user));
		if ($this->request()->get('save_settings'))
		{
			$send_mail_url =  phpfox::getLib('phpfox.database')->select('*')
			->from(Phpfox::getT('feedback_settings'))
			->where('settings_type = "send_mail_to_none_user"')
			->execute('getRow');
			$is_send_mail_to_none_user = $this->request()->get('is_send_mail_to_none_user');
			if($send_mail_url != null)
			{
				phpfox::getLib('phpfox.database')
				->update(Phpfox::getT('feedback_settings'),
				array('param_values'=>$is_send_mail_to_none_user),
                            'settings_id  = '.$send_mail_url['settings_id']);
			}
			else
			{
		 		if ($is_send_mail_to_none_user == 1)
		 		{
		 			Phpfox::getLib('phpfox.database')
		 				->insert(Phpfox::getT('feedback_settings'),
		 					array(
   								 'param_values'=> $is_send_mail_to_none_user,
                            	 'settings_type'=>'send_mail_to_none_user'
                             ));

		 		}

			}
		}
		*/		
	}
}

?>
