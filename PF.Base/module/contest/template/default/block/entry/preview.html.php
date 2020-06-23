{literal}
	<style type="text/css">
		.js_box_title {
			background: #fff;
			color: #333;
			border-bottom: 1px solid #d7d7d7;
			padding-left: 15px;
		}
		.js_box_content {
			padding-top: 10px;
		}
	</style>
{/literal}
<div class="wrap_preview">
	<div class="table_title">
		<div class="table_left">
			{$aEntryParam.sTitle}
		</div>
	</div>
	<div  class="table_content">
		{module name=$sTemplateViewPath aYnEntry=$aYnEntry bIsPreview=true}
	</div> 
	<div class="table_description">
		<h5><span>{phrase var='contest.description'}</span></h5>
		<div class="table_left">
            {$aEntryParam.sSummary|parse|shorten:'550':'comment.view_more':true|split:550}
		</div>
	</div>
</div>
