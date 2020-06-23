{literal}
<style type="text/css">
	.table1{
		line-height: 20px;
		margin-bottom: 4px;
	}
	.table_left1{
		float: left;
		text-align: center;
	}
	.table_right1{
		margin-left: 100px;
	}
</style>
{/literal}

<form id="core_js_resume" method="post">
	<div class="error_message" style="display:none;"></div>
	<input type="hidden" name='val[user_id]' value="{$user_id}"/>
	<input type="hidden" name='val[resume_id]' value="{$resume_id}"/>
	<input type="hidden" name='val[type]' value="{$type}"/>
<div class="table1 form-group">
	<div class="table_left1">
		{_p var='title'}
	</div>
	<div class="table_right1">
		<input type="text" class="form-control" size="31" name="val[title]"/>
	</div>
</div>

<div class="table1 form-group">
	<div class="table_left1">
		{_p var='your_message'}
	</div>
	<div class="table_right1">
		<textarea class="form-control" cols="30" rows="5" name="val[message]"></textarea>
	</div>
</div>


<div class="table1 form-group">
	<div class="table_left1">
		
	</div>
	<div class="table_right1">
		<button type="button" value="" class="button btn btn-primary btn-sm" id="btnSend">{_p var='send'}</button>
	</div>
</div>
</form>
{literal}
<script type="text/javascript">
	$Behavior.onLoadResumePopUp = function()
	{
		$('#btnSend').click(function(){
			$(this).addClass('disabled').attr('disabled','disabled');
			$("#core_js_resume").ajaxCall('resume.sendMessage');
		});
	}
	$Core.loadInit();
</script>
{/literal}
