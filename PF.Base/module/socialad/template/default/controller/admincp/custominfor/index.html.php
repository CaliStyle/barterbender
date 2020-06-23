
{foreach from=$aCustoms item=aCustom}
<div class='panel panel-default js_ynsa_custom_holder'>
	<div class="panel-heading">
        <div class="panel-title">
		    {$aCustom.phrase}
        </div>
	</div>

	<div class="js_ynsa_view_custom_info">
		<div class="js_ynsaContent panel-body">
			{if $aCustom.content_parsed} 
				{$aCustom.content_parsed|parse}
			{else}
				{_p var='section_no_content'}
			{/if}
		</div>
        <div class="panel-footer">
            <input type="button" value="{_p var='edit'}" class="btn btn-primary" onclick="ynsa_showEditForm($(this));"/>
        </div>
	</div>
	<div class="ynsaCustominforEdit js_ynsa_edit_term_custom_info" style="display:none">
		<form method="post" action="{url link='admincp.socialad.custominfor.index'}">
			<div class="form-group panel-body">
				<input type="hidden" name="val[custominfor_type_id]" value="{$aCustom.type_id}" />
				<!-- package description -->
                <textarea style="display: none;" class="js_temp_content">{$aCustom.content}</textarea>
				<textarea class="form-control js_edit_custom_input" name="val[content]" cols="60" rows="8" id="{$aCustom.type_id}" style="width:90%;">{$aCustom.content}</textarea>
			</div>
            <div class="panel-footer">
                <input type="submit" value="{_p var='save'}" class="btn btn-primary" name="val[save]" />
                <input type="button" value="{_p var='cancel'}" class="btn btn-default" onclick="ynsa_hideEditForm($(this));"/>
            </div>
		</form>
	</div>
</div> <!-- end custom infor section -->
{/foreach}

{literal}
<script type="text/javascript">
    function ynsa_showEditForm(ele) {
        ele.closest('.js_ynsa_custom_holder').find('.js_ynsa_edit_term_custom_info').show();
        ele.closest('.js_ynsa_view_custom_info').hide();
    }
    function ynsa_hideEditForm(ele) {
        var parent = ele.closest('.js_ynsa_edit_term_custom_info'),
            content = parent.find('.js_temp_content').val();
        parent.hide();
        parent.siblings('.js_ynsa_view_custom_info').show();
        parent.find('.js_edit_custom_input').val(content);
    }
</script>
{/literal}
