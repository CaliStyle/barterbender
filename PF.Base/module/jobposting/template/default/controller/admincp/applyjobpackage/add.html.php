<?php

defined('PHPFOX') or exit('NO DICE!');

?>
{$sCreateJs}
<form method="post" enctype="multipart/form-data" action="{url link='admincp.jobposting.applyjobpackage.add'}{if $bIsEdit }id_{$aForms.package_id}/ {/if}" id="js_add_package_form" name="js_add_package_form">
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                {if $bIsEdit}
                    {phrase var='edit_a_aj_package'}
                {else}
                    {phrase var='create_a_new_aj_package'}
                {/if}
            </div>
        </div>
        <div class="panel-body">
            <div class="form-group">
                <label>{required}{phrase var='aj_package_name'}:</label>
                <input title="{phrase var='aj_package_name'}" class="form-control" type="text" name="val[name]" id ="name" value="{value type='input' id='name'}">
            </div>
            <div class="form-group">
                <label>{required}{phrase var='apply_job_number'}:</label>
                <input title="{phrase var='apply_job_number'}" class="form-control" type="text" name="val[apply_number]" id ="name" value="{value type='input' id='apply_number'}">
            </div>
            <div class="form-group">
                <label>{required}{phrase var='valid_period'}:</label>
                <div class="row">
                    <div class="col-md-6">
                        <input title="{phrase var='valid_period'}" class="form-control" type="text" name="val[expire_number]" id ="name" value="{value type='input' id='expire_number'}">
                    </div>
                    <div class="col-md-6">
                        <select title="" name="val[expire_type]" class="form-control">
                            <option value="1" {value type="select" id="expire_type" default="1"}>{phrase var='day'}</option>
                            <option value="2" {value type="select" id="expire_type" default="2"}>{phrase var='week'}</option>
                            <option value="3" {value type="select" id="expire_type" default="3"}>{phrase var='month'}</option>
                            <option value="0" {value type="select" id="expire_type" default="0"}>{phrase var='never_expires'}</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label>{required}{phrase var='package_fee'}:</label>
                <input title="" class="form-control" type="text" name="val[fee]" id ="name" value="{value type='input' id='fee'}">
            </div>
        </div>
        <div class="panel-footer">
            <input type="submit" name="val[submit]" value="Save" class="btn btn-primary">
        </div>
    </div>
</form>
