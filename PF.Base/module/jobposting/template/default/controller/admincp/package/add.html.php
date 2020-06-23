<?php

defined('PHPFOX') or exit('NO DICE!');

?>

{$sCreateJs}
<form method="post" enctype="multipart/form-data" action="{url link='admincp.jobposting.package.add'}" id="js_add_package_form" name="js_add_package_form">
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                {if $bIsEdit}
                    {phrase var='edit_a_package'}
                {else}
                    {phrase var='add_new_package'}
                {/if}
            </div>
        </div>

        {if $bIsEdit}
        <input type="hidden" name="id" value="{$aForms.package_id}">
        {/if}

        <div class="panel-body">
            <div class="form-group">
                <label>{required}{phrase var='job_posting_package_name'}</label>
                <input title="{phrase var='job_posting_package_name'}" class="form-control" type="text" name="val[name]" id ="name" value="{value type='input' id='name'}">
            </div>
            <div class="form-group">
                <label>{required}{phrase var='post_job_number'}</label>
                <input title="{phrase var='post_job_number'}" class="form-control" type="text" name="val[post_number]" id ="name" value="{value type='input' id='post_number'}">
            </div>
            <div class="form-group">
                <label>{required}{phrase var='valid_period'}</label>
                <div class="row">
                    <div class="col-md-6">
                        <input title="{phrase var='valid_period'}" class="form-control" type="text" name="val[expire_number]" id ="name" value="{value type='input' id='expire_number'}">
                    </div>
                    <div class="col-md-6">
                        <select name="val[expire_type]" class="form-control" title="{phrase var='valid_period'}">
                            <option value="1" {if isset($aForms.expire_type) && $aForms.expire_type==1}selected{/if}>{phrase var='day'}</option>
                            <option value="2" {if isset($aForms.expire_type) && $aForms.expire_type==2}selected{/if}>{phrase var='week'}</option>
                            <option value="3" {if isset($aForms.expire_type) && $aForms.expire_type==3}selected{/if}>{phrase var='month'}</option>
                            <option value="0" {if isset($aForms.expire_type) && $aForms.expire_type==0}selected{/if}>{phrase var='never_expires'}</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label>{required}{phrase var='package_fee'}</label>
                <input title="{phrase var='package_fee'}" class="form-control" type="text" name="val[fee]" id ="name" value="{value type='input' id='fee'}">
            </div>
        </div>
        <div class="panel-footer">
            <input type="submit" name="val[submit]" value="Save" class="btn btn-primary">
        </div>
    </div>
</form>