<upgrade>
	<settings>
		<setting>
			<group />
			<module_id>core</module_id>
			<is_hidden>0</is_hidden>
			<type>boolean</type>
			<var_name>use_md5_for_file_names</var_name>
			<phrase_var_name>setting_use_md5_for_file_names</phrase_var_name>
			<ordering>1</ordering>
			<version_id>3.2.0rc1</version_id>
			<value>1</value>
		</setting>
	</settings>
	<hooks>
		<hook>
			<module_id>core</module_id>
			<hook_type>controller</hook_type>
			<module>core</module>
			<call_name>core.component_controller_admincp_stat_clean</call_name>
			<added>1335951260</added>
			<version_id>3.2.0</version_id>
			<value />
		</hook>
	</hooks>
</upgrade>