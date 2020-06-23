<?php

defined('PHPFOX') or exit('NO DICE!');

?>
<div id="ajax-response-custom">
</div>
<form method="post" action="" onsubmit="return onSubmitValid(this);">
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                {_p('ultimate_videos_category_detail')}
            </div>
        </div>

        <div class="panel-body">
            {if $bIsEdit}
                <div><input type="hidden" name="id" value="{$aForms.category_id}"/></div>
            {/if}
            <div class="form-group">
                <label for="">{_p('parent_category')}:</label>
                {$selectBox}
            </div>

            <div class="form-group">
                {field_language phrase='name' label='Title' field='name' format='val[name_' size=30 maxlength=255 required=true}
            </div>

        </div>

        <div class="panel-footer">
            <input type="submit" value="{_p('Submit')}" class="btn btn-primary"/>
        </div>
    </div>
</form>
{literal}
<script type="text/javascript">
    var MissingAddName = "{/literal}{_p('provide_a_category_name')}{literal}";

    function onSubmitValid(obj) {
        $.ajaxCall('ultimatevideo.AdminAddCategory', $(obj).serialize(), 'post');
        return false;
    }
</script>
{/literal}