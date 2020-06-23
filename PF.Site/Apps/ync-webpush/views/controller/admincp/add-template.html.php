<?php
defined('PHPFOX') or exit('NO DICE!');
?>
{$sCreateJs}
<form action="{url link='current'}" enctype="multipart/form-data" method="post" id="ync_webpush_add_template" onsubmit="{$sGetJsForm}">
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                {$sTitle}
            </div>
        </div>
        <div class="panel-body">
            <input type="hidden" name="val[template_id]" value="{if $bIsEdit}{$iEditId}{/if}">
            <div class="form-group">
                <label for="">{required}{_p var='template_name'}</label>
                <input class="form-control" type="text" name="val[template_name]" value="{value id="template_name" type="input"}"/>
            </div>
            {template file='yncwebpush.block.admincp.add-template-info'}
        </div>
        <div class="panel-footer">
            <button name="val[submit]" class="btn btn-primary">{_p var='save__u'}</button>
            <a " class="btn btn-default" href="{url link='admincp.yncwebpush.manage-templates'}">{_p var='back'}</a>
        </div>
    </div>
</form>