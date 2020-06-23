<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');
/**
 * 
 * 
 * @copyright       [YOUNET_COPYRIGHT]
 * @author          YouNet Company
 * @package         YouNet_Event
 */
?>
{literal}
<script type="text/javascript">
    function viewTutorial(ele)
    {
        if(ele)
        {
            if($(ele).html() == oTranslations['fevent.view'])
            {
                $('#'+ele.rel).slideDown();
                $(ele).html(oTranslations['fevent.hide']);
            }
            else
            {
                $('#'+ele.rel).slideUp();
                $(ele).html(oTranslations['fevent.view']);
            }
        }
    }
</script>
<style type="text/css">
    div.tip{
        margin-top: 0px;
    }
    div.tip a, div.tip_tutorial a
    {
        color: blue;
    }
    div.tip_tutorial
    {
        padding-bottom: 4px;
        border-bottom: 1px solid #CFCFCF;
    }
    div.tip_tutorial ul
    {
        counter-reset: li;
    }
    div.tip_tutorial ul li
    {
        list-style: decimal-leading-zero outside none;
        margin: 0 0 0 26px;
        padding: 4px 0;
        position: relative;
    }
    div.tip_tutorial ul li ul li
    {
        list-style: disc outside none;
        margin: 0 0 0 26px;
        padding: 4px 0;
        position: relative;
    }
    div#tip_google_tutorial img{
        padding-top:5px;
    }
</style>
{/literal}
{$sCreateJs}
<form method="post" action="{url link='admincp.fevent.settinggapi'}" id="js_form" onsubmit="{$sGetJsForm}">
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
    	        {_p var='google_api_details'}
            </div>
        </div>
    <div class="panel-body">
        <div class="extra_info">{_p var='in_google_api_settings_you_must_change_redirect_uri_to'} <span style="color:blue;">{$sRedirectUri}</span></div>
        <div class="extra_info" id="tip_google"><a href="javascript:void(0)" onclick="viewTutorial(this);" rel="tip_google_tutorial">{_p var='view'}</a> {_p var='tutorial_how_to_register_google_api'}</div>
        <div id="tip_google_tutorial" class="tip_tutorial" style="display: none">
            <ul>
                <li>{_p var='go_to_google_apis_console'}</li>
                <li>{_p var='create_a_project_new' sCorePath=$sCorePath}</li>
                <li>{_p var='active_calendar_api_service_new' sCorePath=$sCorePath}</li>
                <li>{_p var='create_an_oauth_client_id_new' sCorePath=$sCorePath RedirectUri=$sRedirectUri CoreHost=$sCoreHost}</li>
                <li>{_p var='get_google_api_detail'}</li>
            </ul>
        </div>
        <div class="form-group">
            <label for="client_id">
                {required}{_p var='client_id'}:
            </label>
            <input class="form-control" type="text" id="oauth2_client_id" name="val[oauth2_client_id]" value="{value type='input' id='oauth2_client_id'}" size="60" />
        </div>
        <div class="form-group">
            <label for="client_secret">
                {required}{_p var='client_secret'}:
            </label>
            <input class="form-control" type="text" id="oauth2_client_secret" name="val[oauth2_client_secret]" value="{value type='input' id='oauth2_client_secret'}" size="60" />
        </div>
        <div class="form-group">
            <label for="api_key">
                {required}{_p var='api_key'}:
            </label>
            <input class="form-control" type="text" id="developer_key" name="val[developer_key]" value="{value type='input' id='developer_key'}" size="60" />
        </div>
    </div>
    <div class="panel-footer">
        <input type="submit" value="{_p var='submit'}" class="btn btn-primary" />
    </div>
    </div>
</form>