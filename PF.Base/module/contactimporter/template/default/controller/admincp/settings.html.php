<?php
/**
 * [PHPFOX_HEADER]
 *
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author
 * @package  		Module_Contactimporter
 * @version
 */

defined('PHPFOX') or exit('NO DICE!');

?>
<form method="post" action="{url link='admincp.contactimporter.settings'}" id="admincp_contactimporter_form_message">
<input type="hidden" name="action" value="global_settings"/>
    <div class="message" style="padding:10px;">
        {_p var='all_social_api_keys_configuration_was_setup_in'}<br/><br/>
        <a href="{url link='admincp.socialbridge.providers'}" target="_blank">{url link='admincp.socialbridge.providers'}</a>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                {_p var='global_settings'}
            </div>
        </div>
        <div class="panel-body">
            <div class="form-group">
                <label>
                    {_p var='maximum_number_of_providers_per_home_page'}:
                </label>
                <input class="form-control" type="text" name="val[number_provider_display]" value="{$number_provider_display}" />
                <div class="extra_info">
                     {_p var='if_you_put_1_that_mean_all_of_providers_will_display_on_home_page'}
                </div>
            </div>
            <div class="form-group">
                <label>
                    {_p var='icon_size_on_homepage'}:
                </label>
                <select name="val[icon_size]" class="form-control">
                    <option value="30" {if $icon_size==30}selected="selected"{/if} />30</option>
                    <option value="35" {if $icon_size==35}selected="selected"{/if} />35</option>
                    <option value="40" {if $icon_size==40}selected="selected"{/if} />40</option>
                    <option value="45" {if $icon_size==45}selected="selected"{/if} />45</option>
                    <option value="50" {if $icon_size==50}selected="selected"{/if} />50</option>
                </select>
            </div>
            <div class="form-group" style="display:none;">
                <label>
                    {required}{_p var='unsubcribe'}
                </label>
                <div class="item_is_active_holder">
                    <span class="js_item_active item_is_active"><input type="radio" name="val[is_unsubcribed]" value="1" {if $is_unsubcribed eq 1 } {value type='radio' id='is_active' default='1' selected='true'}{/if}/> {phrase var='admincp.yes'}</span>
                    <span class="js_item_active item_is_not_active"><input type="radio" name="val[is_unsubcribed]" value="0" {if $is_unsubcribed eq 0 } {value type='radio' id='is_active' default='0' selected='true'}{/if}/> {phrase var='admincp.no'}</span>
                </div>
            </div>
        </div>
        <div class="panel-footer">
            <input type="submit" value="{_p var='save_change'}" class="btn btn-primary" name="save_global_settings"/>
        </div>
    </div>
</form>

<form method="post" action="{url link='admincp.contactimporter.settings'}" id="admincp_contactimporter_form_message">
    <div class="panel panel-default">
        <input type="hidden" name="action" value="add"/>
        <div class="panel-heading">
            <div class="panel-title">
                {_p var='default_invite_message'}
            </div>
        </div>
        <div class="panel-body">
            <div class="form-group">
                <label>
                    {_p var='message_label'}
                </label>
                <textarea class="form-control" id="default_message" name="default_message" rows="5" cols="40">{if isset($lang_message.text)} {$lang_message.text} {/if}</textarea>
            </div>
        </div>
        <div class="panel-footer">
            <input type="submit" value="{_p var='save_change'}" class="btn btn-primary" name="save_message_invite"/>
        </div>
    </div>
</form>
<br />
