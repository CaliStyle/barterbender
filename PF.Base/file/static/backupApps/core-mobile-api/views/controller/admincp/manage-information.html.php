<?php
defined('PHPFOX') or exit('NO DICE!');
?>

<div class="panel panel-default">
    <div class="panel-heading">
        <div class="panel-title">
            {_p var='Logo'}
        </div>
    </div>
    <form action="{url link='admincp.mobile.manage-information'}" method="post" enctype="multipart/form-data">
        <div class="panel-body">
            <div class="form-group">
                <label>{_p var='site_logo_on_mobile_application'}:</label>
                {if !empty($sLogo)}
                <div class="mt-2 mb-2 ml-1">
                    <input name="val[current_logo]" value="{$sLogo}" type="hidden"/>
                    <img src="{$sLogo}" alt="" width="64px">
                </div>
                    {if !$bIsDefault}
                        <div class="ml-1 mb-2">
                            <a href="{url link='admincp.mobile.manage-information' delete=true}" class="sJsConfirm">{_p var='remove_current_logo'}</a>
                        </div>
                    {/if}
                {/if}
                <input type="file" class="form-control" id="logo" name="logo"/>
                <div class="help-block">
                    {_p var='choose_logo_for_your_application_it_should_be_in_w_h_for_the_best_layout_system_will_choose_a_default_logo_if_you_not_upload_yours' w='64' h='56'}
                </div>
            </div>
        </div>
        <div class="panel-footer">
            <button name="val[submit]" class="btn btn-primary">{_p var='update'}</button>
        </div>
    </form>
</div>
