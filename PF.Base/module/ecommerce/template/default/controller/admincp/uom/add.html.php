<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<form method="post" action="{url link='admincp.ecommerce.uom.add'}">
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                {if $bIsEdit}
                    {phrase var='edit_uom'}
                {else}
                    {phrase var='add_uom'}
                {/if}
            </div>
        </div>

        <div class="panel-body">
            {if $bIsEdit}
                <div><input type="hidden" name="id" value="{$aForms.uom_id}" /></div>
                <div><input type="hidden" name="val[edit_id]" value="{$aForms.uom_id}" /></div>
                <div><input type="hidden" name="val[name]" value="{$aForms.title}" /></div>
            {/if}
            <div class="form-group">
                {field_language required=true phrase='title' label='title' field='name' format='val[name_' size=30 maxlength=100}
            </div>
        </div>

        <div class="panel-footer">
            <button type="submit" class="btn btn-primary">{phrase var='submit'}</button>
        </div>
    </div>
</form>