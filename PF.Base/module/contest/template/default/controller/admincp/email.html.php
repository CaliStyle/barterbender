<?php 
/**
 * [PHPFOX_HEADER]
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<form method="post" id="frmEmailTemplate" action="{url link='admincp.contest.email'}" name="js_form">
    <div class="panel panel-default">
        <div class="panel-heading">
            {phrase var='contest.email_templates'}
        </div>
        <div class="panel-body">
            <div class="form-group">
                <label>{required}{phrase var='contest.email_templates_types'}:</label>
                <select name="val[type_id]" id="type_id" onchange="$.ajaxCall('contest.fillEmailTemplate', 'type_id=' + $(this).val());" class="form-control">
                    <option value="">{phrase var='contest.select'}:</option>
                    {foreach from=$aTemplateTypes item=aTemplateType}
                    <option value="{$aTemplateType.id}"> {$aTemplateType.phrase}</option>
                    {/foreach}
                </select>
            </div>

            <div class="form-group">
                <label>{phrase var='contest.subject'}:</label>
                <input type="text" name="val[subject]" value="{value type='input' id='subject'}" id="subject" maxlength="150" class="form-control" />
            </div>

            <div class="table form-group">
                <label>{phrase var='contest.content'}:</label>
                {editor id='content' rows='15'}
            </div>

            <div class="extra_info table">
                    {module name='contest.keyword-placeholder'}
            </div>
        </div>
        <div class="panel-footer">
            <input type="submit" value="{phrase var='contest.save_now'}" class="btn btn-primary" />
        </div>
    </div>
</form>

<script type="text/javascript">
	$('#type_id option').each(function() {l} 
		if($(this).val() == {$iCurrentTypeId})
		{l}
			$(this).attr('selected', 'selected');
		{r}
	{r});
</script>