<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');
/**
 *
 *
 * @copyright        [YOUNET_COPYRIGHT]
 * @author          YouNet Company
 * @package         YouNet_Document
 */
defined('PHPFOX') or exit('NO DICE!');

?>
{$sCreateJs}
{if $error_message != ""}
<div class="error_message"> {$error_message}</div>{/if}
<form method="post" enctype="multipart/form-data" action="{url link='current'}" id="js_form"
      onsubmit="{$sGetJsForm}">
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                {phrase var='license_details'}
            </div>
        </div>
        <div class="panel-body">
            {if isset($aForms.license_id)}
            <div><input type="hidden" name="val[id]" value="{$aForms.license_id}"/></div>
            {/if}
            <div class="form-group">
                <label for="license_name">{required}{phrase var='license_name'}:</label>
                <input class="form-control" type="text" name="val[name]" value="{value type='input' id='license_name'}" id="license_name"
                       size="30">
            </div>
            <div class="form-group">
                <label for="">{required}{phrase var='license_icon'}:</label>
                <input class="form-control" type="hidden" id="max_file_size" name="MAX_FILE_SIZE"
                       value="{$max_file_size}">
                <input class="form-control" name="uploadedfile" id="uploadedfile" type="file">
                <p class="help-block">
                    {_p var='the_file_size_limit_is_filesize_if_your_upload_does_not_work_try_uploading_a_smaller_picture' filesize='500 Kb'}
                </p>
            </div>
            <div class="form-group">
                <label for="reference_url">{phrase var='reference_link'}:</label>
                <input class="form-control" type="text" name="val[reference_url]" value="{value type='input' id='reference_url'}"
                       id="reference_url" size="60">
            </div>
        </div>
        <div class="panel-footer">
            <button type="submit" class="btn btn-primary">{phrase var='admincp.submit'}</button>
        </div>
    </div>
</form>