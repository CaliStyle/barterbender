<?php 
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<form method="post" action="{url link='admincp.videochannel.add'}">
{if $bIsEdit}
	<div><input type="hidden" name="id" value="{$aForms.category_id}" /></div>
    <div><input type="hidden" name="val[name]" value="{$aForms.name}" /></div>
{/if}

    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                {phrase var='videochannel.video_category_details'}
            </div>
        </div>
        <div class="panel-body">
            <div class="form-group">
                <label for="">{phrase var='videochannel.parent_category'}:</label>
                {$selectBox}
            </div>
            {field_language phrase='name' required=true label='name' field='name' format='val[name_' size=30 maxlength=255}
        </div>

        <div class="panel-footer">
            <input type="submit" value="{phrase var='videochannel.submit'}" class="btn btn-primary" />
        </div>
    </div>
</form>