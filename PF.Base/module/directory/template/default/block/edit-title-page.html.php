<form method="post" action="" id="js_edit_title_page">
	<input type="hidden" name="page_business_id" id="page_business_id" value="{$page_business_id}">
	<div class="form-group mb-0">
		<label>{phrase var='current_title'}:</label>
		{$aPage.module_phrase}
	</div>
	<div class="form-group">
		<label>{phrase var='new_title'}</label>
		<div class="table_right"><input class="form-control" type="text" name="new_title" id="new_title" value=""></div>
	</div>
	<div class="yndirectory-button">
		<button type="button" class="btn btn-sm btn-primary dont-unbind" name="update_title" id="update_title" value="{phrase var='save_changes'}">{phrase var='save_changes'}</button>
	</div>
	<div class="yndirectory-message" id='message'></div>
</form>
{literal}
	<script type="text/javascript">
	;
	$('#update_title').click(function(){
		$.ajaxCall('directory.updateTitlePage',$('#js_edit_title_page').serialize(), 'post');
		if($('#js_edit_title_page #new_title').val() != '' ){
		 	js_box_remove(this);
		}
		return false;
	})
	;
	</script>
{/literal}