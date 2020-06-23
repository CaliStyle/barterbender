<?php

/*
 * @copyright        [YouNet_COPYRIGHT]
 * @author           YouNet Development
 * @package          Module_Contactimporter
 * @version          2.06
 *
 */
?>
<?php

class contactimporter_Component_Controller_Admincp_settings extends Phpfox_Component
{

	public function process()
	{
		$aGlobalSettings = Phpfox::getLib('database') -> select('*') -> from(Phpfox::getT('contactimporter_settings')) -> execute('getRows');

		$number_provider_display = 10;
		$is_unsubcribed = 1;
		$icon_size = 30;
		foreach ($aGlobalSettings as $iSettingId => $aRow)
		{
			if ($aRow['settings_type'] == 'number_provider_display')
			{
				$number_provider_display = $aRow['param_values'];
			}
			if ($aRow['settings_type'] == 'is_unsubcribed')
			{
				$is_unsubcribed = $aRow['param_values'];
			}
			if ($aRow['settings_type'] == 'icon_size')
			{
				$icon_size = $aRow['param_values'];
			}
		}

        $aLocale = Phpfox_Locale::instance()->getLang();
		$sLanguageCode = $aLocale['language_code'];
		$lang_message = Phpfox::getLib('phpfox.database') -> select('*') -> from(Phpfox::getT('language_phrase'), 'p') -> where('p.language_id = "'.$sLanguageCode.'" AND p.var_name="default_invite_message_text"') -> execute('getRow');
		if(!$lang_message)
            $lang_message['text'] = _p('you_are_being_invited_to_join_our_social_network');
		$this -> template() -> assign(array(
			'sCallBackUrl' => phpfox::getParam('core.path') . 'module/contactimporter/static/tw.php',
			'lang_message' => $lang_message,
			'number_provider_display' => $number_provider_display,
			'is_unsubcribed' => $is_unsubcribed,
			'icon_size' => $icon_size
		));
		if ($this -> request() -> get('save_global_settings'))
		{
			$aVal = $this -> request() -> get('val');
			if (!is_numeric($aVal['number_provider_display']) || ((int)($aVal['number_provider_display']) != -1 && (int)($aVal['number_provider_display']) < 0))
			{
				$this -> url() -> send('admincp.contactimporter.settings', null, _p('maximum_of_providers_per_home_page_is_not_valid'));
			}
			$aGlobalSetting = array(
				'1' => array(
					'number_provider_display',
					(int)$aVal['number_provider_display']
				),
				'2' => array(
					'is_unsubcribed',
					(int)$aVal['is_unsubcribed']
				),
				'3' => array(
					'icon_size',
					(int)$aVal['icon_size']
				)
			);
			foreach ($aGlobalSetting as $iSettingId => $aRow)
			{
				$sql = " INSERT INTO " . Phpfox::getT('contactimporter_settings') . " (settings_id, settings_type, param_values)";
				$sql .= " VALUES (" . $iSettingId . ",'" . $aRow[0] . "','" . $aRow[1] . "')";
				$sql .= " ON DUPLICATE KEY UPDATE param_values = '" . $aRow[1] . "';";
				Phpfox::getLib('database') -> query($sql);
			}
			phpfox::getService('contactimporter') -> removeCache();
			$this -> url() -> send('admincp.contactimporter.settings', null, _p('global_settings_were_updated_successfully'));
		}

		$oCache = Phpfox::getLib('cache');
		if ($this -> request() -> get('save_message_invite'))
		{
			$default_message = $this -> request() -> get('default_message');
			if (empty($default_message))
			{
				Phpfox_Error::set(_p('no_message_input'));
			}
			else if(isset($lang_message['phrase_id']))
			{
				Phpfox::getLib('phpfox.database') -> update(Phpfox::getT('language_phrase'), array('text' => $default_message), 'phrase_id = ' . $lang_message['phrase_id']);
				$oCache -> remove();
				$this -> url() -> send('current', null, _p('update_default_message'));
			}
			else {
                Phpfox::getLib('phpfox.database') -> insert(Phpfox::getT('language_phrase'), array('text' => $default_message, 'language_id' => $sLanguageCode, 'product_id' => 'phpfox', 'var_name' => 'default_invite_message_text', 'text_default' => _p('you_are_being_invited_to_join_our_social_network'), 'added' => PHPFOX_TIME));
                $oCache -> remove();
                $this -> url() -> send('current', null, _p('update_default_message'));
            }
		}
		if ($this -> request() -> get('save_maximum_invitation'))
		{
			$vals = $this -> request() -> get('val');

			foreach ($vals as $key => $val)
			{
				if (!is_numeric($val))
				{
					$this -> url() -> send('current', null, _p('the_maximum_allowed_invitations_for_each_group_users_must_be_a_numberic'));
					return;
				}
				else
				if ($val < 0)
				{
					$this -> url() -> send('current', null, _p('the_maximum_allowed_invitations_for_each_group_users_must_be_more_than_zero'));
					return;
				}

				$id = str_replace('max_value_', '', $key);
				$max_in = Phpfox::getLib('phpfox.database') -> select('id_max_invitation') -> from(Phpfox::getT('user_group'), 'gr') -> leftjoin(Phpfox::getT('contactimporter_max_invitations'), 'm', 'gr.user_group_id=m.id_user_group') -> where('gr.user_group_id =' . $id) -> execute('getSlaveField');

				if ($val < 0)
					$val = 60;
				if ($max_in != null)
				{
					Phpfox::getLib('phpfox.database') -> update(Phpfox::getT('contactimporter_max_invitations'), array('number_invitation' => $val), 'id_max_invitation = ' . $max_in);
				}
				else
				{
					Phpfox::getLib('phpfox.database') -> insert(Phpfox::getT('contactimporter_max_invitations'), array(
						'id_user_group' => $id,
						'number_invitation' => $val
					));
				}
			}
			$maximum_invitations = Phpfox::getLib('phpfox.database') -> select('*') -> from(Phpfox::getT('user_group'), 'gr') -> leftjoin(Phpfox::getT('contactimporter_max_invitations'), 'm', 'gr.user_group_id=m.id_user_group') -> execute('getRows');
			$this -> url() -> send('current', null, _p('maximum_allowed_invitations_update'));
		}
		$maximum_invitations = Phpfox::getLib('phpfox.database') -> select('*') -> from(Phpfox::getT('user_group'), 'gr') -> leftjoin(Phpfox::getT('contactimporter_max_invitations'), 'm', 'gr.user_group_id=m.id_user_group') -> execute('getRows');
		$this -> template() -> assign(array('maximum_invitations' => $maximum_invitations));
		$this -> template()
            -> setBreadCrumb(_p('Apps'),$this->url()->makeUrl('admincp.apps'))
            -> setBreadCrumb(_p('module_contactimporter'),$this->url()->makeUrl('admincp.app',['id' => '__module_contactimporter']))
            -> setBreadCrumb(_p('global_settings'), $this -> url() -> makeUrl('admincp.contactimporter.settings'));

							
	}

}
?>