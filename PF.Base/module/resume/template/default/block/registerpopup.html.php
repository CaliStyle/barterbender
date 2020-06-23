<div>
	{_p var='to_view_more_please_register_who_viewed_me_service'}
	<span style="margin-top: 5px;display:block;">
		<button onclick="tb_remove();$.ajaxCall('resume.upgradeAccount','view=0');return false;"
			   type="button" class="button btn btn-success btn-sm">{_p var='upgrade_to_view'}</button>
		<button onclick="tb_remove();return false;" type="button" class="button btn btn-default btn-sm">{_p var='cancel'}</button>
	</span>
</div>